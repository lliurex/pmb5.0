<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator.class.php,v 1.11.2.1 2017-09-14 14:45:29 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto_parametres_perso.class.php');

class rdf_entities_integrator {
	/**
	 * Table de r�f�rence de l'entit� � int�gr�
	 * @var string
	 */
	protected $table_name;
	
	/**
	 * Cl� primaire de la table de r�f�rence de l'entit� � int�grer
	 * @var int $table_key
	 */
	protected $table_key;
	
	/**
	 * Store RDF associ�
	 * @var rdf_entities_store
	 */
	protected $store;
	
	/**
	 * Tableau associatif champ table SQL / propri�t� classe RDF
	 * @var array
	 */
	protected $map_fields;
	
	/**
	 * Tableau associatif des champs de cl� �trang�re SQL / propri�t� RDF
	 * @var array
	 */
	protected $foreign_fields;
	
	/**
	 * Tableau contenant les entit�s et leurs tables de liaison
	 * @var array
	 */
	protected $linked_entities;
	
	/**
	 * Association champ / callable
	 * @var array
	 */
	protected $special_fields;
	
	/**
	 * Tableau des �l�ments permettant de construire la requ�te de base (dans la table $this->table)
	 * @var array
	 */
	protected $base_query_elements;
	
	/**
	 * Identifiant de l'entit� en cours d'int�gration
	 * @var int
	 */
	protected $entity_id;
	
	/**
	 * Tableau des �l�ments permettant de construire les requ�tes dans les tables de liaisons
	 * @var array [{{ table }} => ['reference_field_name' => {{ reference_field_name }}, 'external_field_name' => {{ external_field_name }}, 'external_field_values' => [{{ external_field_value_1 }}, {{ external_field_value_2 }}]]] 
	 */
	protected $linked_queries_elements;
	
	/**
	 * Type d'int�gration : 1 : INSERTION, 2 : MODIFICATION, 3 : MIGRATION
	 * @var string
	 */
	protected $integration_type;
	
	/**
	 * Identifiant du contributeur
	 * @var int
	 */
	protected $contributor_id;
	
	/**
	 * Type de contributeur : 0 : Utilisateur gestion, 1 : Lecteur OPAC
	 * @var int
	 */
	protected $contributor_type;
	
	/**
	 * Tableau des callables pour les champs sp�ciaux (responsabilit�, notices li�es...)
	 * @var array
	 */
	protected $special_callables;
	
	/**
	 * Prefixe associ� aux champs persos de l'entit�
	 * @var string
	 */
	protected $ppersos_prefix;
	
	/**
	 * Tableau des donn�es de l'entit� � renvoyer
	 * @var array
	 */
	protected $entity_data;
		
	/**
	 * Constructeur
	 * @param rdf_entities_store $store Store rdf � utiliser
	 */
	public function __construct($store) {
		$this->store = $store;
		$this->init_foreign_fields();
		$this->init_linked_entities();
		$this->init_map_fields();
		$this->init_special_fields();
	}
	
	protected function init_map_fields() {
		$this->map_fields = array();
		return $this->map_fields;
	}

	protected function init_foreign_fields() {
		$this->foreign_fields = array();
		return $this->foreign_fields;
	}
	
	protected function init_linked_entities() {
		$this->linked_entities = array();
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array();
		$this->special_callables = array();
		return $this->special_fields;
	}
	
	/**
	 * Initialise le tableau des �l�ments � ins�rer dans la table de r�f�rence
	 * Permet de d�finir des valeurs par d�faut
	 */
	protected function init_base_query_elements() {
		$this->base_query_elements = array();
		return $this->base_query_elements;
	}
	
	/**
	 * Int�gre une entit� dont on ne connait pas le type dans PMB
	 * @param string $uri URI de l'entit� dans le store
	 */
	public function integrate_entity($uri) {
		if (!$uri) {
			return array();
		}
		$type_property = $this->store->get_property($uri, 'rdf:type');
		$entity_integrator = $this->get_entity_integrator_from_type_uri($type_property[0]['value']);
		if ($entity_integrator) {
			return $entity_integrator->integrate_itself($uri);
		}
		return array();
	}
	
