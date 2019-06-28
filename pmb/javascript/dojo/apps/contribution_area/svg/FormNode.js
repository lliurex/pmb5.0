// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormNode.js,v 1.3 2017-01-25 09:35:45 tsamson Exp $

define([
        "dojo/_base/declare", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojo/dom-class", 
        "dojo/query", 
        "apps/contribution_area/svg/Node",
        "d3/d3"
    ], function(declare,lang, topic, domClass, query, SvgNode, d3){
	return declare(SvgNode, {
		name: null,
		id: null,
		type: null,
		radius:15,
		active: null,
		eltId: null,
		propertyPmbName: '',
		constructor: function(data){
			this.name = data.name;
			this.id = data.id.toString();
			this.type = data.type;
			this.color = this.colors[6];
			this.eltId = data.eltId;
			this.propertyPmbName = data.propertyPmbName;
		},
		selectNode: function(){
			this.unselectNode();
		},
	});
});
	