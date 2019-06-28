<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_contact_form.inc.php,v 1.2 2016-06-29 08:34:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/contact_form/contact_form.class.php");
require_once($class_path."/encoding_normalize.class.php");
switch($sub){
	case 'form':
		switch ($action){
			case 'send_mail':
				$contact_form = new contact_form();
				$contact_form->set_form_fields(json_decode(stripslashes($form_fields)));
				if($contact_form->check_form()) {
					$contact_form->send_mail();
				}
				print encoding_normalize::json_encode(array('sended' => $contact_form->is_sended(), 'messages' => $contact_form->get_messages()));
				break;
		}
		break;
}
?>