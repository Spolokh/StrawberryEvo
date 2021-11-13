<?php
/**
 * @package Public
 * @access public
 */

namespace classes\Users;

use classes\{Template, PHPMailer, CuteParser};

final class Profile extends CuteParser
{
	private $action;
    private $header;
	private $module = 'profile';
    private $errors = [];
	private $values = [];

	public $member;

	public function __construct ($config)
	{
		parent::__construct($config);

		$this->action = $_POST['action'] ?? null;
        $this->header = $_SERVER['HTTP_X_REQUESTED_WITH'];
	}

	private function form( $member, $dir = templates_directory . '/Users/' )
	{
		$template = (new Template($dir))->open('editprofile', $this->module);
		$template->set('username', $member['username'], $this->module)
			->set('name', $member['name'], $this->module)
			->set('mail', $member['mail'], $this->module)
			->set('age', date_AddRows($member['age']), $this->module)
			->set('about', htmlspecialchars(str_replace("<br/>", NL, $member['about'])), $this->module)
			->set('ljusername', $member['lj_username'], $this->module)
			->set('ljpassword', $member['lj_password'], $this->module)
		;

		if (isset($member['contacts']) /*and \CN::isJson($member['contacts'])*/)
		{
			$contact = json_decode($member['contacts']);

			$template->set('city',  $contact->city,  $this->module)
				->set('page',  $contact->page,  $this->module)
				->set('skype', $contact->skype, $this->module)
				->set('phone', $contact->phone, $this->module)
			;
		}
	
		return $template ->compile($this->module, true);	
		$template ->fullClear();
	}

	private function edit($member, $result = '')
	{
		if ( $this->action !== 'editprofile' or false === isXmlHttpRequest() )
        {
            cute_response_code( 500, 0 );
		}

		//if ( !$this->header or strtolower($this->header) !== 'xmlhttprequest' )
        // {
        //    cute_response_code( 500,0 );
		//}

		foreach ($_POST as $k => $v)
        {
            $$k = $v;
		}
		
		if ( !filter_var($mail, FILTER_VALIDATE_EMAIL) )
		{
			$this->errors[] = t('Извините, этот e-mail неправильный.');
		}

		foreach (parent::select(['users', 'select' => ['mail'], 'where' => ["id <> $member[id]"]]) as $row)
		{
			if ($mail && strtolower($row['mail']) == strtolower($mail))
			{
				$this->errors[] = t('Такой e-mail уже кто-то использует.');
			}
		}

		if ( reset($this->errors) )
		{
			$allow_add_comment = false;
			cute_response_code(500, join ( '<br/>', array_values($this->errors) )) ;
		}

		foreach ( parent::select(['users', 'select' => ['password'], 'where' => $member['id']]) AS $row )
		{
			if ($editpass != '') {
				$row['password'] = md5x($editpass);
				$_SESSION['password'] = $row['password'];
				cute_setcookie('password', $row['password']);
			}
		}

		if(($added_time = strtotime($day.' '.$month.' '.$year)) == -1)
		{
			$added_time = time;
		}

		$upload_image = false;
	
		if ( $_FILES['avatar']['name'] and $_FILES['avatar']['error'] == UPLOAD_ERR_OK )
		{	//$EXTENSION = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
			$userpics = cute_parse_url($this->Config['path_userpic_upload']);
			$userpics = $userpics['abs'];
		
			CN::isDir ($userpics);
		
			$handle = new Upload($_FILES['avatar']);	  
				
			if ($handle->uploaded)
			{
				$handle->allowed = ['image/*'];
				$handle->file_new_name_body = $member['username'];
				
				if ($this->Config['avatar_w'])
				{
					$handle->image_resize     = true;
					$handle->image_ratio_crop = true;
					$handle->image_x = $this->Config['avatar_w'];
					$handle->image_y = $this->Config['avatar_h'];
				}
			 
				if ($config['avatar_ext'] != '')
				{
					$handle->image_convert = $this->Config['avatar_ext'];
				}

				$handle->file_overwrite = true;
				$handle->process($userpics);
				
				if ($handle->processed)  //$upload_image = true;
				{
					$this->values['avatar'] = $handle->file_dst_name_ext;
				}
				$handle->clean();
			}
		}

		$this->values['age']   = $added_time;
		$this->values['name']  = replace_comment('add', $name, true);
		$this->values['mail']  = replace_comment('add', $mail, true);
		$this->values['about'] = replace_comment('add', $about);
		
		if ( !empty($editpass) ) {
			$this->values['password'] = md5x($editpass);
		}
		//$this->values['password'] = $row['password'];
		$this->values['contacts'] = json_encode($contacts, JSON_UNESCAPED_UNICODE);
		$this->values['lj_username'] = replace_comment('add', $ljusername, true);
		$this->values['lj_password'] = replace_comment('add', $ljpassword, true);
		
		try {
			$result = parent::update(['users', 'where' => $member['id'], 'values' => $this->values]) 
			? t('Ваш профиль успешно отредактирован!') : t('Ошибка запроса!');
			$this->values = [];
			cute_response_code( 200,$result );
		} catch (\Exception $e) {
			cute_response_code( 500,$e->getMessage() );
		}
	}
	
	private function isXmlHttpRequest()
	{
		return (strtolower($this->header) !== 'xmlhttprequest') ? false : true;
	}

	public function run($member)
	{
		return isset($this->action) ? $this->edit($member) : $this->form($member);
	}
}
