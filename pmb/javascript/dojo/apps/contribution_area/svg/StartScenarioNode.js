// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: StartScenarioNode.js,v 1.1 2017-01-25 09:35:45 tsamson Exp $

define([
        "dojo/_base/declare", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojo/dom-class", 
        "dojo/query", 
        "apps/contribution_area/svg/ScenarioNode",
        "d3/d3"
    ], function(declare,lang, topic, domClass, query, ScenarioNode, d3){
	return declare(ScenarioNode, {
		name: null,
		id: null,
		type: null,
		radius:20,
		active: null,
		constructor: function(data){
			this.name = data.name;
			this.id = data.id.toString();
			this.type = data.type;
			this.color = this.colors[7];
			this.entityType = data.entityType;
		},		
	});
});
	