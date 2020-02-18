<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_ayudas.inc.php,v 1.57.2.3 2017-12-26 15:07:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($form_cb)) $form_cb = '';
if(!isset($grant_period)) $grant_period = '';
if (!isset($cod_centro)) $cod_centro='';
if(!isset($limite_page)) $limite_page = '';
if(!isset($page)) $page = 0;
if(!isset($numero_page)) $numero_page = '';

require_once ($class_path.'/emprunteur.class.php');
//Récupération des variables postées, on en aura besoin pour les liens
$page_url="./edit.php";

function check_codcentro($codcentro){
	
	$error=False;
	if (strlen($codcentro)<8){
		$error=True;
	}else{
		$init_code=substr($codcentro,0,2);
		switch($init_code){
			case "46":
			case "12":
				$result=is_numeric($codcentro);
				if(!$result){
					$error=True;
				}
				break;
			case "03":
				$temp_code = ltrim($codcentro, '0');
				$result=is_numeric($temp_code);
				if(!$result){
					$error=True;
				}
				break;
			default:
				$error=True;
				break;
		}
	}
	
	return $error;

}

function search_empty_fields($convo){
	
	if (($convo->identification=="")||($convo->lang=="")||($convo->female_author=="")||($convo->literary_work=="")||($convo->price=="")||($convo->location=="")){
		return 1;
	}	
	return 0;

}
switch($dest) {
	case "TABLEAU":
		$filename = "convo_list.xml";
		$doc = new DOMDocument('1.0', 'ISO-8859-1');
		$doc->formatOutput = false;
		break;
	case "TABLEAUHTML":
		echo "<h1>".$titre_page."</h1>" ;  
		break;
	default:
		echo "<h1>".$titre_page."</h1>" ;
		break;
}

