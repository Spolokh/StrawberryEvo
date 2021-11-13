<?php
/**
 * @package Show
 * @access private
 */

include_once 'strawberry/head.php';

// убирает форму
add_filter('allow-comment-form', 'comment_form');

function comment_form(){
	return false;
}

// запрещаем менять шаблон кроме как через переменную $template
add_filter('unset-template', 'unset_template');

function unset_template($files){
	$files[] = basename($_SERVER['PHP_SELF']);
	return $files;
}

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="'.$config['charset'].'" ?>';
?>

<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<?php
$query = $sql->select(array('table' =>'shop','select'=> array('url','name','desc'), 'where' => array('tags=2','and','hidden=0')));

foreach ($query AS $row){
?>

	<url>
		<loc><?=cute_get_link($row, 'shop');?></loc>
		<priority>0.8</priority>
	</url>
	
<?php } ?>
</urlset>