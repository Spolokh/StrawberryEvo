<?php
/**
 * @package Public
 * @access public
 */

namespace classes\Users;

use \CN;
use classes\{
	Template, 
	PHPMailer, 
	CuteParser
};

final class Profile extends CuteParser
{
	private $action
		, $header
		, $errors = []
		, $values = []
		, $upload = false
		, $module = 'editprofile'
	;
	
	public $member = [];

	public function __construct ($config)
	{
		parent::__construct($config);

		$this->action = $_POST['action'] ?? null;
        $this->header = $_SERVER['HTTP_X_REQUESTED_WITH'];
	}

	public function __set($member, $value)  
    {
        $value = $this->member ;
    }

	private function form( $dir = templates_directory . '/Users/' )
	{
		$template = (new Template($dir))->open('editprofile', $this->module);
		$template->set('username', $this->member['username'], $this->module)
			->set('age', date_AddRows($this->member['age']), $this->module)
			->set('name', $this->member['name'], $this->module)
			->set('mail', $this->member['mail'], $this->module)
			->set('about', htmlspecialchars(str_replace("<br/>", NL, $this->member['about'])), $this->module)
			->set('ljusername', $this->member['lj_username'], $this->module)
			->set('ljpassword', $this->member['lj_password'], $this->module)
		;

		if( isset($this->member['contacts']) and CN::isJson($this->member['contacts']) )
		{
			$contact = json_decode($this->member['contacts']);

			$template->set('city',  $contact->city,  $this->module)
					 ->set('page',  $contact->page,  $this->module)
					 ->set('skype', $contact->skype, $this->module)
					 ->set('phone', $contact->phone, $this->module)
			;
		}
	
		print $template ->compile($this->module, true);	
		$template ->fullClear();
	}

	private function edit($result = '')
	{
		if ( !isset($this->action) or $this->action !== $this->module or !$this->isXmlHttpRequest() )
        {
            cute_response_code( 500, 0 );
		}

		foreach ($_POST as $k => $v)
        {
            $$k = $v;
		}
		
		if ( !filter_var($mail, FILTER_VALIDATE_EMAIL) )
		{
			$this->errors[] = t('Извините, этот e-mail неправильный.');
		}

		foreach (parent::select(['users', 'select' => ['mail'], 'where' => ["id <> $this->member[id]"]]) as $row)
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

		foreach ( parent::select(['users', 'select' => ['password'], 'where' => $this->member['id']]) AS $row )
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

		$uploadImage = false;
	
		if ( $_FILES['avatar']['name'] and $_FILES['avatar']['error'] == UPLOAD_ERR_OK )
		{
			$userpics = cute_parse_url($this->Config['path_userpic_upload']);
			$userpics = $userpics['abs'];
		
			CN::isDir ($userpics);
		
			$Upload = new Upload($_FILES['avatar']);	  
				
			if ($Upload->uploaded)
			{
				$Upload->allowed = ['image/*'];
				$Upload->file_new_name_body = $this->member['username'];
				
				if ($this->Config['avatar_w'])
				{
					$Upload->image_resize     = true;
					$Upload->image_ratio_crop = true;
					$Upload->image_x = $this->Config['avatar_w'];
					$Upload->image_y = $this->Config['avatar_h'];
				}
			 
				if( $config['avatar_ext'] != '' )
				{
					$Upload->image_convert = $this->Config['avatar_ext'];
				}

				$Upload->file_overwrite = true;
				$Upload->process ($userpics);
				
				if ($Upload->processed)  //$uploadImage = true;
				{
					$this->values['avatar'] = $Upload->file_dst_name_ext;
				}
				$Upload->clean();
			}
		}

		$this->values['age']   = $added_time;
		$this->values['name']  = replace_comment('add', $name, true);
		$this->values['mail']  = replace_comment('add', $mail, true);
		$this->values['about'] = replace_comment('add', $about);
		
		if ( !empty($editpass) ) {
			$this->values['password'] = md5x($editpass);
		}

		$this->values['contacts'] = json_encode($contacts, JSON_UNESCAPED_UNICODE);
		$this->values['lj_username'] = replace_comment('add', $ljusername, true);
		$this->values['lj_password'] = replace_comment('add', $ljpassword, true);
		
		try {
			
			$result = $this->update(['users', 'where' => $this->member['id'], 'values' => $this->values])
			? t('Ваш профиль успешно отредактирован!') : t('Ошибка запроса!');
			$this->values = [];
			cute_response_code( 200, $result );
		} catch (\Exception $e) {
			cute_response_code( 500, $e->getMessage() );
		}
	}
	
	private function isXmlHttpRequest() : bool
	{
		return (strtolower($this->header) !== 'xmlhttprequest') ? false : true;
	}

	public function run()
	{
	 	isset($this->action) ? $this->edit() : $this->form();
	}
}
