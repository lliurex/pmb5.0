/* +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: templates.js,v 1.2 2017-03-17 13:51:33 dgoron Exp $ */

if(typeof templates == "undefined"){
	templates = {
		input_completion_field : function(name, completion, autfield) {
			var input = document.createElement('input');
			input.setAttribute('name',name);
			input.setAttribute('id',name);
			input.setAttribute('type','text');
			input.className='saisie-30emr';
			input.setAttribute('value','');
			input.setAttribute('completion', completion);
			input.setAttribute('autfield', autfield);
			return input;
		},
		delete_button_field : function(name, input_field, hidden_field) {
			var button = document.createElement('input');
			button.setAttribute('id',name);
			button.onclick=function() {
				input_field.value = '';
				hidden_field.value = '';
			};
			button.setAttribute('type','button');
			button.className='bouton_small';
			button.setAttribute('readonly','');
			button.setAttribute('value',pmbDojo.messages.getMessage('template','raz'));
			return button;
		},
		selector_button_field : function(name, selector_fonction) {
			var button = document.createElement('input');
			button.setAttribute('id',name);
			button.setAttribute('type','button');
			button.className='bouton';
			button.setAttribute('readonly','');
			button.setAttribute('value',pmbDojo.messages.getMessage('template','parcourir'));
			button.onclick=selector_fonction;
			return button;
		},
		hidden_field : function(name) {
			var hidden = document.createElement('input');
			hidden.name=name;
			hidden.setAttribute('type','hidden');
			hidden.setAttribute('id',name);
			hidden.setAttribute('value','');
			return hidden;
		},
		get_max_node : function(name) {
			if(document.getElementById('max_'+name)) {
				return document.getElementById('max_'+name);
			} else {
				//Hack
				if(document.getElementById('max_'+name.replace('f_', ''))) {
					return document.getElementById('max_'+name.replace('f_', ''));
				}
			}
		},
		get_add_node : function(name) {
			if(document.getElementById('add'+name)) {
				return document.getElementById('add'+name);
			} else {
				//Hack
				if(document.getElementById('add'+name.replace('f_', ''))) {
					return document.getElementById('add'+name.replace('f_', ''));
				}
			}
		},
		add_completion_field : function(name, id, completion) {
			var suffixe = this.get_max_node(name).value;
			
			var input_completion_field = this.input_completion_field(name+suffixe, completion, id+suffixe);
			
			var hidden_field = this.hidden_field(id+suffixe);
			
			var delete_button_field = this.delete_button_field('del_'+name+suffixe, input_completion_field, hidden_field);
			
			var div=document.createElement('div');
			div.className='row';
			div.appendChild(input_completion_field);
		    div.appendChild(document.createTextNode(' '));
		    div.appendChild(delete_button_field);
		    div.appendChild(hidden_field);

		    this.get_add_node(name).appendChild(div);
		    this.get_max_node(name).value = suffixe*1+1*1;
			ajax_pack_element(input_completion_field);
		},
		add_completion_selection_field : function(name, id, completion, selector_fonction) {
			var suffixe = this.get_max_node(name).value;
			
			var input_completion_field = this.input_completion_field(name+suffixe, completion, id+suffixe);
			
			var hidden_field = this.hidden_field(id+suffixe);
			
			var selector_button_field = this.selector_button_field('sel_'+name+suffixe, selector_fonction);
			
			var delete_button_field = this.delete_button_field('del_'+name+suffixe, input_completion_field, hidden_field);
			
			var div=document.createElement('div');
			div.className='row';
			div.appendChild(input_completion_field);
		    div.appendChild(document.createTextNode(' '));
		    div.appendChild(selector_button_field);
		    div.appendChild(document.createTextNode(' '));
		    div.appendChild(delete_button_field);
		    div.appendChild(hidden_field);

		    this.get_add_node(name).appendChild(div);
		    this.get_max_node(name).value = suffixe*1+1*1;
			ajax_pack_element(input_completion_field);
		},
		add_completion_qualified_field : function(name, id, completion, select_name) {
			var suffixe = this.get_max_node(name).value;
			
		    var select_field = document.getElementById(select_name+'0').cloneNode(true);	
		    select_field.setAttribute('name', select_name + suffixe);
		    select_field.setAttribute('id', select_name + suffixe);
		    
			var input_completion_field = this.input_completion_field(name+suffixe, completion, id+suffixe);
			
			var hidden_field = this.hidden_field(id+suffixe);
			
			var delete_button_field = this.delete_button_field('del_'+name+suffixe, input_completion_field, hidden_field);
			
			var div=document.createElement('div');
			div.className='row';
			div.appendChild(select_field);
		    div.appendChild(document.createTextNode(' '));
			div.appendChild(input_completion_field);
		    div.appendChild(document.createTextNode(' '));
		    div.appendChild(delete_button_field);
		    div.appendChild(hidden_field);

		    this.get_add_node(name).appendChild(div);
		    this.get_max_node(name).value = suffixe*1+1*1;
			ajax_pack_element(input_completion_field);
		},
	}
}