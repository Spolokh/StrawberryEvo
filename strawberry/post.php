<?php
	
defined('rootpath') or exit('None');

$mod = 'post';

$template = templates_directory . '/Users/';
$template = new Template ($template);
$template ->open('addpost', $mod);

//$template ->set ('insert_tags', run_filters('insert-tags-options', 'short'), $mod);
 
$template ->set('title', isset($post['title']) ? htmlspecialchars(replace_news('admin', $post['title'])) : '', $mod);
$template ->set('short', isset($post['short']) ? htmlspecialchars(replace_news('admin', $post['short'])) : '', $mod);
$template ->set('full', isset($post['full']) ? htmlspecialchars(replace_news('admin', $post['full'])) : '', $mod);

 

$result = $template->compile($mod, true);

echo $result;

	
