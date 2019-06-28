// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearchController.js,v 1.7 2017-07-25 08:47:09 arenou Exp $

define(['dojo/_base/declare',
        'dijit/layout/ContentPane',
        'dojo/store/Memory',
        'dojo/_base/lang',
        'apps/search/SearchFieldsTree',
        'dojo/query!css3',
        'dojo/dom-construct',
        'apps/search/SearchDnd',
        'dojo/dom-class',
        'dojo/dom-attr',
        'dijit/tree/ObjectStoreModel',
        'dojo/dom-style',
        'dojo/on',
        'dojox/widget/Standby',
        'dojo/request',
        'dojo/dom-form',
        'dojo/parser'
], function(declare, ContentPane, Memory, lang, SearchFieldsTree, query, domConstruct, SearchDnd, domClass, domAttr, ObjectStoreModel, domStyle, on, Standby, request, domForm, parser) {
	return declare(null, {
		contentTree: null,
		contentForm: null,
		store: null,
		searchFieldsList: null,
		widgets: [],
		
		constructor: function() {
			this.generateDom();
			this.parseSelector();
			this.buildTree();
			this.buildForm();
		},
		
		generateDom: function() {
			this.contentTree = new ContentPane({
				splitter: true,
				region: 'left',
				style: 'height:100%;width:250px;'
			}).placeAt('extended_search_dnd_container');
			this.contentForm = new ContentPane({
				id: 'extended_search_dnd_content_form',
				splitter: true,
				region: 'center',
				style: 'height:100%;'
			}).placeAt('extended_search_dnd_container');
		},
		
		parseSelector: function() {
			this.store = new Memory({data:[{id: 'root'}]});
			var children = dojo.byId('add_field').children;
			for (var i in children) {
				if ((children[i].nodeName == 'OPTGROUP') && (children[i].children.length)) {
					this.store.put({
						id: 'parent_' + i,
						label: children[i].label,
						parent: 'root'
					});
					for (var j in children[i].children) {
						if (children[i].children[j].nodeName == 'OPTION') {
							this.store.put({
								id: children[i].children[j].value,
								label: children[i].children[j].label,
								parent: 'parent_' + i,
								leaf: true
							});
						}
					}
				}
			}
			this.store.getChildren = function(object) {
				return lang.hitch(this.store, this.query({parent: object.id}));
			};
			domStyle.set(dojo.byId('choose_criteria'), 'display', 'none');
			domStyle.set(dojo.byId('add_field'), 'display', 'none');
		},
		
		buildTree: function() {
			// Un titre pour l'arbre
			domConstruct.place('<h3>' + dojo.byId('choose_criteria').innerHTML + '</h3>', this.contentTree.id);
			
			// Expand/Collapse all
			domConstruct.place('<span id="search_fields_tree_expandall" class="liLike"><img class="dijitTreeExpando dijitTreeExpandoClosed" data-dojo-attach-point="expandoNode" src="./images/expand_all.gif"></span><span id="search_fields_tree_collapseall" class="liLike"><img class="dijitTreeExpando dijitTreeExpandoOpened" data-dojo-attach-point="expandoNode" src="./images/collapse_all.gif"></span><div class="row"></div>', this.contentTree.id);
			var model = new ObjectStoreModel({
				store: this.store,
				query: { id: 'root'},
				mayHaveChildren: function(item) {
					return !item.leaf;
				}
			});
			var tree = new SearchFieldsTree({model: model, searchController: this});
			tree.placeAt(this.contentTree);
			on(dojo.byId('search_fields_tree_expandall'), 'click', function() {tree.expandAll();});
			on(dojo.byId('search_fields_tree_collapseall'), 'click', function() {tree.collapseAll();});
			
			var search_perso = dojo.byId('search_perso');
			if(search_perso){
				domConstruct.place('<hr><h3>' + pmbDojo.messages.getMessage('search', 'search_perso_title') + '</h3>', this.contentTree.id);
				domConstruct.place(search_perso,this.contentTree.id);
				domStyle.set(search_perso, 'display', 'block');
			}
		},
		
		buildForm: function() {
			var form = query('form[name="search_form"]')[0];
			domConstruct.place(form,this.contentForm.id);
			this.widgets = parser.parse(form);
			this.updateForm(form);
		},
		
		updateForm: function(form) {
			this.searchFieldsList = query('table tbody tr', form);
			// On enl�ve la ligne du tableau qui contient le bouton rechercher
			this.searchFieldsList.pop();
			if (this.searchFieldsList.length) {
				if (domStyle.get(form, 'display') == 'none') {
					domStyle.set(form, 'display', 'block');
				}
				if (dojo.byId("search_fields_no_selected_fields")) {
					domConstruct.destroy("search_fields_no_selected_fields");
				}
				this.initDnd();
				this.updateDeleteButtons();
			} else {
				domStyle.set(form, 'display', 'none');
				domConstruct.place('<span class="saisie-contenu" id="search_fields_no_selected_fields">' + pmbDojo.messages.getMessage('search', 'search_fields_no_selected_fields') + '</span>',this.contentForm.id);
			}
		},
		
		getFormInfos: function() {
			var stand = new Standby({target: 'extended_search_dnd_content_form', imageText: 'Chargement...', image: './images/patience.gif'});
			document.body.appendChild(stand.domNode);
			stand.startup();
			stand.show();
			form = query('form[name="search_form"]')[0];
			request.post("ajax.php?module=ajax&categ=extended_search&sub=get_already_selected_fields",{
				data : JSON.parse(domForm.toJson(form))
			}).then(lang.hitch(this, function(data) {
				for (var i = 0; i < this.widgets.length; i++) {
					this.widgets[i].destroy();
				}
				domConstruct.place(data, query('form[name="search_form"] table')[0], 'replace');
				this.widgets = parser.parse(query('form[name="search_form"] table')[0]);
				this.updateForm(form);
				ajax_parse_dom();
				stand.hide();
			}));
		},
		
		initDnd: function() {
			if (this.searchFieldsList.length) {
				var dndForm = new SearchDnd(this.searchFieldsList[0].parentNode, {type: ['searchField'], searchController: this});
				this.searchFieldsList.forEach(this.declareItems, this);
				dndForm.sync();
			}
		},
		
		declareItems: function(node, index, nodeList) {
			domClass.add(node, 'dojoDndItem');
			// On met une poign�e !
			domConstruct.place('<i class="fa fa-arrows"></i>', node.childNodes[0]);
			domStyle.set(node.childNodes[0], 'cursor', 'move');
			domAttr.set(node, 'search_field_index', index);
			domClass.add(node.childNodes[0], 'dojoDndHandle');
		},
		
		updateDeleteButtons: function() {
			var delete_field = query('form[name="search_form"] input[name="delete_field"]')[0];
			this.searchFieldsList.forEach(function(node, index, nodeList){
				var search_field_index = domAttr.get(node, 'search_field_index');
				var button = dojo.byId('delete_field_button_' + search_field_index);
				if (button) {
					domAttr.set(button, 'onclick', '');
					on(button, 'click', lang.hitch(this, function() {
						domAttr.set(delete_field, 'value', search_field_index);
						this.getFormInfos();
						domAttr.set(delete_field, 'value', '');
					}));
				}
			}, this);
		}
	});
});