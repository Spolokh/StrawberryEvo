<?php
/**
 * @package Show
 * @access private
 */

//include_once 'head.php';
defined('rootpath') or die('No direct access allowed.');

foreach ($_GET as $k => $v){
	$$k = empty($v) ? $$k : @htmlspecialchars($v);
}

foreach ($_POST as $k => $v){
	$$k = empty($v) ? $$k : @htmlspecialchars($v);
}

if (isset($static) and is_array($static)){
	foreach ($vars as $k => $v){
		if ($v != 'static' and $v != 'id'){
			unset($$v);
		}
	}

	foreach ($static as $k => $v){
	    $$k = $v;
	}
}

if (empty($sort[0]) or !strstr($sort[1], 'SC')){
	$sort = array('id', 'DESC');
}

if (isset($catalog)){
	if (!strstr($catalog, ',') and !is_numeric($catalog)){
		$catalog = open_get_id($catalog);
	}

	foreach (explode(',', str_replace(' ', '', $catalog)) as $cat){
	    	$catalog_tmp .= catalog_get_children($cat).',';
	}

	$catalog_tmp = chicken_dick($catalog_tmp, ',');
	$catalog = ($catalog_tmp ? $catalog_tmp : $catalog);
}

$allow_full_catalog = false;

$where = array();

if ($catalog){

	$catalog_tmp = chicken_dick($catalog_tmp, ',');
	$catalog     = $catalog_tmp ? $catalog_tmp : $catalog;

	$where[] = "catalog = $catalog";
	$where[] = 'and';
}
 
$where[] = "publication = 1";
 
$query = $sql->select(array(
	'table'   => 'shop', 
	'orderby' => array(array('date', 'DESC'), $sort),
	'where'   => $where, 
	'limit'   => array(($skip ? $skip : 0), $number)
));

$count = $sql->count(array('table' => 'links', 'where' => $where));

if(!reset($query)){
   echo t('каталог пустой');
   return;
}
   
foreach($query as $row){

	$tpl['catalog']       = $row;
	$tpl['catalog']['_']  = $row;
			
	if ($cat_arr = explode(',', $row['catalog'])){
	
		$cat = array();

		foreach ($cat_arr as $v){
			$cat['id'][]   = $v;
			$cat['name'][] = ($catalogs[$v]['name'] ? '<a href="'.cute_get_link($catalogs[$v], 'catalog').'" title="'.replace_news('admin', $catalogs[$v]['description']).'">'.$catalogs[$v]['name'].'</a>' : '');
			$cat['desc'][] = $catalogs[$v]['description'];
			$cat['url'][]  = $catalogs[$v]['url'];
		}
	}
			
	if ($catheader != catalog_get_title($row['catalog'])){
		$tpl['catalog']['catheader'] = $catheader = catalog_get_title($row['catalog']); 
	} else {
		$tpl['catalog']['catheader'] = '';
	}
			
	
	$tpl['catalog']['description'] = run_filters('news-entry-content', $row['description']);
			
	ob_start();
	include templates_directory.'/Catalogs/'.($allow_full_catalog ? 'full' : 'active').'.tpl';
	$output = ob_get_clean();
	
	$output = run_filters('news-entry', $output);
	$output = replace_news('show', $output);
	$output = str_replace('{name}', $tpl['catalog']['name'], $output);
	echo $output;

	if ($allow_full_catalog){
		$sql->update(array('table'=> 'links', 'where'=> array("id = $row[id]"), 'values'=> array('views' => $row['views'] + 1)));
	}		
} 

if ($vars = run_filters('unset', $vars)){
	foreach ($vars as $var){
		unset($$var);
	}
}

unset($catalog_tmp, $parent, $no_prev, $no_next, $prev, $var);