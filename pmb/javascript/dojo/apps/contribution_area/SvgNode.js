// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SvgNode.js,v 1.2 2017-01-20 09:54:51 tsamson Exp $

define(["dojo/_base/declare", "dojo/_base/lang", "dojo/topic", "dojo/dom-class", "dojo/query"], function(declare,lang, topic, domClass, query){
	return declare(null, {
		name: null,
		id: null,
		type: null,
		radius:null,
		active: null,
		colors: ["#1f77b4", "#aec7e8", "#ff7f0e", "#ffbb78", "#2ca02c", "#98df8a", "#d62728", "#ff9896", "#9467bd", "#c5b0d5", "#8c564b", "#c49c94", "#e377c2", "#f7b6d2", "#7f7f7f", "#c7c7c7", "#bcbd22", "#dbdb8d", "#17becf", "#9edae5"],
		constructor: function(data){
			this.name = data.name;
			this.id = data.id;
			this.type = data.type;
			this.initDecoration();
			topic.subscribe('graph', lang.hitch(this, this.handleEvents));
		},
		handleEvents: function(evtType, evtArgs){
//			console.log(arguments);
			switch(evtType){
				case 'clickedNode':
					this.clickedNode(evtArgs.id);
					break;
			}
		},
		/**
		 * Specials Colors & radius depends of the type
		 */
		initDecoration: function(){
//			console.log(this.type);
			switch(this.type){
				case 'scenarii':
					this.color = this.colors[7];
					this.radius = 20;
					break;
				case 'attachment':
					this.color = this.colors[3];
					this.radius = 5;
					break;
			}
		},
		clickedNode: function(nodeId){
			
		}
	});
});
	