	/**
	 * Int�gration d'une entit� du type de cette classe
	 * @param string $uri URI de l'entit�
	 */
	public function integrate_itself($uri) {
		if (!$uri) {
			return array();
		}
		$this->entity_data = array(
				'uri' => $uri,
				'children' => array()
		);
		$this->integration_type = 0;
		
		$this->get_entity_id($uri);
		$this->get_contributor_id($uri);
		
		$this->init_base_query_elements();
		$this->linked_queries_elements = array();
		foreach ($this->store->get_properties($uri) as $property_uri => $values) {
			$this->handle_property($property_uri, $values);
		}
		$this->execute_base_query();
		$this->execute_linked_queries();
		if ($this->ppersos_prefix) {
			$onto_parametres_perso = new onto_parametres_perso($this->ppersos_prefix);
			$integrated_children = $onto_parametres_perso->rec_fields_perso_with_integrator($this, $uri, $this->entity_id);
			if (count($integrated_children)) {
				$this->entity_data['children'] = array_merge($this->entity_data['children'], $integrated_children);
			}
		}

		foreach ($this->special_callables as $callable) {
			call_user_func_array($callable["method"], $callable["arguments"]);
		}
		
		$this->post_create($uri);
		$this->entity_data['id'] = $this->entity_id;
		return $this->entity_data;
	}
	
	/**
	 * Traite la propri�t�
	 * @param string $property_uri URI de la propri�t�
	 * @param array $values Valeurs associ�es
	 */
	protected function handle_property($property_uri, $values) {
		if (isset($this->map_fields[$property_uri])) {
			$this->base_query_elements[$this->map_fields[$property_uri]] = $values[0]['value'];
		}
		if (isset($this->foreign_fields[$property_uri])) {
			if ($values[0]['type'] === 'uri') {
				$integrated_entity = $this->integrate_entity($values[0]['value']);
				$this->entity_data['children'][] = $integrated_entity;
				if ($integrated_entity['id']) {
					$this->base_query_elements[$this->foreign_fields[$property_uri]] = $integrated_entity['id'];
				}
			}
		}
		if (isset($this->linked_entities[$property_uri])) {
			foreach ($values as $value) {
				$external_field_value = $value['value'];
				if ($value['type'] === 'uri') {
					$integrated_entity = $this->integrate_entity($value['value']);
					$this->entity_data['children'][] = $integrated_entity;
					$external_field_value = $integrated_entity['id']; 
				}
				if ($value['type'] === 'literal') {
					$external_field_value = $value['value'];
				}
				if ($external_field_value) {
					$this->add_linked_queries_element($this->linked_entities[$property_uri], $external_field_value);
				}
			}
		}
				
		if (isset($this->special_fields[$property_uri])) {
			$arguments = $this->special_fields[$property_uri]["arguments"];
			$arguments[] = $values;
			$this->special_callables[] = array('method' => $this->special_fields[$property_uri]["method"], 'arguments' => $arguments);
		}
	}
	
	/**
	 * Execute la requ�te d'insertion ou de modification dans la table de r�f�rence de l'entit�
	 * @return int Identifiant de l'entit� ins�r�e ou modifi�e
	 */
	protected function execute_base_query() {
		if (!count($this->base_query_elements) || !$this->table_name || !$this->table_key) {
			return $this->entity_id;
		}
		$this->integration_type = 1;
		$query = 'insert into '.$this->table_name.' set ';
		$query_clause = '';
		if ($this->entity_id) {
			$this->integration_type = 2;
			$query = 'update '.$this->table_name.' set ';
			$query_clause = ' where '.$this->table_key.' = '.$this->entity_id;
		}
		$first_value = true;
		
		
		foreach ($this->base_query_elements as $base_query_field => $base_query_value) {
			if (!$first_value) {
				$query.= ', ';
			}
			$query.= $base_query_field.' = "'.addslashes(pmb_utf8_array_decode($base_query_value)).'"';
			$first_value = false;
		}
		
		pmb_mysql_query($query.$query_clause);
		if (!$this->entity_id) {
			$this->entity_id = pmb_mysql_insert_id();
		}
		return $this->entity_id;
	}
	
