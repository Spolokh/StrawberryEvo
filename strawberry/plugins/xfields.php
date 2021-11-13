<?php
/**
 * @package Plugins
 * @access private
 */

/*
Plugin Name: 	XFields
Plugin URI: 	http://webmaster.nzpv.ru/
Description: 	Дополнительные поля.
Version: 		2.0
Application: 	Strawberry
Author: 		Yury Spolokh
*/

add_action('head', 'xfields');

function xfields()
{
    global $sql;

    if (!$sql->tableExists('fields'))
	{	
		$sql->createTable(['fields', 'columns' => [
            'id'            => ['type' => 'int', 'auto_increment' => 1, 'primary' => 1],
            'key'           => ['type' => 'string', 'permanent' => 1],
            'name'          => ['type' => 'string'],
            'type'          => ['type' => 'string'],
            'module'        => ['type' => 'string', 'default' => 'news'],
            'hidden'        => ['type' => 'string', 'default' => '0'],
            'parent'        => ['type' => 'int', 'default' => '0'],
            'category'      => ['type' => 'string', 'default' => '0'],
            'usergroup'     => ['type' => 'string', 'default' => '0'],
            'description'   => ['type' => 'string']
        ]]);

		$sql->alterTable( 
            ['story', 'name' => 'fields', 'action' => 'insert', 'values' => ['type' => 'text']]
        );

        return false;
    }
}

add_filter('options', function ($options)
{
	$options[] = [  
        'name'=> t('Дополнительные поля'), 'url' => 'plugin=xfields', 'category' => 'plugins'
    ];
    return $options;
});

add_action('plugins','xfields_CheckAdminOptions');

function xfields_CheckAdminOptions()
{
	if ( $_GET['plugin'] and $_GET['plugin'] == 'xfields' ) {
		xfields_AdminOptions();
	}
}

function xfields_AdminOptions()
{
    global $sql, 
        $xfield, 
        $PHP_SELF;

    echoheader('options', t('Дополнительные поля'));
?>

        <img align="ABSmiddle" src="skins/images/help_small.gif"> &nbsp; 
        <a Title="Дополнительные поля" href="index.php?mod=help&amp;section=xfields" role="button" data-target="myModal" data-toggle="modal">
            <?=t('Подробнее')?>
        </a>
<?php
    echofooter();
}

add_action('xfields-action', 'admins_xfields', 1);

function admins_xfields()
{
    global $post, $mod, $xfields;

    $xfields = include xfields_file;

    if ( empty($xfields) ) {
        return false;
    }

    $post['data'] = ( isset($post['data']) and CN::isJson($post['data']) ) ? json_decode($post['data']) : null;

    ob_start();
?>
    <legend><?=t('Дополнительные поля')?></legend>

<?php foreach ($xfields AS $k => $v) : ?>

    <input type="text"  size="100" name="field[<?=$k?>]" 
        value="<?=( isset($post['data']) ? replace_comment('admin', $post['data']->$k) : '' ) ?>" placeholder="<?=$v?>"
    />
    <hr style="height:0" />

<?php endforeach;  
    return ob_get_clean();
}

add_filter('news-show-generic', 'xfields_parse');

function xfields_parse ($tpl)
{
    global $row, $xfields;

    $tpl['xfields'] = [];

    if ( $xfields = json_decode($row['data'], true) )
    {
        foreach($xfields as $k => $v) {
			$tpl['xfields'][$k] = $v;
		}
    }
    return $tpl;
}

//add_action('mass-deleted', 'xfields_delete', 2);

function xfields_delete(){
   /* global $row;
    $xfieldsaction = 'delete';
    $xfieldsid = $row['id'];
    include plugins_directory.'/xfields/core.php';*/
}

//add_action('new-save-entry', 'call_xfields_Save');
//add_action('edit-save-entry', 'call_xfields_Save');

function call_xfields_Save(){
   /* global $id, $xfield;
	$xfieldsid = $id;
	$xfieldsaction = 'init';
    include plugins_directory.'/xfields/core.php';
	$xfieldsaction = 'save';
	include plugins_directory.'/xfields/core.php';*/
}

add_filter('template-active', 'xfields_templates');
add_filter('template-full',   'xfields_templates');

function xfields_templates($template) : string
{
	$template['xfields'] = t('Например $tpl[\'post\'][\'xfields\'][\'X\'], где "X" это имя поля.');
    return $template;
}

function get_starred (string $text, int $end = 0, string $replace = '*', $skip = null) : string
{
	$stars = str_split($text);
	$count = count($stars);
	
   	foreach($stars as $k => $v)
    {
		if( $k === 0 || ($end && $k == $count - $end) )
		{
			continue;
		} 
		
		//if ( isset($skip) and $text[$k] !== $skip){
			$text[$k] = $replace;
		//}
   	}
   	
	return $text;
}
