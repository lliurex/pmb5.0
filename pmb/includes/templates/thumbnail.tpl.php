<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: thumbnail.tpl.php,v 1.2 2017-05-18 11:02:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

$js_function_chklnk_tpl = "
<script type='text/javascript'>
function chklnk_f_thumbnail_url(element){
	if(element.value != ''){
		var url=element.value;
		var thisRegex = new RegExp('^[a-zA-Z0-9_]+\.php','g');
		var flagPhp=false;
		if(thisRegex.test(url)){
			url = '".(isset($pmb_url_base) ? $pmb_url_base : '')."/'+url;
			flagPhp=true;
		}
		var wait = document.createElement('img');
		wait.setAttribute('src','images/patience.gif');
		wait.setAttribute('align','top');
		while(document.getElementById('f_thumbnail_check').firstChild){
			document.getElementById('f_thumbnail_check').removeChild(document.getElementById('f_thumbnail_check').firstChild);
		}
		document.getElementById('f_thumbnail_check').appendChild(wait);
		var testlink = encodeURIComponent(url);
		var req = new XMLHttpRequest();
		req.open('GET', './ajax.php?module=ajax&categ=chklnk&timeout=!!pmb_curl_timeout!!&link='+testlink, true);
		req.onreadystatechange = function (aEvt) {
		  if (req.readyState == 4) {
		  	if(req.status == 200){
				var img = document.createElement('img');
			    var src='';
			    if(req.responseText == '200'){
					if(!flagPhp){
			    		if((element.value.substr(0,7) != 'http://') && (element.value.substr(0,8) != 'https://')) element.value = 'http://'+element.value;
					}
					//impec, on print un petit message de confirmation
					src = 'images/tick.gif';
				}else{
			      //problème...
					src = 'images/error.png';
					img.setAttribute('style','height:1.5em;');
			    }
			    img.setAttribute('src',src);
				img.setAttribute('align','top');
				while(document.getElementById('f_thumbnail_check').firstChild){
					document.getElementById('f_thumbnail_check').removeChild(document.getElementById('f_thumbnail_check').firstChild);
				}
				document.getElementById('f_thumbnail_check').appendChild(img);
			}
		  }
		};
		req.send(null);
	}
}
</script>";