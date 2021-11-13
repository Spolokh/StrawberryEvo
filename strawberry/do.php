<?php

include_once 'head.php';

$uri = $REQUEST_URI;

switch($uri)
{
	case "/do/form" : 
		$result = (new classes\ModalForm($config))->run(); 
		break;
		
	case "/do/login" :
		ob_start();
		include forms_directory.'/LoginForm.tpl';
		$result = ob_get_clean();
		break;
		
	default : 
		header('HTTP/1.1 404 Not Found');
        	exit;
}

exit($result);