if ($cod_centro!= ""){
// nombre de références par pages
	$check=check_codcentro($cod_centro);
	if($check==1){
		switch($dest) {
			case "TABLEAU":
				break;
			case "TABLEAUHTML":
				break;
			default:
				echo "
					<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$page_url?categ=$categ&sub=$sub&limite_page=$limite_page&numero_page=$numero_page&cod_centro=$cod_centro' method=post>
					<div class='left'>
						$nav_bar $msg[circ_afficher] $msg[1905] &nbsp <input type=text name=limite_page value='$limite_page' class='saisie-5em'>&nbsp&nbsp$msg[notices_convo_codcentro]&nbsp<input type=text name=cod_centro value='$cod_centro' class='saisie-5em' maxlength='9'>&nbsp&nbsp$msg[notices_convo_year]&nbsp
					</div>
					<div class='right'>
						<img  src='./images/mimetype/xml-dist.png' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='Export tableau EXCEL' title='Export fichier XML'/>&nbsp;&nbsp;
						<img  src='./images/tableur_html.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='Export tableau HTML' title='Export tableau HTML'/>&nbsp;&nbsp;
					</div>
					<script type='text/javascript'>
						function survol(obj){
							obj.style.cursor = 'pointer';
						}
						function start_export(type){
							document.forms['form-$current_module-list'].dest.value = type;
							document.forms['form-$current_module-list'].submit();
						}	
					</script>
				";
						
				echo gen_liste("select el.expl_custom_champ, el.expl_custom_list_lib from expl_custom_lists el left join expl_custom ec on  el.expl_custom_champ=ec.idchamp where ec.name='Convocatoria' order by expl_custom_list_lib","el.expl_custom_list_lib","el.expl_custom_list_lib","grant_period","",$grant_period,-1,"",0,$msg["select_grant_period"]);
				echo "&nbsp;<input type='submit' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.dest.value='';\" />&nbsp;&nbsp;<input type='hidden' name='dest' value='' />";
				echo "
					<div class='row'></div></form><br />";
				echo "
				<div class='row'></div></form>";
				error_message($msg['grant_period_search'], str_replace('!!form_cb!!', $form_cb, $msg['notices_convo_codcentro_error']), 1, './edit.php?categ=books&sub='.$sub);
		}
		break;		
	}
	if ($nb_per_page_empr != "") 
		$nb_per_page = $nb_per_page_empr ;
	else 
		$nb_per_page = 10;

	$restrict_convo="";
	if ($grant_period) {
		
		if ($grant_period!=0) {
			$restrict_convo= " where t.Convo='$grant_period' group by t.registre_id";
		}else{ 
			$restrict_convo = "where t.Convo='2018' group by t.registre_id";
		}	
	} else{
			$restrict_convo=" where t.Convo='2018' group by t.registre_id";
	}

	// on récupére le nombre de lignes 
	$requete='SELECT registre_id,tit1,id_type,identification,id,id_lang,lang,female_author,literary_work,price,if (sum(num)>5,5,sum(num)) as num,Convo,id_location,location,0 as isnotice from 
	(SELECT n.notice_id as registre_id,SUBSTRING(n.tit1, 1, 99) as tit1, if (c1.notices_custom_small_text="N/A" or isnull(c1.notices_custom_small_text),"",c1.notices_custom_small_text) as id_type, if(c1.notices_custom_small_text="N/A" or isnull(c1.notices_custom_small_text) ,"",(Select notices_custom_list_lib from notices_custom_lists where notices_custom_list_value=c1.notices_custom_small_text and c2.idchamp=notices_custom_champ )) as identification, 
	if(n.code!="",substring(n.code,1,33),n.code) as id,if(c3.notices_custom_small_text="N/A" or isnull(c3.notices_custom_small_text),"",c3.notices_custom_small_text) as id_lang, if(c3.notices_custom_small_text="N/A" or isnull(c3.notices_custom_small_text),"",(Select notices_custom_list_lib from notices_custom_lists where notices_custom_list_value=c3.notices_custom_small_text and c4.idchamp=notices_custom_champ )) as lang,
	    if (c5.notices_custom_small_text="N/A" or isnull(c5.notices_custom_small_text),"",c5.notices_custom_small_text) as female_author, if (c7.notices_custom_small_text="N/A" or isnull(c7.notices_custom_small_text),"",c7.notices_custom_small_text) as literary_work, format(c9.notices_custom_float,2) as price,
		if(temp.Count >= 5,5,temp.Count) as num,temp.Convo,if (c11.notices_custom_small_text="N/A" or isnull(c11.notices_custom_small_text),"",c11.notices_custom_small_text) as id_location, 
	    if(c11.notices_custom_small_text="N/A" or isnull(c11.notices_custom_small_text),"",(Select notices_custom_list_lib 
	    from notices_custom_lists where notices_custom_list_value=c11.notices_custom_small_text and c12.idchamp=notices_custom_champ )) as location 
	    from notices n 
	    left join notices_custom_values c1 on n.notice_id=c1.notices_custom_origine left join notices_custom c2 on c2.idchamp=c1.notices_custom_champ
	    left join notices_custom_values c3 on n.notice_id=c3.notices_custom_origine left join notices_custom c4 on c4.idchamp=c3.notices_custom_champ
		left join notices_custom_values c5 on n.notice_id=c5.notices_custom_origine left join notices_custom c6 on c6.idchamp=c5.notices_custom_champ 
	    left join notices_custom_values c7 on n.notice_id=c7.notices_custom_origine left join notices_custom c8 on c8.idchamp=c7.notices_custom_champ
	    left join notices_custom_values c9 on n.notice_id=c9.notices_custom_origine left join notices_custom c10 on c10.idchamp=c9.notices_custom_champ
	    left join notices_custom_values c11 on n.notice_id=c11.notices_custom_origine left join notices_custom c12 on c12.idchamp=c11.notices_custom_champ 
	    left join
		(SELECT e.expl_notice as id, COUNT(e.expl_notice) AS Count, e1.expl_custom_small_text COLLATE utf8_general_ci as Convo FROM exemplaires e 
		left join expl_custom_values e1 on e1.expl_custom_origine=e.expl_id left join expl_custom e2 on e2.idchamp=e1.expl_custom_champ 
		where e1.expl_custom_small_text!=0 and e2.name="Convocatoria" 
	    GROUP BY e.expl_notice,e1.expl_custom_small_text UNION ALL 
	    SELECT en.explnum_notice as id, COUNT(en.explnum_notice) AS Count, e1n.explnum_custom_small_text COLLATE utf8_general_ci as Convo FROM explnum en 
		left join explnum_custom_values e1n on e1n.explnum_custom_origine=en.explnum_id left join explnum_custom en2 on en2.idchamp=e1n.explnum_custom_champ 
		where e1n.explnum_custom_small_text!=0 and en2.name="Convocatoria" 
	    GROUP BY en.explnum_notice,e1n.explnum_custom_small_text ) temp ON temp.id = n.notice_id 
		where c2.name="Identificacion" and c4.name="Idioma" and c6.name="Autoria" and c8.name="Literaria" and c10.name="Precio" and c12.name="Ubicacion" and temp.Count>0) t';

	$requete_unionall2=' union all 
		SELECT registre_id,tit1,id_type,identification,id,id_lang,lang,female_author,literary_work,price,if (sum(num)>5,5,sum(num)) as num,Convo,id_location,location,0 as isnotice from 
		(
		SELECT n.notice_id as registre_id,SUBSTRING(n.tit1, 1, 99) as tit1,"" as id_type,"" as identification, if(n.code!="",substring(n.code,1,33),n.code) as id, "" as id_lang, ""as lang,"" as female_author, "" as literary_work, "" as price, if(temp.Count >= 5,5,temp.Count) 
		as num,temp.Convo,"" as id_location,"" as location from notices n  
		left join (SELECT e.expl_notice as id, COUNT(e.expl_notice) AS Count, e1.expl_custom_small_text COLLATE utf8_general_ci as Convo FROM exemplaires e 
		left join expl_custom_values e1 on e1.expl_custom_origine=e.expl_id left join expl_custom e2 on e2.idchamp=e1.expl_custom_champ 
		where e1.expl_custom_small_text!=0 and e2.name="Convocatoria" 
	    GROUP BY e.expl_notice,e1.expl_custom_small_text UNION ALL 
	    SELECT en.explnum_notice as id, COUNT(en.explnum_notice) AS Count, e1n.explnum_custom_small_text COLLATE utf8_general_ci as Convo FROM explnum en 
		left join explnum_custom_values e1n on e1n.explnum_custom_origine=en.explnum_id left join explnum_custom en2 on en2.idchamp=e1n.explnum_custom_champ 
		where e1n.explnum_custom_small_text!=0 and en2.name="Convocatoria" 
	    GROUP BY en.explnum_notice,e1n.explnum_custom_small_text ) temp ON temp.id = n.notice_id 
		where temp.Count>0 and not exists (Select c3.notices_custom_origine from notices_custom_values c3 left join 
		notices_custom c4 on c4.idchamp=c3.notices_custom_champ where c3.notices_custom_origine=n.notice_id and c3.notices_custom_origine=n.notice_id
		and c4.name in("Identificacion","Idioma","Autoria","Literaria","Precio","Ubicacion"))) t';

	$requete_unionall3=' union all
		SELECT registre_id,tit1,id_type,identification,id,id_lang,lang,female_author,literary_work,price,if (sum(num)>5,5,sum(num)) as num,Convo,id_location,location,0 as isnotice from 
	(SELECT n.notice_id as registre_id,SUBSTRING(n.tit1, 1, 99) as tit1, if (c1.notices_custom_small_text="N/A" or isnull(c1.notices_custom_small_text),"",c1.notices_custom_small_text) as id_type, if(c1.notices_custom_small_text="N/A" or isnull(c1.notices_custom_small_text) ,"",(Select notices_custom_list_lib from notices_custom_lists where notices_custom_list_value=c1.notices_custom_small_text and c2.idchamp=notices_custom_champ )) as identification, if(n.code!="",substring(n.code,1,33),n.code) as id,
		if(c3.notices_custom_small_text="N/A" or isnull(c3.notices_custom_small_text),"",c3.notices_custom_small_text) as id_lang, if(c3.notices_custom_small_text="N/A" or isnull(c3.notices_custom_small_text),"",(Select notices_custom_list_lib from notices_custom_lists where notices_custom_list_value=c3.notices_custom_small_text and c4.idchamp=notices_custom_champ )) as lang,
	    if (c5.notices_custom_small_text="N/A" or isnull(c5.notices_custom_small_text),"",c5.notices_custom_small_text) as female_author, if (c7.notices_custom_small_text="N/A" or isnull(c7.notices_custom_small_text),"",c7.notices_custom_small_text) as literary_work, "" as price,
		if(temp.Count >= 5,5,temp.Count) as num,temp.Convo,if (c11.notices_custom_small_text="N/A" or isnull(c11.notices_custom_small_text),"",c11.notices_custom_small_text) as id_location, 
	    if(c11.notices_custom_small_text="N/A" or isnull(c11.notices_custom_small_text),"",(Select notices_custom_list_lib 
	    from notices_custom_lists where notices_custom_list_value=c11.notices_custom_small_text and c12.idchamp=notices_custom_champ )) as location 
	    from notices n 
	    left join notices_custom_values c1 on n.notice_id=c1.notices_custom_origine left join notices_custom c2 on c2.idchamp=c1.notices_custom_champ
	    left join notices_custom_values c3 on n.notice_id=c3.notices_custom_origine left join notices_custom c4 on c4.idchamp=c3.notices_custom_champ
		left join notices_custom_values c5 on n.notice_id=c5.notices_custom_origine left join notices_custom c6 on c6.idchamp=c5.notices_custom_champ 
	    left join notices_custom_values c7 on n.notice_id=c7.notices_custom_origine left join notices_custom c8 on c8.idchamp=c7.notices_custom_champ
	    left join notices_custom_values c11 on n.notice_id=c11.notices_custom_origine left join notices_custom c12 on c12.idchamp=c11.notices_custom_champ 
	    left join
		(SELECT e.expl_notice as id, COUNT(e.expl_notice) AS Count, e1.expl_custom_small_text COLLATE utf8_general_ci as Convo FROM exemplaires e 
		left join expl_custom_values e1 on e1.expl_custom_origine=e.expl_id left join expl_custom e2 on e2.idchamp=e1.expl_custom_champ 
		where e1.expl_custom_small_text!=0 and e2.name="Convocatoria" 
	    GROUP BY e.expl_notice,e1.expl_custom_small_text UNION ALL 
	    SELECT en.explnum_notice as id, COUNT(en.explnum_notice) AS Count, e1n.explnum_custom_small_text COLLATE utf8_general_ci as Convo FROM explnum en 
		left join explnum_custom_values e1n on e1n.explnum_custom_origine=en.explnum_id left join explnum_custom en2 on en2.idchamp=e1n.explnum_custom_champ 
		where e1n.explnum_custom_small_text!=0 and en2.name="Convocatoria" 
	    GROUP BY en.explnum_notice,e1n.explnum_custom_small_text ) temp ON temp.id = n.notice_id 
		where c2.name="Identificacion" and c4.name="Idioma" and c6.name="Autoria" and c8.name="Literaria" and c12.name="Ubicacion" and temp.Count>0
        and n.notice_id not in (Select c9.notices_custom_origine from notices_custom_values c9 left join notices n on n.notice_id=c9.notices_custom_origine left join notices_custom c10 on c10.idchamp=c9.notices_custom_champ where
        c10.name="Precio"))t';
	
	$requeteb=' union all 
	SELECT registre_id,tit1,id_type,identification,id,id_lang,lang,female_author,literary_work,price,if (sum(num)>5,5,sum(num)) as num,Convo,id_location,location,1 as isnotice from 
	(SELECT b.bulletin_id as registre_id,SUBSTRING(concat(b.bulletin_numero,"-",b.bulletin_titre),1,99) as tit1, if(c1.notices_custom_small_text="N/A" or isnull(c1.notices_custom_small_text),"",c1.notices_custom_small_text) as id_type, if(c1.notices_custom_small_text="N/A","",(Select notices_custom_list_lib from notices_custom_lists where notices_custom_list_value=c1.notices_custom_small_text and c2.idchamp=notices_custom_champ )) as identification, if(b.bulletin_cb!="",substring(b.bulletin_cb,1,33),b.bulletin_cb) as id,
		if(c3.notices_custom_small_text="N/A" or isnull(c3.notices_custom_small_text),"",c3.notices_custom_small_text) as id_lang, if(c3.notices_custom_small_text="N/A","",(Select notices_custom_list_lib from notices_custom_lists where notices_custom_list_value=c3.notices_custom_small_text and c4.idchamp=notices_custom_champ )) as lang,
	    if (c5.notices_custom_small_text="N/A" or isnull(c5.notices_custom_small_text),"",c5.notices_custom_small_text) as female_author, if(c7.notices_custom_small_text="N/A" or isnull(c7.notices_custom_small_text),"",c7.notices_custom_small_text) as literary_work, format(c9.notices_custom_float,2) as price,
		if(temp.Count >= 5,5,temp.Count) as num,temp.Convo,if(c11.notices_custom_small_text="N/A" or isnull(c11.notices_custom_small_text),"",c11.notices_custom_small_text) as id_location, 
	    if(c11.notices_custom_small_text="N/A","",(Select notices_custom_list_lib 
	    from notices_custom_lists where notices_custom_list_value=c11.notices_custom_small_text and c12.idchamp=notices_custom_champ )) as location 
	    from bulletins b left join notices n on b.num_notice=n.notice_id 
	    left join notices_custom_values c1 on n.notice_id=c1.notices_custom_origine left join notices_custom c2 on c2.idchamp=c1.notices_custom_champ
	    left join notices_custom_values c3 on n.notice_id=c3.notices_custom_origine left join notices_custom c4 on c4.idchamp=c3.notices_custom_champ
		left join notices_custom_values c5 on n.notice_id=c5.notices_custom_origine left join notices_custom c6 on c6.idchamp=c5.notices_custom_champ 
	    left join notices_custom_values c7 on n.notice_id=c7.notices_custom_origine left join notices_custom c8 on c8.idchamp=c7.notices_custom_champ
	    left join notices_custom_values c9 on n.notice_id=c9.notices_custom_origine left join notices_custom c10 on c10.idchamp=c9.notices_custom_champ
	    left join notices_custom_values c11 on n.notice_id=c11.notices_custom_origine left join notices_custom c12 on c12.idchamp=c11.notices_custom_champ 
	    left join
		(SELECT e.expl_bulletin as id, COUNT(e.expl_bulletin) AS Count, e1.expl_custom_small_text COLLATE utf8_general_ci as Convo FROM exemplaires e 
		left join expl_custom_values e1 on e1.expl_custom_origine=e.expl_id left join expl_custom e2 on e2.idchamp=e1.expl_custom_champ 
		where e1.expl_custom_small_text!=0 and e2.name="Convocatoria" 
	    GROUP BY e.expl_bulletin,e1.expl_custom_small_text UNION ALL 
	    SELECT en.explnum_bulletin as id, COUNT(en.explnum_bulletin) AS Count, e1n.explnum_custom_small_text COLLATE utf8_general_ci as Convo FROM explnum en 
		left join explnum_custom_values e1n on e1n.explnum_custom_origine=en.explnum_id left join explnum_custom en2 on en2.idchamp=e1n.explnum_custom_champ 
		where e1n.explnum_custom_small_text!=0 and en2.name="Convocatoria" 
	    GROUP BY en.explnum_bulletin,e1n.explnum_custom_small_text ) temp ON temp.id = b.bulletin_id 
		where c2.name="Identificacion" and c4.name="Idioma" and c6.name="Autoria" and c8.name="Literaria" and c10.name="Precio" and c12.name="Ubicacion" and temp.Count>0) t';


	$requeteb_unionall2=' union all 
		SELECT registre_id,tit1,id_type,identification,id,id_lang,lang,female_author,literary_work,price,if (sum(num)>5,5,sum(num)) as num,Convo,id_location,location,1 as isnotice from 
		(
		SELECT b.bulletin_id as registre_id,SUBSTRING(concat(b.bulletin_numero,"-",b.bulletin_titre),1,99) as tit1,"" as id_type,"" as identification, if(b.bulletin_cb!="",substring(b.bulletin_cb,1,33),b.bulletin_cb) as id, "" as id_lang, ""as lang,"" as female_author, "" as literary_work, "" as price, if(temp.Count >= 5,5,temp.Count) 
		as num,temp.Convo,"" as id_location,"" as location from bulletins b left join notices n on b.num_notice=n.notice_id left join
		(SELECT e.expl_bulletin as id, COUNT(e.expl_bulletin) AS Count, e1.expl_custom_small_text COLLATE utf8_general_ci as Convo FROM exemplaires e 
		left join expl_custom_values e1 on e1.expl_custom_origine=e.expl_id left join expl_custom e2 on e2.idchamp=e1.expl_custom_champ 
		where e1.expl_custom_small_text!=0 and e2.name="Convocatoria" 
	    GROUP BY e.expl_bulletin,e1.expl_custom_small_text UNION ALL 
	    SELECT en.explnum_bulletin as id, COUNT(en.explnum_bulletin) AS Count, e1n.explnum_custom_small_text COLLATE utf8_general_ci as Convo FROM explnum en 
		left join explnum_custom_values e1n on e1n.explnum_custom_origine=en.explnum_id left join explnum_custom en2 on en2.idchamp=e1n.explnum_custom_champ 
		where e1n.explnum_custom_small_text!=0 and en2.name="Convocatoria" 
	    GROUP BY en.explnum_bulletin,e1n.explnum_custom_small_text ) temp ON temp.id = b.bulletin_id 
		where temp.Count>0 and not exists (Select c3.notices_custom_origine from notices_custom_values c3 left join 
		notices_custom c4 on c4.idchamp=c3.notices_custom_champ where c3.notices_custom_origine=n.notice_id and c3.notices_custom_origine=n.notice_id
		and c4.name in("Identificacion","Idioma","Autoria","Literaria","Precio","Ubicacion"))) t';
		
	$requeteb_unionall3=' union all
		SELECT registre_id,tit1,id_type,identification,id,id_lang,lang,female_author,literary_work,price,if (sum(num)>5,5,sum(num)) as num,Convo,id_location,location,1 as isnotice from 
		(SELECT b.bulletin_id as registre_id,SUBSTRING(concat(b.bulletin_numero,"-",b.bulletin_titre),1,99)  as tit1, if(c1.notices_custom_small_text="N/A" or isnull(c1.notices_custom_small_text),"",c1.notices_custom_small_text) as id_type, if(c1.notices_custom_small_text="N/A","",(Select notices_custom_list_lib from notices_custom_lists where notices_custom_list_value=c1.notices_custom_small_text and c2.idchamp=notices_custom_champ )) as identification, if(b.bulletin_cb!="",substring(b.bulletin_cb,1,33),b.bulletin_cb) as id,
		if(c3.notices_custom_small_text="N/A" or isnull(c3.notices_custom_small_text),"",c3.notices_custom_small_text) as id_lang, if(c3.notices_custom_small_text="N/A","",(Select notices_custom_list_lib from notices_custom_lists where notices_custom_list_value=c3.notices_custom_small_text and c4.idchamp=notices_custom_champ )) as lang,
	    if (c5.notices_custom_small_text="N/A" or isnull(c5.notices_custom_small_text),"",c5.notices_custom_small_text) as female_author, if(c7.notices_custom_small_text="N/A" or isnull(c7.notices_custom_small_text),"",c7.notices_custom_small_text) as literary_work, ""as price,
		if(temp.Count >= 5,5,temp.Count) as num,temp.Convo,if(c11.notices_custom_small_text="N/A" or isnull(c11.notices_custom_small_text),"",c11.notices_custom_small_text) as id_location, 
	    if(c11.notices_custom_small_text="N/A","",(Select notices_custom_list_lib 
	    from notices_custom_lists where notices_custom_list_value=c11.notices_custom_small_text and c12.idchamp=notices_custom_champ )) as location 
	    from bulletins b left join notices n on b.num_notice=n.notice_id 
	    left join notices_custom_values c1 on n.notice_id=c1.notices_custom_origine left join notices_custom c2 on c2.idchamp=c1.notices_custom_champ
	    left join notices_custom_values c3 on n.notice_id=c3.notices_custom_origine left join notices_custom c4 on c4.idchamp=c3.notices_custom_champ
		left join notices_custom_values c5 on n.notice_id=c5.notices_custom_origine left join notices_custom c6 on c6.idchamp=c5.notices_custom_champ 
	    left join notices_custom_values c7 on n.notice_id=c7.notices_custom_origine left join notices_custom c8 on c8.idchamp=c7.notices_custom_champ
	    left join notices_custom_values c11 on n.notice_id=c11.notices_custom_origine left join notices_custom c12 on c12.idchamp=c11.notices_custom_champ 
	    left join
		(SELECT e.expl_bulletin as id, COUNT(e.expl_bulletin) AS Count, e1.expl_custom_small_text COLLATE utf8_general_ci as Convo FROM exemplaires e 
		left join expl_custom_values e1 on e1.expl_custom_origine=e.expl_id left join expl_custom e2 on e2.idchamp=e1.expl_custom_champ 
		where e1.expl_custom_small_text!=0 and e2.name="Convocatoria" 
	    GROUP BY e.expl_bulletin,e1.expl_custom_small_text UNION ALL 
	    SELECT en.explnum_bulletin as id, COUNT(en.explnum_bulletin) AS Count, e1n.explnum_custom_small_text COLLATE utf8_general_ci as Convo FROM explnum en 
		left join explnum_custom_values e1n on e1n.explnum_custom_origine=en.explnum_id left join explnum_custom en2 on en2.idchamp=e1n.explnum_custom_champ 
		where e1n.explnum_custom_small_text!=0 and en2.name="Convocatoria" 
	    GROUP BY en.explnum_bulletin,e1n.explnum_custom_small_text ) temp ON temp.id = b.bulletin_id 
		where c2.name="Identificacion" and c4.name="Idioma" and c6.name="Autoria" and c8.name="Literaria" and c12.name="Ubicacion" and temp.Count>0
	 and n.notice_id not in (Select c9.notices_custom_origine from notices_custom_values c9 left join notices n on n.notice_id=c9.notices_custom_origine left join notices_custom c10 on c10.idchamp=c9.notices_custom_champ where
        c10.name="Precio"))t';	

	if(!isset($nbr_lignes)) {
		
		if ($grant_period) {
			if ($grant_period!=0) {
				$restrict_convo= " where t.Convo='$grant_period' group by t.registre_id";
			}else{ 
				$restrict_convo =" where t.Convo='2018' group by t.registre_id";
			}	
		} else{
			$restrict_convo=" where t.Convo='2018' group by t.registre_id";
		}


		$res = @mysql_query($requete.$restrict_convo.$requete_unionall2.$restrict_convo.$requete_unionall3.$restrict_convo.$requeteb.$restrict_convo.$requeteb_unionall2.$restrict_convo.$requeteb_unionall3.$restrict_convo,$dbh);
		$nbr_lignes = mysql_num_rows($res);
	}

	//Si aucune limite_page n'a été passée, valeur par défaut $nb_per_page
	if (!$limite_page) 
		$limite_page = $nb_per_page;
	else 
		$nb_per_page = $limite_page;

	$nbpages= $nbr_lignes / $limite_page;

	if(!$page) $page=1;

	$debut =($page-1)*$nb_per_page;

	if($nbr_lignes) {
		
		$requete =  $requete.$restrict_convo.$requete_unionall2.$restrict_convo.$requete_unionall3.$restrict_convo.$requeteb.$restrict_convo.$requeteb_unionall2.$restrict_convo.$requeteb_unionall3.$restrict_convo;

		if (!isset($sortby))
			$sortby = 'tit1';
			
		$requete .= "  ORDER BY $sortby ";

		switch($dest) {
			case "TABLEAU":
				$res = @mysql_query($requete, $dbh);
				$xmlRoot=$doc->createElement("XML");
				$xmlIdForm=$doc->createElement('IDFORM','FOMLEC_JUS01');
				$xmlCodCentro=$doc->createElement('CODCENTROTMP',$cod_centro);
				$xmlConvocatoria=$doc->createElement('CONVOCATORIA');
				$xmlConvocatoria->appendChild($doc->createCDATASection($grant_period));
				$xmlList = $doc->createElement("c0001");
				$xmlList = $doc->appendChild($xmlList);
				for($i=0; $i < mysql_num_rows($res); $i++) {
					$row = mysql_fetch_row($res);
					$currentTrack = $doc->createElement("ITEM_c0001");
					$currentTrack = $xmlList->appendChild($currentTrack);
					$book_title=$doc->createElement('CL00c0001');
					$book_title->appendChild($doc->createCDATASection(utf8_encode($row[1])));
					$currentTrack->appendChild($book_title);
					$book_idType=$doc->createElement('CL01c0001');
					$book_idType->appendChild($doc->createCDATASection($row[2]));
					$currentTrack->appendChild($book_idType);
					$book_id=$doc->createElement('CL02c0001');
					$book_id->appendChild($doc->createCDATASection($row[4]));
					$currentTrack->appendChild($book_id);
					$book_lang=$doc->createElement('CL03c0001');
					$book_lang->appendChild($doc->createCDATASection($row[5]));
					$currentTrack->appendChild($book_lang);
					$book_author=$doc->createElement('CL04c0001');
					$book_author->appendChild($doc->createCDATASection($row[7]));
					$currentTrack->appendChild($book_author);
					$book_work=$doc->createElement('CL05c0001');
					$book_work->appendChild($doc->createCDATASection($row[8]));
					$currentTrack->appendChild($book_work);
					$book_price=$doc->createElement('CL06c0001');
					$book_price->appendChild($doc->createCDATASection($row[9]));
					$currentTrack->appendChild($book_price);
					$book_num=$doc->createElement('CL07c0001');
					$book_num->appendChild($doc->createCDATASection($row[10]));
					$currentTrack->appendChild($book_num);
					$book_location=$doc->createElement('CL08c0001');
					$book_location->appendChild($doc->createCDATASection($row[12]));
					$currentTrack->appendChild($book_location);
		
				}
				
				$xmlRoot->appendChild($xmlIdForm);
				$xmlRoot->appendChild($xmlCodCentro);
				$xmlRoot->appendChild($xmlConvocatoria);
				$xmlRoot->appendChild($xmlList);
				$doc->appendChild($xmlRoot);
				header("Content-type: application/xml");
				header('Content-Disposition: attachment; filename='.$filename);
				echo preg_replace("/\n/","",$doc->saveXML()); 
				break;
			case "TABLEAUHTML":
				$res = @mysql_query($requete, $dbh);
				$convo_list = "<table width=100%>" ;
				$convo_list .="<tr>
					<th>$msg[expl_title]</th>
					<th>$msg[exp_type_id]</th>
					<th>$msg[expl_id]</th>
					<th>$msg[expl_lang]</th>
					<th>$msg[expl_female_author]</th>
					<th>$msg[expl_literary_work]</th>
					<th>$msg[expl_price]</th>
					<th>$msg[expl_num]</th>
					<th>$msg[expl_location]</th>
					</tr>";
				while(($convo=mysql_fetch_object($res))) {
					
					$convo_list .= "<tr>";
					$convo_list .= "	
								<td>$convo->tit1</td>
								<td>$convo->id_type</td>
								<td>$convo->id</td>
								<td>$convo->id_lang</td>
								<td>$convo->female_author</td>
								<td>$convo->literary_work</td>
								<td>$convo->price</td>
								<td>$convo->num</td>
								<td>$convo->id_location</td>";
							
					$convo_list .= "</tr>";
				}
								
				$convo_list .= "</table>" ;
				echo $convo_list ;
				break;
			default:
				$requete .= "LIMIT $debut,$nb_per_page ";
				$res = @mysql_query($requete, $dbh);
				$parity=1;
				$convo_list .="<tr>
					<th>$msg[expl_title]</th>
					<th>$msg[exp_type_id]</th>
					<th>$msg[expl_id]</th>
					<th>$msg[expl_lang]</th>
					<th>$msg[expl_female_author]</th>
					<th>$msg[expl_literary_work]</th>
					<th>$msg[expl_price]</th>
					<th>$msg[expl_num]</th>
					<th>$msg[expl_location]</th>";
								
				$convo_list .="</tr>";
				$cont_error=0;
				while(($convo=mysql_fetch_object($res))) {
					$check_convo=search_empty_fields($convo);

					if ($parity % 2) 
						$pair_impair = "even";
					else 
						$pair_impair = "odd";
				
					$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" ";
					if ($convo->isnotice==0){
						$script="onclick=\"document.location='./catalog.php?categ=isbd&id=".rawurlencode($convo->registre_id)."';\"";
					}else{				
						$script="onclick=\"document.location='./catalog.php?categ=serials&sub=bulletinage&action=view&bul_id=".rawurlencode($convo->registre_id)."';\"";
				
					}
					$convo_list .= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'>";
					if ($check_convo==0){
						$convo_list .= "
									<td $script>
									<strong>$convo->tit1</strong></td>
									<td>$convo->identification</td>
									<td>$convo->id</td>
									<td>$convo->lang</td>
									<td>$convo->female_author</td>
									<td>$convo->literary_work</td>
									<td>$convo->price</td>
									<td>$convo->num</td>
									<td>$convo->location</td>";
							
					    
						$convo_list .= "</tr>";
						$parity += 1;
					}else{
						$cont_error++;
						$convo_list .= "
										<td style=color:#ff0000 $script>
										<strong>$convo->tit1</strong>
										</td>
										<td style=color:#ff0000>$convo->identification</td>
										<td style=color:#ff0000>$convo->id</td>
										<td style=color:#ff0000>$convo->lang</td>
										<td style=color:#ff0000>$convo->female_author</td>
										<td style=color:#ff0000>$convo->literary_work</td>
										<td style=color:#ff0000>$convo->price</td>
										<td style=color:#ff0000>$convo->num</td>
										<td style=color:#ff0000>$convo->location</td>";
						    
							$convo_list .= "</tr>";
							$parity += 1;
				
					
					}	
				}
				mysql_free_result($res);
				
				// constitution des liens
				$nbepages = ceil($nbr_lignes/$nb_per_page);
				$suivante = $page+1;
				$precedente = $page-1;
				
				// affichage du lien précédent si nécéssaire
				$nav_bar = '';
				if($precedente > 0)
					$nav_bar .= "<a href='$PHP_SELF?categ=books&sub=$sub&page=$precedente&nbr_lignes=$nbr_lignes&form_cb=".rawurlencode($form_cb)."&limite_page=$limite_page&grant_period=$grant_period&cod_centro=$cod_centro&sortby=$sortby'><img src='./images/left.gif' border='0' title='$msg[48]' alt='[$msg[48]]' hspace='3' align='middle'></a>";
				for($i = 1; $i <= $nbepages; $i++) {
					if($i==$page) 
						$nav_bar .= "<strong>page $i/$nbepages</strong>";
				}
				if($suivante<=$nbepages) 
					$nav_bar .= "<a href='$PHP_SELF?categ=books&sub=$sub&page=$suivante&nbr_lignes=$nbr_lignes&form_cb=".rawurlencode($form_cb)."&limite_page=$limite_page&grant_period=$grant_period&cod_centro=$cod_centro&sortby=$sortby'><img src='./images/right.gif' border='0' title='$msg[49]' alt='[$msg[49]]' hspace='3' align='middle'></a>";

				// affichage du résultat
				echo "
					<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$page_url?categ=$categ&sub=$sub&limite_page=$limite_page&numero_page=$numero_page&cod_centro=$cod_centro' method=post>
					<div class='row'>
						<div class='left'>
							$nav_bar $msg[circ_afficher] $msg[1905] &nbsp <input type=text name=limite_page value='$limite_page' class='saisie-5em'>&nbsp&nbsp$msg[notices_convo_codcentro]&nbsp<input type=text name=cod_centro value='$cod_centro' class='saisie-5em' maxlength='9'>&nbsp&nbsp$msg[notices_convo_year]&nbsp
						</div>
						<div class='right'>
							<img  src='./images/mimetype/xml-dist.png'  border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='Export tableau EXCEL' title='Export tableau XML'/>&nbsp;&nbsp;
							<img  src='./images/tableur_html.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='Export tableau HTML' title='Export tableau HTML'/>&nbsp;&nbsp;
						</div>
					</div>	
					<script type='text/javascript'>
						function survol(obj){
							obj.style.cursor = 'pointer';
						}
						function start_export(type){
							document.forms['form-$current_module-list'].dest.value = type;
							document.forms['form-$current_module-list'].submit();
						}	
					</script>
				";
				
				echo gen_liste("select el.expl_custom_champ, el.expl_custom_list_lib from expl_custom_lists el left join expl_custom ec on  el.expl_custom_champ=ec.idchamp where ec.name='Convocatoria' order by expl_custom_list_lib","el.expl_custom_list_lib","el.expl_custom_list_lib","grant_period","",$grant_period,-1,"",0,$msg["select_grant_period"]);
				#echo "$nbsp$msg[notices_convo_codcentro]&nbsp;</b><input type=text name=cod_centro='$cod_centro' size='9'>";
				echo "&nbsp;<input type='submit' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.dest.value='';\" />&nbsp;&nbsp;<input type='hidden' name='dest' value='' />";
			
				echo "
					<div class='row'></div></form><br />";
				print "<script type='text/javascript' src='$base_path/javascript/sorttable.js'></script>";
				
				print pmb_bidi("<table  class='sortable' width='100%'>".$convo_list."</table>");
				if ($cont_error>0){
					echo "<div class='row'>
						</br><h1>".$msg['convo_empty_values']."</h1>
						<div>";	
				}	
				break;
		} //switch($dest)

	} else {
		// la requête n'a produit aucun résultat
		switch($dest) {
			case "TABLEAU":
				break;
			case "TABLEAUHTML":
				break;
			default:
				echo "
					<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$page_url?categ=$categ&sub=$sub&limite_page=$limite_page&numero_page=$numero_page&cod_centro=$cod_centro' method=post>
					<div class='left'>
						$nav_bar $msg[circ_afficher] $msg[1905] &nbsp <input type=text name=limite_page value='$limite_page' class='saisie-5em'>&nbsp&nbsp$msg[notices_convo_codcentro]&nbsp<input type=text name=cod_centro value='$cod_centro' class='saisie-5em' maxlength='9'>&nbsp&nbsp$msg[notices_convo_year]&nbsp
					</div>
					<div class='right'>
						<img  src='./images/mimetype/xml-dist.png' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='Export tableau EXCEL' title='Export fichier XML'/>&nbsp;&nbsp;
						<img  src='./images/tableur_html.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='Export tableau HTML' title='Export tableau HTML'/>&nbsp;&nbsp;
					</div>
					<script type='text/javascript'>
						function survol(obj){
							obj.style.cursor = 'pointer';
						}
						function start_export(type){
							document.forms['form-$current_module-list'].dest.value = type;
							document.forms['form-$current_module-list'].submit();
						}	
					</script>
				";
						
				echo gen_liste("select el.expl_custom_champ, el.expl_custom_list_lib from expl_custom_lists el left join expl_custom ec on  el.expl_custom_champ=ec.idchamp where ec.name='Convocatoria' order by expl_custom_list_lib","el.expl_custom_list_lib","el.expl_custom_list_lib","grant_period","",$grant_period,-1,"",0,$msg["select_grant_period"]);
				echo "&nbsp;<input type='submit' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.dest.value='';\" />&nbsp;&nbsp;<input type='hidden' name='dest' value='' />";
				echo "
					<div class='row'></div></form><br />";
			
				echo "
				<div class='row'></div></form>";
				error_message($msg['grant_period_search'], str_replace('!!form_cb!!', $form_cb, $msg['grant_periodo_search_error']), 1, './edit.php?categ=books&sub='.$sub);
		}
	}
}else{

	switch($dest) {
			case "TABLEAU":
				break;
			case "TABLEAUHTML":
				break;
			default:
				echo "
					<form class='form-$current_module' id='form-$current_module-list' name='form-$current_module-list' action='$page_url?categ=$categ&sub=$sub&limite_page=$limite_page&numero_page=$numero_page&cod_centro=$cod_centro' method=post>
					<div class='left'>
						$nav_bar $msg[circ_afficher] $msg[1905] &nbsp <input type=text name=limite_page value='$limite_page' class='saisie-5em'>&nbsp&nbsp$msg[notices_convo_codcentro]&nbsp<input type=text name=cod_centro value='$cod_centro' class='saisie-5em' maxlength='9'>&nbsp&nbsp$msg[notices_convo_year]&nbsp
					</div>
					<div class='right'>
						<img  src='./images/mimetype/xml-dist.png' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='Export tableau EXCEL' title='Export fichier XML'/>&nbsp;&nbsp;
						<img  src='./images/tableur_html.gif' border='0' align='top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='Export tableau HTML' title='Export tableau HTML'/>&nbsp;&nbsp;
					</div>
					<script type='text/javascript'>
						function survol(obj){
							obj.style.cursor = 'pointer';
						}
						function start_export(type){
							document.forms['form-$current_module-list'].dest.value = type;
							document.forms['form-$current_module-list'].submit();
						}	
					</script>
				";
						
				echo gen_liste("select el.expl_custom_champ, el.expl_custom_list_lib from expl_custom_lists el left join expl_custom ec on  el.expl_custom_champ=ec.idchamp where ec.name='Convocatoria' order by expl_custom_list_lib","el.expl_custom_list_lib","el.expl_custom_list_lib","grant_period","",$grant_period,-1,"",0,$msg["select_grant_period"]);
				echo "&nbsp;<input type='submit' class='bouton' value='".$msg['actualiser']."' onClick=\"this.form.dest.value='';\" />&nbsp;&nbsp;<input type='hidden' name='dest' value='' />";
				echo "
					<div class='row'></div></form><br />";
			
				echo "
				<div class='row'></div></form>";
				error_message($msg['grant_period_search'], str_replace('!!form_cb!!', $form_cb, $msg['notices_convo_codcentro_error']), 1, './edit.php?categ=books&sub='.$sub);
		}


}