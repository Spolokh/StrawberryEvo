<?php
/**
 * @package Private
 * @access private
 */

include_once dirname(__DIR__) . '/strawberry/head.php';

header('Content-type: text/html; charset='.$config['charset']);
header('X-Robots-Tag: noindex');

$header = $_SERVER['HTTP_X_REQUESTED_WITH'] ;

if( (empty($header) 
	or strtolower($header) != 'xmlhttprequest') 
	or (!isset($_POST['sessid'], $_POST['action'])) )
{
	exit;
}

foreach( $_POST AS $k => $v )
{
    $$k = htmlspecialchars($v);
}

$errors     = [];
$allow_add_comment = true;

if( !isset($sessid) or $sessid !== session_id() )
{
	exit('Вам тут не надо !');
}

//FILTER_SANITIZE_NUMBER_INT Удаляет все символы, кроме цифр и знаков плюса и минуса.
if ( !$is_logged_in )
{
	if (empty($name)) {  //filter_var($name, FILTER_SANITIZE_STRING);
		$errors[] = t('Введите ваше имя.');
	}

	if (empty($mail) or !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
		$errors[] = t('Извините, этот E-mail неправильный.');
	}

	if (strlen($name) > 50) 
	{ 	// Check the lenght of name
		$errors[] = t('Вы ввели слишком длинное имя.');
	}
	
	if (strlen($mail) > 50 )
	{ 	// Check the lenght of mail
		$errors[] = t('Вы ввели слишком длинный e-mail.');
	}
}

//if ($phone and !preg_match('/^[0-9+-]{6,10}$/is', $phone)){
//	$error_message[] = t('Ваш телефон написан неверно.');
//}

if (empty($comment))
{
	$errors[] = t('Заполните поле "Комментарий".');
}

if ($config['comment_max_long'] and strlen($comment) > $config['comment_max_long'])
{ 	
	$errors[] = t('Вы ввели слишком длинный комментарий.'); // Check the lenght of comment
}

if( reset($errors) )
{
    $allow_add_comment = false;

	cute_response_code( 
		500, join(' ', array_values($errors));
	);
}

$allow_add_comment or exit;

$name    = $is_logged_in ? $member['name'] : replace_comment('add', preg_replace("/\n/", '', $name));
$mail    = $is_logged_in ? $member['mail'] : replace_comment('add', preg_replace("/\n/", '', $mail));
$subject = $subject ? filter_var(preg_replace("/\n/", '', $subject), FILTER_SANITIZE_STRING) : t('Cообщение с сайта.');

//replace_comment('add', preg_replace("/\n/", '', $subject)) : t('Cообщение с сайта.');
$comment = replace_comment('add', $comment);

$template = isset($template) ? $template : 'callback';

//$phone   = replace_comment('add', preg_replace("/\n/", '', $phone));
//http://sms.ru/sms/send?api_id=a0d6135e-5440-0e14-014f-eca334d5665e&to=79037494477&text=hello+world
/*

$fields = [
	'api_id' =>	'a0d6135e-5440-0e14-014f-eca334d5665e',
	'to'	 =>	preg_replace('/_|-|\s|\(|\)|\+/', '', $config['site_phone']),
	'text'	 =>	'Привет!
];


$ch = curl_init('http://sms.ru/sms/send');
 
$options = [
	CURLOPT_TIMEOUT 	=> 30,
	CURLOPT_POSTFIELDS 	=> [
		'api_id' =>	'a0d6135e-5440-0e14-014f-eca334d5665e',
		'to'	 =>	preg_replace('/_|-|\s|\(|\)|\+/', '', $config['site_phone']),
		'text'	 =>	'Привет!
	],
	CURLOPT_RETURNTRANSFER => true
];

curl_setopt_array($ch, $options);

if ( curl_errno($ch) )
{
	$sms = curl_error($ch);
}
$sms = curl_exec($ch);
curl_close($ch);
*/

/*
$ch = curl_init('http://sms.ru/sms/send');

try {

	$options = [
		CURLOPT_TIMEOUT    => 30,
		CURLOPT_POSTFIELDS => [
			'api_id' =>	'a0d6135e-5440-0e14-014f-eca334d5665e',
			'to'	 =>	preg_replace('/_|-|\s|\(|\)|\+/', '', $config['site_phone']),
			'text'	 =>	'Привет!'
		],

		CURLOPT_RETURNTRANSFER => true
	];

	curl_setopt_array($ch, $options);
	$sms = curl_exec($ch);
	
} catch (\Exeption $e) {
	$sms = curl_error($ch);
}

curl_close($ch);
*/

use Curl\Curl;
use classes\Mailer\PHPMailer;

$curl = new Curl();

try
{
	$curl->post( 'http://sms.ru/sms/send', [
		'api_id' =>	'a0d6135e-5440-0e14-014f-eca334d5665e',
		'to'	 =>	preg_replace('/_|-|\s|\(|\)|\+/', '', $config['site_phone']),
		'text'	 =>	'Привет!'  // file_get_contents(mails_directory .'/'. $template.'.tpl')
	] );
	$curl->setTimeout(30);

	$curl->exec();
	$sms = $curl->response;
	 
} catch (\Exeption $e) {
	throw $curl->errorCode;
}

$curl->close();
/*
if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
    echo 'Data server received via POST:' . "\n";
    var_dump($curl->response->form);
}*/


ob_start();
include mails_directory.'/'.$template.'.tpl';
$Body = ob_get_clean();

$mailer = new PHPMailer(); 
$mailer->From     = $mail;
$mailer->FromName = $name;
$mailer->CharSet  = $config['charset'];
$mailer->Sender   = $mail;
$mailer->Subject  = $subject; 

//$mailer->Body     = $Body;

$mailer->msgHTML(file_get_contents(mails_directory .'/'. $template.'.tpl'));

//$mailer->AltBody  = 'Добавляем адрес и альтернативный текст'; // Добавляем адрес в список получателей
$mailer->AddAddress( $config['admin_mail'], "Администратору сайта" );
$mailer->AddReplyTo( $mail, $name );
//$mailer->AddCC( $config['admin_mail'], 'Первый копия' );
//$mailer->ConfirmReadingTo = $config['admin_mail']; 
//$mailer->IsHTML(true);
$result = $mailer->Send() ? t('Ваше сообщение успешно отправленно!'): $mailer->ErrorInfo; 
$mailer->ClearAddresses(); 
$mailer->ClearAttachments();

exit ($result);
