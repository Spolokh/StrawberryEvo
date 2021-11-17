<?php
/**
 * @package Show
 * @access private
 */

include_once 'head.php';

$template = 'remote_headlines';
$number = ($number ? $number : 7);
include root_directory.'/show_news.php';
