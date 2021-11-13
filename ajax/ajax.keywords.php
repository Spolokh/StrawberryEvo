<?php

include_once substr(dirname(__FILE__), 0, -5).'/strawberry/head.php';

$request = '';

if ( isset($_POST['keywords']) ){

	$request = $_POST['keywords'];
	
	if ( !$query = $sql->select(['keywords', 'select' => ['id', 'name']]) ) {
		exit ('<span></span>') ;
	}

	foreach ($query as $row) {
		$values[] = $row['name']; //Фильтруем $pets в промежуточный массив $values
	}
}

array_walk ( $values, function($item, $k)
{	
	global $data, $request; 

	$string = substr($item, 0, strlen($request));
	
	if( $string == $request || strtolower($string) == $request || strtoupper($string) == $request ) {
		$data[] = $item;
	}
});	

$result = !empty($data) ? '<ul><li>' . implode('</li><li>', $data) . '</li></ul>' : '<span></span>';
exit ($result) ;
