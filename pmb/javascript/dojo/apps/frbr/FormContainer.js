// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FormContainer.js,v 1.11 2017-05-10 16:08:24 tsamson Exp $


define(["dojo/_base/declare", 
        "dijit/layout/ContentPane", 
        "dojo/store/Memory", 
        "dijit/tree/ObjectStoreModel", 
        "dojo/_base/lang",
        "dijit/form/Button",
        "dojo/dom-construct",
        "dojo/request/xhr",
        "dojo/_base/lang",
        "dojo/topic",
        "dojo/dom-form",
        "dojo/query",
        "dijit/registry"], 
        function(declare, ContentPane, Memory, ObjectStoreModel, lang, Button, domConstruct, xhr, lang, topic, domForm, query, registry){
	return declare([ContentPane], {
		constructor: function(){
			this.set('executeScripts', true);
			this.own(	topic.subscribe('EntityTree', lang.hitch(this, this.handleEvents)),
						topic.subscribe('formButton', lang.hitch(this, this.handleEvents)),
						topic.subscribe('TreeContainer', lang.hitch(this, this.handleEvents))						
			);
		},
		
		postCreate:function(){
			this.inherited(arguments);			
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){
				case 'leafClicked':			
					this.requestContent(evtArgs);
					break;
				case 'addNode':
					this.addNode(evtArgs);
					break;
				case 'saveNode':
					this.saveNode(evtArgs);
					break;
				case 'deleteNode':
					this.deleteNode(evtArgs);
					break;
				case 'clearForm': 
					this.clearContentPane();
					break;
				case 'leafRootClicked':			
					this.requestRootContent(evtArgs);
					break;
			}			
		},
		clearContentPane: function(){
			this.set('content', '');
		},
		requestContent: function(item){
			var num_parent_from_leaf = 0;
			if (item.parent) {
				num_parent_from_leaf = item.parent.split('_')[1];
			}
			var id_from_leaf = item.id.split('_')[1];
			xhr("./ajax.php?module=cms&categ=frbr_entities&action=get_form&type="+item.type+"&num_page="+item.page+"&num_parent="+num_parent_from_leaf+"&id="+id_from_leaf, {
				handleAs: "text"
			}).then(lang.hitch(this,this.loadContent));
		},
		requestRootContent: function(item){
			xhr("./ajax.php?module=cms&categ=frbr_entities&action=get_form&type=page&num_page="+item.page, {
				handleAs: "text"
			}).then(lang.hitch(this,this.loadContent));
		},
		
		addNode: function(arg){
			var num_parent_from_leaf = 0; 
			if (!arg.selectedItem.root) {
				num_parent_from_leaf = arg.selectedItem.id.split('_')[1];
			}
			xhr("./ajax.php?module=cms&categ=frbr_entities&action=get_form&type="+arg.type+"&num_page="+arg.selectedItem.page+"&num_parent="+num_parent_from_leaf, {
				handleAs: "text"
			}).then(lang.hitch(this,this.loadContent));
		},
		
		loadContent:function(data){ //Called by the xhr promise (then)
			var widgets = query('[widgetid]', this.domNode);
			widgets.forEach(function(widget){
				var widget = registry.byId(widget.getAttribute('id'));
				if(widget){
					widget.destroy();
				}
			});
			this.set("content", data);
			preLoadScripts(this.domNode);
			topic.publish('FormContainer', 'formLoaded');
		},
		saveNode : function(form) {
			xhr(form.action + '&num_page=' + this.numPage,{
				data :JSON.parse(domForm.toJson(form.id)),
				handleAs: "json",
				method:'POST'
			}).then(lang.hitch(this,function(response){
				if (response.status) {
					topic.publish('FormContainer', 'updateTree', response);
					var item = {id : response.type+'_'+response.status, page : this.numPage, type : response.type};
					this.requestContent(item);
				}				
			}));
		},
		deleteNode : function(params) {
			xhr('./ajax.php?module=cms&categ=frbr_entities&type='+params.type+'&action=delete&id_element='+params.id+'&num_page=' + this.numPage + '&recursive=' + params.recursive,{
				handleAs: "json",
				method:'GET'
			}).then(lang.hitch(this,function(response){
				if (response.status) {
					topic.publish('FormContainer', 'updateTree', response);
					this.domNode.innerHTML = "";
				}	
			}));
		},
		
		
	});
});