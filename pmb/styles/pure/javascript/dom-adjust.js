$(document).ready(function() {
	//$("#contenu>div[id$='Child']").wrap("<div class='row empr-section'></div>");
	$("#pbar").addClass("row");
	$("#contenu>table").wrap("<div class='row table-container bkg-white'></div>");
	$("#contenu>input,#contenu>p,#contenu>ul").wrap("<div class='row'></div>");
	// Encadre les left et right qui ne sont pas dans des row -made by vt-
	var leftItems = document.getElementsByClassName('left');
	for(var i=0 ; i<leftItems.length ; i++){
		 if(leftItems[i].nextElementSibling){
			if(leftItems[i].nextElementSibling){
				for(var j=0 ; j<leftItems[i].nextElementSibling.classList.length ; j++){
					if(leftItems[i].nextElementSibling.classList[j] == "right" && !(leftItems[i].parentNode && (leftItems[i].parentNode.className.indexOf("row") !== -1))){
						var row = document.createElement('div');
						leftItems[i].parentElement.insertBefore(row, leftItems[i]);
						var rightDiv = leftItems[i].nextElementSibling;
						row.setAttribute('class', 'row bkg-grey uk-clearfix plop');
						row.appendChild(leftItems[i]);
						row.appendChild(rightDiv);
					}
				}
			}   
		}
	}
	// Page circ.php categ=empr_saisie
	$("div[style='float:left']+div[style='float:right']").prev().add("div[style='float:left']+div[style='float:right']").wrapAll("<div class='bkg-grey uk-clearfix form-contenu'></div>");
	$('.item-expand').nextUntil("div").wrapAll("<span class='actions-search'></span>'");
	$('.actions-search,.item-expand,#contenu>b').wrapAll( "<div class='row'></div>" );
	$('#search_fields_tree_collapseall,#search_fields_tree_expandall').wrapAll( "<div class='row'></div>" );
	$(".admin #contenu>.row>.row>.item-expand>a").addClass("ss-nav-cms-item-expand ");
	$("#contenu-frame .form-alter> a").addClass("bdd_updade");//chargment ajax a modifier 
	$("#cms_drag_activate_button").parent(".row").addClass("ui-cms-drag-button");
	$(".cms div[onclick*='expandBase']").parent(".row").addClass("ui-expand-container");
	// Page categ doc num
	$(".form-contenu").prepend($(".form-catalog>.left:nth-child(1),.form-catalog>.right:nth-child(2)"))
	//$("#cms_build_navig_informations").next().css("height","675px"); ne fonctionne pas :(
	
	
	// search result  liste de résultat de recherche à passer en liste
    if ( $('#contenu>div').has(".actions-search")){
		$('.notice-parent,.notice-child').addClass('search-result-item')
	}; 
	//----]	
	if (document.URL.indexOf("categ=caddie") == -1) {
		$("#contenu>div[id$='Child']").wrap("<div class='row'></div>")
	};	
    if (document.URL.indexOf("categ=search") != -1) {
        $('.notice-parent,.notice-child').wrapAll("<div class='row'><ul class='uk-list uk-list-striped'></ul></div>");
		var notParent = Array.prototype.slice.call(document.querySelectorAll('.uk-list-striped .notice-parent'));
		notParent.forEach(function(parent){
			var li = document.createElement('li');
			//$(li).
			parent.parentElement.appendChild(li);
			var child = parent.nextElementSibling;
			li.appendChild(parent);
			li.appendChild(child);
			var parentElt = parent.parentElement;
		})	
		$("div:not('.notice-child')").each(function(){
			if ($(this).children().length == 0) {
				$(this).addClass('wyr-empty-box')
			}
		});
    };
    if (document.URL.indexOf("categ=last_records") != -1) {
       $(".row>.notice-parent,.row>div[class^='notice-parent']+div[class^='notice-child']").wrapAll("<div class='row'><ul class='uk-list uk-list-striped'></ul></div>");
		var notParent = Array.prototype.slice.call(document.querySelectorAll('.uk-list-striped .notice-parent'));
		notParent.forEach(function(parent){
			var li = document.createElement('li');
			//$(li).
			parent.parentElement.appendChild(li);
			var child = parent.nextElementSibling;
			li.appendChild(parent);
			li.appendChild(child);
			var parentElt = parent.parentElement;
		})	
		$("div:not('.notice-child')").each(function(){
			if ($(this).children().length == 0) {
				$(this).addClass('wyr-empty-box')
			}
		});
	};
	if (document.URL.indexOf("categ=serials&sub=search") != -1) {
        $('.notice-parent,.notice-child').wrapAll("<div class='row'><ul class='uk-list uk-list-striped'></ul></div>");
		var notParent = Array.prototype.slice.call(document.querySelectorAll('.uk-list-striped .notice-parent'));
		notParent.forEach(function(parent){
			var li = document.createElement('li');
			//$(li).
			parent.parentElement.appendChild(li);
			var child = parent.nextElementSibling;
			li.appendChild(parent);
			li.appendChild(child);
			var parentElt = parent.parentElement;
		})	
		$("div:not('.notice-child')").each(function(){
			if ($(this).children().length == 0) {
				$(this).addClass('wyr-empty-box')
			}
		});
    };		
	if (document.URL.indexOf("categ=caddie") != -1) {
		$('#contenu>.notice-child,#contenu>.notice-parent').wrapAll(("<div class='row'><div class='row'></div></div>"));
	};				
	$('.row.wyr-empty-box').next("div[class^='colonne']").wrap("<div class='row'></div>");
	
	$("#saisie_auteur.form-autorites").prepend("<div class='row bkg-white recepter'></div>");
	/*var moveItem = $("#saisie_auteur.form-autorites>.row>div.left,#saisie_auteur.form-autorites>.row>div.right").detach();
	$(".recepter").prepend(moveItem);*/
	// notication #extra2
	if ($(".notification>img[src$='new.png']").length == 1){
		$(".notification").addClass("uk-active")
	};


	
});