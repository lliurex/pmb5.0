// $Id: k-set-wyrkit.js,v 1.14.2.14 2017-12-28 14:27:36 wlair Exp $ */

$(document).ready(function() {
    var selectorFinalUrl = '.' + window.location.href.substring(window.location.href.lastIndexOf('/'));
    var selectorSetterMenu = document.getElementById('menu');
    if (selectorSetterMenu) {
        var selectorSetterLinks = selectorSetterMenu.querySelectorAll('a[href]');
        var selectorSetterLink = selectorSetterMenu.querySelector('a[href="' + selectorFinalUrl + '"]');
        if (!selectorSetterLink) {
            selectorSetterLinks = Array.prototype.slice.call(selectorSetterLinks);
            selectorSetterLinks.some(function(link) {
                if (link.href && selectorFinalUrl.indexOf(link.getAttribute('href')) != -1) {
                    //Faire ton traitement ici (mettre la classe selected ou je ne sais quoi avec jQuery)
                    link.parentElement.classList.add('uk-active');
                    return true;
                }
            });
        } else {
            //Faire ton traitement ici (mettre la classe selected ou je ne sais quoi avec jQuery)
            selectorSetterLink.parentElement.classList.add('uk-active');
        }
    }
    
    $(".hmenu").addClass(function(index) {
        if ($(".hmenu").children().length === 0)
            return "empty-node"
    });
    $(".row").addClass(function(index) {
        if ($(this).children().length > 0){
            return "uk-clearfix"}
    })
    $(".right:empty").addClass("empty-node-g");
    $("a[title='Tableau de Bord']").addClass("dashboard");
	//$("#extra2").prepend("<div class='sticky-placeholder-notif'></div>");
	
    // flex
    $("#cms_menu_editorial_tree").addClass("uk-flex-auto.uk-flex.uk-flex-column ss-nav-cms");
    $("#cms_menu_editorial_tree").children().addClass("ss-nav-cms-item");
    $("#cms_menu_editorial_tree .ss-nav-cms-item img").parent().addClass("ss-nav-cms-item-expand");
    $("#add_buttons").children().addClass("uk-button uk-button-default uk-button-small ui-button-Xsmall wyr-custom wyr-input");
    $("form[name='serialcirc_state_filters']>div:first-child").addClass("uk-flex");
    $(".form-contenu .colonne_suite").last().addClass("uk-flex-auto");
    $(".form-contenu").find(".colonne_suite:last-child").addClass("wyr-flex-item");
    $(".wyr-flex-item").parent().not('td').addClass("uk-flex");
    $(".left").parent("#empr-name").addClass("uk-flex uk-flex-between");
    $("#bloc_adresse_empr").addClass("uk-flex uk-flex-between wyr-flex-container");
    $("#bloc_adresse_empr * .colonne3").parent().addClass('uk-flex uk-flex-between wyr-flex-container');	
    $("#bloc_adresse_empr").children().addClass("wyr-flex-item");
    $("#empr-name .right font").addClass("uk-label ui-label-custom");
	$(".form-catalog .form-contenu:has('>.colonne_suite')").last().addClass("uk-flex-wrap uk-flex ");
	$(".row.bkg-grey:has('>.left,>.rigth')").addClass("uk-flex uk-flex-between wyr-flex-container uk-flex-wrap uk-flex-auto");
	$("#id_autorite").parent().addClass("uk-flex uk-flex-auto uk-flex-wrap-between uk-flex-between tri-f");
	$(".tri-f>select,.tri-f>input").addClass("ui-flex-1-3");
	
	
    $('#bloc_adresse_empr>.colonne3').has("b").addClass("full-width");
	$('#empr_form_actions_buttons').parent('.row').addClass("uk-flex uk-flex-between wyr-flex-container empr_form_actions_container");
    $("td").not('input-in-td').addClass(function(index) {
        if ($(this).children('input').length > 2) {
            return "uk-flex uk-flex-row input-in-td";
        }
    });
    $('.right').prev('table').addClass(function(index) {
        if ($('.right').children('input').length > 0) {
            return "uk-margin-bottom";
        }
    });
    $('.left').addClass(function(index) {
        if ($(this).siblings('.left').length > 1) {
            return "uk-margin-small-bottom uk-margin-small-right";
        }
    });
    $('.input-in-td>input').addClass('uk-input uk-form-Xsmall');
    // base
	$('h1').parent("#contenu>div").first().addClass("first-child");
	$("div#contenu>p").first().addClass("first-child");
    $("div:has(>hr)").each(function(index) {
        if ($(this).children().length == 1) {
            $(this).remove();
        } else if ($(this).children().length > 1) {
            $(this).addClass("has-hr");
        }
    });	
   
	$('script').parent("p:not('.dijitSplitter')").each(function(){
	 	if ($(this).children().length == 1) {
			$(this).addClass('wyr-empty-box-p')
		}
	});	
	$("div:has(>div:empty),div:has(>span:empty)").not("div[class^='dijit']").addClass(function(index) {
        if ($(this).children().length == 1) {
            return "wyr-empty-box-p";
        }
    });
	$("div#contenu>div,div#contenu>form,#make_mul_sugg-container,#import_sug-container").not("div[class*='wyr-empty'], .first-child").addClass("bkg-white");
	$("#contenu>*.bkg-white:first").addClass("first-white");
	$("#contenu>*.bkg-white:last").addClass("last-white");
	$("input,select").parent("blockquote").addClass("pmb-build-bq");
	$("div.row.wyr-empty-box:contains('e')").addClass("bkg-white");	

    Array.prototype.slice.call(document.getElementsByClassName('notice-child')).forEach(
	function(elt){
		var style = elt.getAttribute('style');
		elt.setAttribute('style', '');
		if(style && style.indexOf('display:none') != -1){
			elt.setAttribute('style', 'display:none;');
		}
	});

    $("iframe[name='term_search']").parent().addClass("bkg-white");
    $("div#contenu>h2").addClass("bkg-white section-sub-title article-title");//titre de type contenu>h2 sans row
    $("div#contenu>.row>h2").addClass("section-sub-title article-title");//titre de type h2 contenu dans une row>contenu
    $('.bkg-white:has(>.left+.right)').addClass('uk-flex uk-flex-between wyr-flex-container uk-flex-wrap');
	$('.wyr-flex-container').children('div').addClass('wyr-flex-item');
    $("div#contenu>p").last().addClass("last-child");
    $("div#contenu>p").next("h2").addClass("uk-margin-remove-top");
    $(".bkg-white>div").not(".left,.right,div[class^='dijit'],:empty").addClass("bkg-grey");
    $(".bkg-white>form").not("div[class^='dijit']").addClass("bkg-grey");
    $(".bkg-grey").prev(".form-contenu.bkg-grey").addClass('uk-margin-remove-bottom');
	$(".saisie-contenu").addClass("astuce");
    $(".astuce>*:first-child, a.aide").addClass("uk-label");
    $('.notice-parent').next("div[id$='Child']").addClass('uk-clearfix');
    $('#contenu>div:has(>.message)').addClass('bkg-white');
    $(".ss-nav-cms-item-expand ").parents(".row.bkg-grey").removeClass("bkg-grey");

    // tab
    $(".hmenu,.sel_navbar,#content_onglet_perio").addClass('uk-tab uk-margin-remove-bottom');
    $(".hmenu .selected, .sel_navbar_current").addClass('uk-active');


    // glyphes
    $('.notice-parent h1 .left>font').prepend('<i class="fa fa-minus" aria-hidden="true"></i>');
    $("#div_alert>*>ul").prepend("<span uk-icon='icon: info; ratio: 1'></span>");
    $("#div_alert>*>ul").addClass("alert-nav");
    $(".icon_history").html("<span uk-icon='icon: historywyr; ratio: 1'></span>");
    $(".icon_help").html("<span uk-icon='icon: question; ratio: 1'></span>");
    $(".icon_param").html("<span uk-icon='icon: cog; ratio: 1'></span>");
    $(".icon_opac").html("<i class='fa fa-globe' aria-hidden='true'></i>");
    $(".icon_sauv").html("<i class='fa fa-floppy-o' aria-hidden='true'></i>");
    $(".icon_quit").html("<span uk-icon='icon: quit; ratio: 1'></span>");
    $("a[title='Tableau de Bord']").html("<span uk-icon='icon: dashboard; ratio: 1'></span>");
    $('#notification').append("<span uk-icon='icon: info; ratio: 1'></span>");
    $("img[alt='basket']").addClass("img-cart-o");
    $("img[alt='basket'],img[src$='basket_small_20x20.gif']").attr("src", "styles/pure/images/basket.svg");
    $("img[alt$='t court']").parent().addClass("icon-table").append("<span uk-icon='icon: clock; ratio: 0.8'></span>");
    $("img[alt$='t court']").remove();
    $("img[src='./images/new.gif']").parent().addClass("icon-table").append("<i class='fa fa-file-pdf-o' aria-hidden='true'></i>");
    $("img[src='./images/new.gif']").remove();
    $("img[src='./images/mail.png']").parent().addClass("icon-table").append("<i class='fa fa-envelope-o' aria-hidden='true'></i>");
    $("img[src='./images/mail.png']").remove();
    $("img[src='./images/error.gif']").parent().addClass("icon-form not").append("<span uk-icon='icon: closeO; ratio: 1'></span>");
    $("img[src='./images/error.gif']").remove();
    $("img[src='./images/sort.png']").parent().addClass("icon-form").append("<i class='fa fa-arrows-v' aria-hidden='true'></i>");
    $("img[src='./images/sort.png']").remove();

    //Glyphes 4 dojo
   // $('.dijitArrowButtonInner').prepend("<span uk-icon='icon: triangle-down; ratio: 0.9'></span>");
    $('dijitButtonNode').addClass('uk-button uk-button-default');

    //notice-parent
   $("form .notice-child").css({
        "width": "100%"
    }).addClass("uncollapse-content uk-overflow-auto");
    $('table.sortable, .tab_sug').parent().addClass('uk-overflow-auto');
	



    //Edition
    $("#contenu>div[id^='procclass']").css({"width": "100%"}).addClass("stat");
    $("#contenu>div[id^='procclass']").next().css({
        "width": "100%"
    }).addClass("stat-child");
    // Table
    $('#contenu>table').addClass("table-bkg");
    $('#contenu table').addClass("uk-table uk-table-small uk-table-striped uk-table-middle");
    $(".stat-child>table").addClass("uk-table uk-table-small uk-table-striped uk-table-middle");
    $('#cms_dragable_cadre').parents("table").addClass("ui-table-Xsmall");
    
	
	//Circulation
	$('#empr_form_actions_buttons').next('.right').addClass('empr_form_actions');
    //CMS
    $(".ui-expand-container>div[onclick*='expandBase']").addClass("uk-accordion-title ui-small-title");
    /*Grid -------*/
    $("#conteneur.cms>#contenu").addClass("uk-grid");
    $("#conteneur.cms>#contenu>h1,#conteneur.cms>#contenu.uk-grid>h3").addClass("uk-width-1-1");
    $("#conteneur.cms>#contenu>.colonne3").addClass("uk-width-1-3");
    $("#conteneur.cms>#contenu>.colonne-suite").addClass("uk-width-3-3");
    
    /*END  -------*/
	
    //Form
    $("div[id^='drag']").addClass("wyr-draggable");
    $("input[type='button'],div[id^='dijit_layout_ContentPane']>a").addClass("uk-button uk-button-default uk-button-small wyr-custom");
    // w mid
    $("input[type='text']").not('.dijitInputInner').addClass("uk-input uk-form-width-medium");
    // w small
    $("select").addClass("uk-select uk-form-width-medium uk-form-small");
    // w Xsmall
	$("input[maxlength='4'],input[value='X'],input[value='...'],input[name='limite_page'],input[name='nb_per_page'],form[name='navbar'] input[type='text']").addClass("uk-input uk-form-width-xsmall");
    $("form .form-contenu select").removeClass('uk-form-width-large').addClass("uk-form-width-medium wyr-form-width-custom");
    $("textarea").addClass("uk-textarea");
    //input
    $("input[type='submit']").addClass("uk-button uk-button-default uk-button-small");
    $("input[type='radio']").addClass("uk-radio");
    $("input[type='checkbox']").addClass("uk-checkbox");
    $("input.bouton, .bdd_updade_link").addClass("uk-button uk-button-default uk-button-small");
    $(".form-retour-expl input[type='text']").addClass("uk-form-width-medium");
	$("input.saisie-80em,input.saisie-30em,input.saisie-40em ,input.saisie-50emr, input.saisie-80emr, input[size='36']").addClass("uk-form-width-large");
    $(".etiquette").addClass("uk-form-label wyr-form-label");
    $(".form-contenu label").not(".etiquette").addClass("uk-form-label wyr-form-label");
    $("form .form-contenu .colonne_suite.uk-flex-auto input:not('.uk-radio,.uk-checkbox,.uk-button')").removeClass('uk-form-width-medium').addClass("uk-width-5-6");
    $("form .form-contenu .colonne_suite.wyr-flex-item input:not('.uk-radio,.uk-checkbox,.uk-button')").removeClass('uk-form-width-medium').addClass("uk-width-5-6");
	$("form[name='sug_modif_form'] .colonne5 input[type='text']").removeClass("uk-form-width-medium ").addClass("uk-form-width-small");

    //Form colored
    $("#contenu>form").first("form").addClass("first-form-page");
    $(".left").parent(".first-form-page").addClass("form-bkg-colored");
    $("#contenu>form").last("form").addClass("last-form-page");
    $("form[class^='form']").nextAll("form[class^='form']").not('.last-form-page').addClass("uk-padding-remove-top");
    $(".left").parent(".last-form-page").addClass("form-bkg-colored");
    $("#contenu input").not(".uk-radio, .uk-checkbox").addClass("wyr-input");
    $("#contenu input.wyr-input[type='file']").addClass("uk-width-3-6");



    // table
    // if link no surbrillance
    $("table a").parents("tr").attr({
        'onmouseover': null,
        'onmouseout': null
    });
	$('table h3').parents('tr').addClass('actions-thead');


    $("#menu>ul").addClass("uk-nav");
    $("#menu>ul>li").addClass("");
    $("#menu>ul>li>ul").addClass("uk-nav-sub");
    $("#contenu>h1,#contenu>.row>h1,#make_mul_sugg>h1,#import_sug>h1").first().addClass("section-title");
    $("#contenu>h1").not(".section-title").addClass("section-sub-title");
    $("#contenu>*>h3").addClass("h2-like section-sub-title");
    $("#contenu>h3").addClass("h2-like section-sub-title");
    $("#contenu *>h3").addClass("article-title");
    $("script").parent("p").addClass("uk-margin-remove");

    // Sticky part
    if ($('#extra').length == 1){
        var stickyDelay = 1;
        var widthInitExtra = window.getComputedStyle(document.getElementById('extra')).width;
        var divInitWidthExtra = document.createElement('div');
        divInitWidthExtra.setAttribute('style','width:'+widthInitExtra);
        divInitWidthExtra.setAttribute('id','initW');
        var extra = document.getElementById('extra');
        extra.insertBefore(divInitWidthExtra, extra.childNodes[0]);
        document.getElementById("navbar").setAttribute('style','padding-right:'+widthInitExtra);

        // add event
        UIkit.sticky('#navbar', {
                top:stickyDelay,
                offset: 0,
                showOnUp: true,
                //animation: "uk-animation-slide-top",
        });
        UIkit.sticky('#extra', {
                top:stickyDelay,
                offset: 0,
                showOnUp: true,
                widthElement:'#initW',

        });
    }
    /*
	 UIkit.sticky('#extra2', {
		 	top:stickyDelay,
			offset: 0,

			widthElement:".sticky-placeholder-notif"
    });	
    */
    // Auto margin :)
    var ukMargin = $(".left").has("input");
    UIkit.margin(ukMargin, {
        margin:'uk-margin-small-top',
    });
    var ukMarginChild = $("div[id*='Child'][id^='el']");
    UIkit.margin(ukMarginChild, {
        margin:'uk-margin-small-top',
    });
    var ukMarginConcept = $("div[id^='concept']");
    UIkit.margin(ukMarginConcept, {
        margin:'uk-margin-small-top',
    });
    UIkit.margin($('.uk-flex-wrap'), {
        margin:'uk-margin-small-top',
    });
    UIkit.margin($('.input-in-td'), {
        margin:'uk-margin-small-top',
    });   
    UIkit.margin($('.td-border-display>div'), {
        margin:'uk-margin-small-top',
    });   
    $("br").next(".row").addClass("uk-margin-small-top");
    
    //*END 
    // Clearfix	
    //$(".left").not(":empty").addClass("uk-clearfix");
    $("form[name='navbar'],.right,.form-contenu,.bkg-white,div[id^='elconn']").addClass("uk-clearfix");

    $("#menu>h3").prepend("<span class='uk-margin-small-right uk-icon'><i class='fa fa-caret-down' aria-hidden='true'></i></span>");
    $("#menu .uk-nav>li>a").prepend("<span class='uk-margin-small-right uk-icon'><i class='fa fa-circle-o' aria-hidden='true'></i></span>");
    $("#contenu>h1,#contenu>.row>h1,#make_mul_sugg>h1,#import_sug>h1").first().prepend("<span class='uk-margin-small-right uk-icon'><i class='fa fa-circle' aria-hidden='true'></i></span>");
    $("#contenu>h1,#make_mul_sugg>h1").not(".section-title").prepend("<span class='w-margin-small-right '><i class='fa fa-minus fa-rotate-90' aria-hidden='true'></i></span>");
    $("#contenu>h2.bkg-white,#contenu>.row.bkg-white>h2").not(":empty").prepend("<span class='w-margin-small-right '><i class='fa fa-minus fa-rotate-90' aria-hidden='true'></i></span>");
    $("#contenu>*>h3").not(".section-record-title").prepend("<span class='w-margin-small-right '><i class='fa fa-minus fa-rotate-90' aria-hidden='true'></i></span>");
    $(".cms_editorial_form>h3").prepend("<span class='w-margin-small-right '><i class='fa fa-minus fa-rotate-90' aria-hidden='true'></i></span>");
	$("#contenu>h3").prepend("<span class='w-margin-small-right '><i class='fa fa-minus fa-rotate-90' aria-hidden='true'></i></span>");
	$('.article-title:only-child').addClass('uk-margin-remove-bottom');


    var rows = document.querySelectorAll('div[class^="row"]');
    rows = Array.prototype.slice.call(document.querySelectorAll('div[class^="row"]')).concat(Array.prototype.slice.call(document.querySelectorAll('div[class^="colonne"]')));
    rows.forEach(function(row) {
        if (row.innerHTML.trim() == "&nbsp;") {
            row.parentNode.removeChild(row)
        }
    });

    $(".empty-node").parent(".row").addClass('has-child-empty');
	$('span.erreur:empty').parent().parent('.bkg-grey').addClass('wyr-empty-box');
	$('span.erreur:empty').parent().parent('.bkg-grey').parent().addClass('wyr-empty-box-display');
    $("#navbar").addClass("uk-navbar-container uk-navbar-left");
    $("#navbar>ul").addClass("uk-navbar-nav");
    $("#extra").addClass("uk-iconnav");
	$("#extra2").addClass("uk-iconnav");
	// add html
	$( ".wyr-empty-box.erreur" ).wrap( "<div class='row bkg-white'></div>" );
	$('.left:only-child').css("float","none");
	// Remove class just add
	$('.wyr-empty-box').removeClass('bkg-grey');
	$("div[class^='dijit']").removeClass('wyr-empty-box');
	
	//  tableau cree en div fixe a la taille de la plus grande cellule
	var cells = document.getElementsByClassName("dom_cell2");
	var size = 0; 
	for(var i=0 ; i<cells.length ; i++){ 
		if(parseInt(window.getComputedStyle(cells[i]).height.replace('px', '')) > size){ 
			size = window.getComputedStyle(cells[i]).height.replace('px', '');
		}
	}
	var rows = document.getElementsByClassName("dom_row2");
	for(var i=0 ; i<rows.length ; i++){
		rows[i].style.setProperty('height', size+'px');
	}	
    $("body").addClass("pure ready");
});