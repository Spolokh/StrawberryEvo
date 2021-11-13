<?php

/**
 * @package Private
 * @access private
 */

include_once 'strawberry/head.php';

defined('rootpath') or die('No direct access allowed.');

foreach ($_GET as $k => $v) {
	$$k = htmlspecialchars($v);
}

if ( !($member || $member['setting']->vk) ) {
	$errors[] = t('Необходима авторизация.');
}

if (reset($errors)) {
    header('HTTP/1.0 500 Internal Server Error');
	echo join("\n", array_values($errors));
    exit;
}

header('Content-Type: application/json'); 

$values ['owner_id'] = $member ['setting']->vk;
$values ['album_id'] = $album_id ?: 'saved';
$values ['format']   = $format   ?: 'json'; //xml
$values ['count']    = $count    ?: 10;
$values ['rev']      = $rev      ?: 0 ;

echo (new Vkapi($config))->api('photos.get', $values)->run();  
