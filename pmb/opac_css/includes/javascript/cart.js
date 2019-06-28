// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cart.js,v 1.7 2017-06-21 14:54:01 tsamson Exp $

function changeBasketImage(id_notice, images_url) {
	
	//Affichage de notices via la classe notice_affichage
	if(window.parent.document.getElementById('baskets'+id_notice)) {
		var basket_node = window.parent.document.getElementById('baskets'+id_notice);
		if (basket_node.hasChildNodes()) {
				basket_node.removeChild(basket_node.firstChild);
		}
		var basket_link = window.parent.document.createElement('a');
		basket_link.setAttribute('href','./index.php?lvl=show_cart');
		basket_link.setAttribute('class','img_basket_exist');
		basket_link.setAttribute('title',msg_notice_title_basket_exist);
		basket_node.appendChild(basket_link);
		var basket_img = window.parent.document.createElement('img');
		basket_img.setAttribute('src',images_url['basket_exist']);
		basket_img.setAttribute('alt',msg_notice_title_basket_exist);
		basket_link.appendChild(basket_img);
	}
	
	//Affichage de notices via les templates Django
	if(window.parent.document.getElementById('record_container_'+id_notice+'_cart')) {
		var basket_node = window.parent.document.getElementById('record_container_'+id_notice+'_cart');
		if (basket_node.hasChildNodes()) {
			while (basket_node.hasChildNodes()) {  
				basket_node.removeChild(basket_node.firstChild);
			}
		}
		var basket_link = window.parent.document.createElement('a');
		basket_link.setAttribute('href','./index.php?lvl=show_cart');
		basket_link.setAttribute('class','img_basketNotCourte img_basketNot');
		basket_link.setAttribute('title',msg_notice_title_basket_exist);
		basket_node.appendChild(basket_link);
		var basket_span_img = window.parent.document.createElement('span');
		basket_span_img.setAttribute('class','icon_basketNot');
		basket_link.appendChild(basket_span_img);
		var basket_span_label = window.parent.document.createElement('span');
		basket_span_label.setAttribute('class','label_basketNot');
		basket_link.appendChild(basket_span_label);
		var basket_img = window.parent.document.createElement('img');
		basket_img.setAttribute('src',images_url['record_in_basket']);
		basket_img.setAttribute('alt',msg_notice_title_basket_exist);
		var basket_txt = document.createTextNode(msg_notice_title_basket_exist);
		basket_span_img.appendChild(basket_img);
		basket_span_label.appendChild(basket_txt);
	}
}