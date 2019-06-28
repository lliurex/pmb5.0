// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PMBConfirmDialog.js,v 1.1.2.2 2017-09-05 12:47:35 vtouchard Exp $


define(["dojo/_base/declare", "dijit/ConfirmDialog", "dojo/_base/lang", "dojo/dom-class"], function(declare, ConfirmDialog, lang, domClass){

	  return declare([ConfirmDialog], {
		  show: function(){
			  this.inherited(arguments);
			  if (!domClass.contains(document.body, "dojoDialogOpened")){
				  domClass.add(document.body, "dojoDialogOpened");
			  }
		  },
		  hide: function(){
			  this.inherited(arguments);
			  if (domClass.contains(document.body, "dojoDialogOpened")){
				  domClass.remove(document.body, "dojoDialogOpened");
			  }
		  },
	  });
});