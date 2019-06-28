// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: TreeContainer.js,v 1.7 2017-05-24 15:24:47 tsamson Exp $


define([
        "dojo/_base/declare", 
        "dijit/layout/ContentPane", 
        "dojo/parser", 
        "apps/frbr/EntityTree", 
        "dijit/layout/BorderContainer",
        "dijit/form/Button",
        "apps/frbr/FormContainer",
        "dojo/topic",
        "dojo/_base/lang"], 
		function(declare,ContentPane,parser,EntityTree,BorderContainer, Button, FormContainer, topic, lang){
	return declare([BorderContainer], {
		tree : null,
		leftContentPane: null,
		constructor: function(){
			this.own(topic.subscribe('FormContainer', lang.hitch(this, this.handleEvents)),
					topic.subscribe('EntityTree', lang.hitch(this, this.handleEvents)));
		},
		postCreate:function(){
			this.inherited(arguments);
			this.leftContentPane = new ContentPane({region : 'left', splitter:true});
			this.tree = new EntityTree(this.data);
			
			this.addDatanode = new Button(
					{
						label : pmbDojo.messages.getMessage('frbr', 'frbr_add_datanode'),
						disabled : true,
						onClick: lang.hitch(this, function(){
							topic.publish('TreeContainer', 'addNode', {selectedItem : this.tree.selectedItem, type : 'datanode'});
						})
					}
			);
			
			this.addFrame = new Button(
					{
						label : pmbDojo.messages.getMessage('frbr', 'frbr_add_frame'),	
						disabled : true,
						onClick: lang.hitch(this, function(){
							topic.publish('TreeContainer', 'addNode', {selectedItem : this.tree.selectedItem, type : 'cadre'});
						})
					}
			);
			
			this.leftContentPane.addChild(this.addDatanode);
			this.leftContentPane.addChild(this.addFrame);
			this.leftContentPane.addChild(this.tree);
			this.addChild(this.leftContentPane);
			
			var formContainer = new FormContainer({region:'center', splitter:true, numPage : this.data.num_page});
			this.addChild(formContainer);
			
			
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'updateTree':
					this.cutTree(evtArgs);
					break;
				case 'leafRootClicked':			
				case 'leafClicked':
					this.disabledButtons(evtArgs);
					break;
			}
		},
		
		init: function(){
			
		},
		
		parseAddedContent: function(){
			
		},
		
		cutTree: function(evtArgs){
			this.tree.destroy();
			this.tree = null;
			this.tree = new EntityTree(JSON.parse(evtArgs.tree_data));
			this.leftContentPane.addChild(this.tree);
			this.selectTreeNodeById(evtArgs.status, evtArgs.type);
		},	
		
	    recursiveHunt : function(item, itemPath){
	    	itemPath.unshift(item);
	    	if (!item.root) {
	    		var parent = this.tree.memoryStore.getParent(item)[0];
	    		this.recursiveHunt(parent,itemPath);	    		
	    	}
	    	return itemPath;
	    },

	    selectTreeNodeById : function(id, type){
	    	var itemId = 0;
	    	if (type != "page") {
	    		itemId = type + '_' + id;
	    	}
	        var item = this.tree.memoryStore.query({'id': itemId})[0];
	        var itemPath = new Array();
	        if (item) {
	        	this.tree.set("path",this.recursiveHunt(item, itemPath));	        
	        }
	    },
	    
	    disabledButtons : function(item) {
	    	if (item.type == 'cadre') {
	    		this.addDatanode.set('disabled',true);
	    		this.addFrame.set('disabled',true);
	    	} else {
	    		this.addDatanode.set('disabled',false);
	    		this.addFrame.set('disabled',false);
	    	}
	    },
	});
});