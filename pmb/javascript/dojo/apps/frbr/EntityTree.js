// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: EntityTree.js,v 1.9 2017-05-10 16:08:24 tsamson Exp $


define(["dojo/_base/declare", 
        "dijit/Tree", 
        "dojo/store/Memory", 
        "dijit/tree/ObjectStoreModel", 
        "dojo/_base/lang",
        "dijit/form/Button",
        "dojo/dom-construct",
        "dojo/topic",
        "dojox/widget/Standby"], 
        function(declare, Tree, Memory, ObjectStoreModel, lang, Button, domConstruct, topic, Standby){
	return declare([Tree], {
		id : 'frbrTree',
		persist : true,
		patience: null,
		memoryStore: null,
		constructor: function(data){
			//Memory store contenant les donn�es n�cessaires pour l'objectstoremodel d�fini plus bas. 
			//C'est sur celui ci que sera directement cabl� le tree dojo
			this.memoryStore = new Memory({
				data:this.formatData(data),
				getChildren: function(object){
					return this.query({parent: object.id});
		        },
		        getParent : function(object){
		        	return this.query({id : object.parent});
		        }
			});
			
			//ModelStore le mod�le autour duquel est articul� l'arbre
			var modelStore = new ObjectStoreModel({
				store:this.memoryStore,
				labelType: 'html',
				query: {root:true},
				mayHaveChildren : function(item) {
					//pas d'icone + sur les cadres
					if (item.type == "cadre") {
						return false;
					}
					return true;
				}
			});
			
			this.model = modelStore;
			this.own(topic.subscribe('FormContainer', lang.hitch(this, this.handleEvents)),
					topic.subscribe('formButton', lang.hitch(this, this.handleEvents)));
		},
		
		getIconClass: function(item, opened){
			return 'no_icon';
		},
		
		postCreate:function(){
			this.inherited(arguments);
		},
		
		handleEvents: function(evtType,evtArgs){
			switch(evtType){			
				case 'formLoaded':
					this.hidePatience();
					break;
				case 'parentChange':
					this.focusParent(evtArgs.parentId);
					break;
				case 'checkChildrenToDelete':
					this.checkChildrenToDelete(evtArgs);
					break;
			}
		},

		init: function(){
			
		},
		_load: function(){
			// summary:
			//		Initial load of the tree.
			//		Load root node (possibly hidden) and it's children.
			this.model.getRoot(
				lang.hitch(this, function(item){
					var rn = (this.rootNode = this.tree._createTreeNode({
						item: item,
						tree: this,
						isExpandable: true,
						label: this.label || this.getLabel(item),
						labelType: this.model.labelType || "text",
						textDir: this.textDir,
						indent: this.showRoot ? 0 : -1
					}));
					
					if(!this.showRoot){
						rn.rowNode.style.display = "none";
						// if root is not visible, move tree role to the invisible
						// root node's containerNode, see #12135
						this.domNode.setAttribute("role", "presentation");
						this.domNode.removeAttribute("aria-expanded");
						this.domNode.removeAttribute("aria-multiselectable");

						// move the aria-label or aria-labelledby to the element with the role
						if(this["aria-label"]){
							rn.containerNode.setAttribute("aria-label", this["aria-label"]);
							this.domNode.removeAttribute("aria-label");
						}else if(this["aria-labelledby"]){
							rn.containerNode.setAttribute("aria-labelledby", this["aria-labelledby"]);
							this.domNode.removeAttribute("aria-labelledby");
						}
						rn.labelNode.setAttribute("role", "presentation");
						rn.labelNode.removeAttribute("aria-selected");
						rn.containerNode.setAttribute("role", "tree");
						rn.containerNode.setAttribute("aria-expanded", "true");
						rn.containerNode.setAttribute("aria-multiselectable", !this.dndController.singular);
					}else{
						this.domNode.setAttribute("aria-multiselectable", !this.dndController.singular);
						this.rootLoadingIndicator.style.display = "none";
					}

					this.containerNode.appendChild(rn.domNode);
					var identity = this.model.getIdentity(item);
					if(this._itemNodesMap[identity]){
						this._itemNodesMap[identity].push(rn);
					}else{
						this._itemNodesMap[identity] = [rn];
					}

					rn._updateLayout();		// sets "dijitTreeIsRoot" CSS classname

					// Load top level children, and if persist==true, all nodes that were previously opened
					this._expandNode(rn).then(lang.hitch(this, function(){
						// Then, select the nodes specified by params.paths[], assuming Tree hasn't been deleted.
						if(!this._destroyed){
							this.rootLoadingIndicator.style.display = "none";
							this.expandChildrenDeferred.resolve(true);
						}
					}));
				}),
				lang.hitch(this, function(err){
					console.error(this, ": error loading root: ", err);
				})
			);
		},
		onClick: function(item){
			this.inherited(arguments);
			if(!item.root){
				topic.publish("EntityTree","leafClicked",item);	
			}else{
				topic.publish("EntityTree","leafRootClicked", item);
			}
			this.setPatience();
		},
		
		formatData : function(data) {
			var formatData = [];
			formatData.push(data['rootNode']);
			formatData = formatData.concat(this.nodeCrawler(data, "treeDatanodes"));
			formatData = formatData.concat(this.nodeCrawler(data, "treeCadres"));
			return formatData;
		},
		
		nodeCrawler: function(data,keySearcher){
			var returnTable = [];
			if(data[keySearcher]){
				for(var key in data[keySearcher]){
					data[keySearcher][key].id = data[keySearcher][key].type+'_'+data[keySearcher][key].id;
					if(parseInt(data[keySearcher][key].parent)) {
						data[keySearcher][key].parent = 'datanode_'+data[keySearcher][key].parent;
					}
					returnTable.push(data[keySearcher][key]);
				}
			}
			return returnTable;
		},
		
		getLabel : function(item){
			var label = this.model.getLabel(item);
			if(item.root){
				return '<i class="fa fa-database" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
			}else if(item.type == "cadre"){
				return '<i class="fa fa-picture-o" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
			}else{
				return '<i class="fa fa-database" aria-hidden="true"></i>&nbsp;<span class="leafLabel">'+label+'</span>';
			}
			return label;
		},
		setPatience: function(){
			if(!this.patience){
				this.patience = new Standby({target: this.domNode.getAttribute('id')});
				document.body.appendChild(this.patience.domNode);
				this.patience.startup();
			}
			this.patience.show();
		},
		hidePatience: function(){
			if (this.patience) {
				this.patience.hide();
			}
		},
		
		focusParent : function(parentId) {
			var node = this.getNodesByItem(parentId);
			if (node[0]) {
 				this.focusChild(node[0]);
			}
		},
		
		getChildrenItems : function(params) {
			var parentItem = {};
			parentItem.id = params.type + '_' + params.id;
			var items = this.memoryStore.getChildren(parentItem);			
			if (items.length) {
				return items;
			}
			return false;
		},
		
		checkChildrenToDelete : function(params) {
			var childrenItems = this.getChildrenItems(params);
			if (childrenItems) {
				if (confirm(pmbDojo.messages.getMessage('frbr', 'frbr_delete_recursive'))) {
					topic.publish('formButton', 'deleteNode', {id : params.id, type : params.type, recursive : 1});
				}
			} else {
				topic.publish('formButton', 'deleteNode', {id : params.id, type : params.type, recursive : 0});
			}		
		}

	});
});