	/**
	 * Retourne la classe d'int�gration associ� au type d'entit�
	 * @param string $uri URI du type d'entit�
	 */
	protected function get_entity_integrator_from_type_uri($type_uri) {
		switch ($type_uri) {
			default :
				$type = substr($type_uri, strpos($type_uri, '#') + 1);
				$integrator_class = 'rdf_entities_integrator_'.$type;
				if (class_exists($integrator_class)) {
					return new $integrator_class($this->store);
				}
				return null;
		}
	}
	
	/**
	 * Retourne l'identifiant de l'entit� en cours d'int�gration
	 */
	protected function get_entity_id($uri) {
		$identifier_property = $this->store->get_property($uri, 'pmb:identifier');
		if (!empty($identifier_property[0])) {
			$this->entity_id = $identifier_property[0]['value']*1;
		}
		return $this->entity_id;
	}
	
	protected function execute_linked_queries() {
		if (!count($this->linked_queries_elements) || !$this->entity_id) {
			return null;
		}
		foreach ($this->linked_queries_elements as $table => $query_elements) {
			// On commence par supprimer les anciens enregistrements
			$query = 'delete from '.$table.' where '.$query_elements['reference_field_name'].' = '.$this->entity_id;
			pmb_mysql_query($query);
			
			// On ins�re tout ce qu'il y a
			$query = 'insert into '.$table.' ('.$query_elements['reference_field_name'].', '.$query_elements['external_field_name'];
			$other_values = '';
			if (is_array($query_elements['other_fields'])) {
				foreach ($query_elements['other_fields'] as $other_field => $other_value) {
					$query.= ', '.$other_field;
					$other_values = ', "'.addslashes($other_value).'"';
				}
			}
			$query.= ') values ';
			$first_value = true;
			foreach ($query_elements['external_field_values'] as $value) {
				if (!$first_value) {
					$query.= ', ';
				}
				$query.= '('.$this->entity_id.', "'.addslashes($value).'"'.$other_values.')';
				$first_value = false;
			}
			//var_dump($query);
			
			pmb_mysql_query($query);
		}
	}
	
	/**
	 * Ajoute un �l�ment pour les requ�tes dans les tables de liaison
	 * @param array $linked_query_data Donn�es relatives � la table de liaison et aux champs � utiliser
	 * @param string $external_field_value Valeur � ins�rer
	 */
	protected function add_linked_queries_element($linked_query_data, $external_field_value) {
		if (!$external_field_value) {
			return null;
		}
		if (!isset($this->linked_queries_elements[$linked_query_data['table']])) {
			$this->linked_queries_elements[$linked_query_data['table']] = array(
					'reference_field_name' => $linked_query_data['reference_field_name'],
					'external_field_name' => $linked_query_data['external_field_name'],
					'other_fields' => $linked_query_data['other_fields'],
					'external_field_values' => array()
			);
		}
		$this->linked_queries_elements[$linked_query_data['table']]['external_field_values'][] = $external_field_value;
	}
	
	/**
	 * Action � effectuer apr�s l'insertion en base
	 */
	protected function post_create($uri) {
		// A d�river
	}
	
	protected function get_contributor_id($uri) {
		$this->contributor_id = 0;
		$this->contributor_type = 0;
		$contributor_property = $this->store->get_property($uri, 'pmb:has_contributor');
		if (!empty($contributor_property[0]['value'])) {
			if ($contributor_property[0]['value']*1) {
				$this->contributor_id = $contributor_property[0]['value']*1;
				$this->contributor_type = 1;
			}
		}
		return $this->contributor_id;
	}
	
	public function get_store() {
		return $this->store;
	}
}