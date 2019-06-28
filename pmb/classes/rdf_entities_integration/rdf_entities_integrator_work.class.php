<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rdf_entities_integrator_work.class.php,v 1.4 2017-05-18 09:38:27 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/rdf_entities_integration/rdf_entities_integrator_authority.class.php');
require_once($class_path.'/titre_uniforme.class.php');

class rdf_entities_integrator_work extends rdf_entities_integrator_authority {
	
	protected $table_name = 'titres_uniformes';
	
	protected $table_key = 'tu_id';
	
	protected $ppersos_prefix = 'tu';
	
	protected function init_map_fields() {
		$this->map_fields = array_merge(parent::init_map_fields(), array(
				'http://www.pmbservices.fr/ontology#date' => 'tu_date',
				'http://www.pmbservices.fr/ontology#label' => 'tu_name',
				'http://www.pmbservices.fr/ontology#work_type' => 'tu_oeuvre_type',
				'http://www.pmbservices.fr/ontology#work_nature' => 'tu_oeuvre_nature',
				'http://www.pmbservices.fr/ontology#shape' => 'tu_forme',
				'http://www.pmbservices.fr/ontology#has_shape' => 'tu_forme_marclist',
				'http://www.pmbservices.fr/ontology#place' => 'tu_lieu',
				'http://www.pmbservices.fr/ontology#subject' => 'tu_sujet',
				'http://www.pmbservices.fr/ontology#intended_termination' => 'tu_completude',
				'http://www.pmbservices.fr/ontology#targeted_audience' => 'tu_public',
				'http://www.pmbservices.fr/ontology#story' => 'tu_histoire',
				'http://www.pmbservices.fr/ontology#context' => 'tu_contexte',
				'http://www.pmbservices.fr/ontology#digital_reference',
				'http://www.pmbservices.fr/ontology#tone' => 'tu_tonalite',
				'http://www.pmbservices.fr/ontology#has_tone' => 'tu_tonalite_marclist',
				'http://www.pmbservices.fr/ontology#coord' => 'tu_coordonnees',
				'http://www.pmbservices.fr/ontology#equinox' => 'tu_equinoxe',
				'http://www.pmbservices.fr/ontology#other_feature' => 'tu_caracteristique'
		));
		return $this->map_fields;
	}
	
	protected function init_foreign_fields() {
		$this->foreign_fields = array_merge(parent::init_foreign_fields(), array(
		));
		return $this->foreign_fields;
	}
	
	protected function init_linked_entities() {
		$this->linked_entities = array_merge(parent::init_linked_entities(), array(
				'http://www.pmbservices.fr/ontology#has_concept' => array(
						'table' => 'index_concept',
						'reference_field_name' => 'num_object',
						'external_field_name' => 'num_concept',
						'other_fields' => array(
								'type_object' => TYPE_TITRE_UNIFORME
						)
				),
				'http://www.pmbservices.fr/ontology#expression_of' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '1',
								'oeuvre_link_other_link' => '0'
						)
				),
				'http://www.pmbservices.fr/ontology#has_expression' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_to',
						'external_field_name' => 'oeuvre_link_from',
						'other_fields' => array(
								'oeuvre_link_expression' => '1',
								'oeuvre_link_other_link' => '0'
						)
				),
				'http://www.pmbservices.fr/ontology#has_other_link' => array(
						'table' => 'tu_oeuvres_links',
						'reference_field_name' => 'oeuvre_link_from',
						'external_field_name' => 'oeuvre_link_to',
						'other_fields' => array(
								'oeuvre_link_expression' => '0',
								'oeuvre_link_other_link' => '1'
						)
				),
				'http://www.pmbservices.fr/ontology#has_event' => array(
						'table' => 'tu_oeuvres_events',
						'reference_field_name' => 'oeuvre_event_tu_num',
						'external_field_name' => 'oeuvre_event_authperso_authority_num'
				),
				'http://www.pmbservices.fr/ontology#music_distribution' => array(
						'table' => 'tu_distrib',
						'reference_field_name' => 'distrib_num_tu',
						'external_field_name' => 'distrib_name'
				),
				'http://www.pmbservices.fr/ontology#subdivision_shape' => array(
						'table' => 'tu_subdiv',
						'reference_field_name' => 'subdiv_num_tu',
						'external_field_name' => 'subdiv_name'
				),
		));
		return $this->linked_entities;
	}
	
	protected function init_special_fields() {
		$this->special_fields = array_merge(parent::init_special_fields(), array(
				'http://www.pmbservices.fr/ontology#has_responsability_author',
				'http://www.pmbservices.fr/ontology#has_responsability_performer',
		));
		return $this->special_fields;
	}
	
	protected function post_create($uri) {
		// Audit
		if ($this->integration_type && $this->entity_id) {
			$query = 'insert into audit (type_obj, object_id, user_id, type_modif, info, type_user) ';
			$query.= 'values ("'.AUDIT_TITRE_UNIFORME.'", "'.$this->entity_id.'", "'.$this->contributor_id.'", "'.$this->integration_type.'", "'.addslashes(json_encode(array("uri" => $uri))).'", "'.$this->contributor_type.'")';
			pmb_mysql_query($query);
		}
		if ($this->entity_id) {
			// Indexation
			titre_uniforme::update_index($this->entity_id);
		}
	}
}