// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AttachmentNode.js,v 1.4 2017-01-27 15:06:17 tsamson Exp $

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
		radius:12,
		active: null,
		destType: null,
		propertyPmbName: '',
		constructor: function(data){
			this.name = data.name;
			this.id = data.id.toString();
			this.type = data.type;
			this.color = this.colors[5];
			this.temporary = true;
			this.destType = data.destType;
			this.propertyPmbName = data.propertyPmbName;
		},
//        handleEvents: function(evtType, evtArgs){
//            switch(evtType){
//                case '':
//                	break;
//            }
//        }
		dragOver: function(){
			var elt = window.draggedContributionElt;
			if (elt.type == 'scenario') {
				//si c'est le méme type			
				if(elt.entityType == this.destType){
					//on s'assure qu'il n'est pas déjé associé é ce noeud précis...
					var elts = graphStore.query({parent:this.id,type:'scenario'});
					var alreadyDroppedHere = false;
					elts.forEach(function(checkingElt){
						/**
						 * TODO : a revoir pour dropper plusieurs scénarios sur un point d'attache
						 */
//						if(checkingElt.eltId == elt.id){
//							alreadyDroppedHere = true;
//						}
						alreadyDroppedHere = true
					});
					if(!alreadyDroppedHere){
						d3.select("circle[id='"+this.id+"']").classed("droppable", true);
						d3.event.preventDefault();
					}else{
						d3.select("circle[id='"+this.id+"']").classed("alreadyDropped", true);
					}
				} else {
					d3.select("circle[id='"+this.id+"']").classed("alreadyDropped", true);
				}
			}
		},
		dragDrop : function(){
			//Le noeud n'est plus temporaire et doit rentrer dans le store (étre enregistré) 
			this.temporary = false;
			var elt = window.draggedContributionElt;
			switch(elt.type) {
				case 'form':
					elt.id = elt.form_id;
					break;
				case 'scenario':
					elt.parent_type = elt.entityType;
					break;
			}	
			topic.publish("Node", 'elementDropped', {target:this, elt:elt});
			d3.select("circle[id='"+this.id+"']").classed("droppable", false);
			d3.selectAll("circle").classed("inactive", false);
		},
		
		dragLeave: function(){			
			d3.select("circle[id='"+this.id+"']").classed("droppable", false);
			d3.select("circle[id='"+this.id+"']").classed("alreadyDropped", false);
		},
		canReceive: function(element){
			d3.select("circle[id='"+this.id+"']").classed("inactive", false);
			switch(element.type) {
				case 'scenario':
					element.parent_type = element.entityType;
					break;
				case 'form':
				default :
					break;
			}
			if(element.parent_type != this.entityType || (element.type == "form")){
				d3.select("circle[id='"+this.id+"']").classed("inactive", true);
			}
		},
	});
});
	