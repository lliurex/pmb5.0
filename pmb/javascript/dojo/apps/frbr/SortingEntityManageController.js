// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SortingEntityManageController.js,v 1.1 2017-04-11 11:46:20 dgoron Exp $

define(['dojo/_base/declare',
        'apps/frbr/EntityManageController'
], function(declare, EntityManageController) {
	return declare([EntityManageController], {
		
		getRequestUrl: function() {
			return "ajax.php?module=cms&categ=frbr_entities&action=get_already_selected_sorting&elem="+this.elem+"&id_element=0";
		},
	});
});