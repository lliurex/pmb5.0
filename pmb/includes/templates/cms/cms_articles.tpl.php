<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_articles.tpl.php,v 1.2 2017-01-18 13:45:24 ngantier Exp $


$cms_articles_list ="
	<h3>!!cms_articles_list_title!!</h3>
<table class='cms_articles_list'>
	!!items!!
</table>

";

$cms_articles_list_item ="
	<tr class='cms_article'>
		<td>
			<a href='./cms.php?categ=article&sub=edit&id=!!cms_article_id!!'>!!cms_article_title!!</a>
		</td>
		<td>
			!!cms_article_type!!
		</td>
	</tr>
";