// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsEditorialTypeContentForm.js,v 1.1.2.2 2017-12-15 14:19:30 dgoron Exp $


define([
        'dojo/_base/declare',
        'dojox/layout/ContentPane',
        'apps/pmb/gridform/FormEdit',
        ], function(declare, ContentPane, FormEdit){
		return declare([ContentPane], {
			type:null,
			constructor: function(data) {
				this.type = data.type;
			},
			onLoad: function(){
//				new FormEdit('cms', this.type);
				ajax_parse_dom();
				init_drag();
			},
		})
});