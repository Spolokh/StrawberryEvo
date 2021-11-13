<?php

namespace classes;

class CN 
{	
	const VERSION_CN = 'Strawberry';
	const VERSION_ID = '1.1.1';
	const HTTP_HOST	 = '';
	
	private static $_config = [];
	private static $_result = [];

	/**
	 * Enter description here...
	 * @access private
	 */
	public static function start(){
		global $starttime;
		$mtime     = microtime();
		$mtime     = explode (' ', $mtime);
		$mtime     = $mtime[1] + $mtime[0];
		$starttime = $mtime;
	}

	/**
	 * Enter description here...
	 * @access private
	 * @return unknown
	 */
		
	public static function stop() {
		global $starttime;
		$mtime     = microtime();
		$mtime     = explode (' ', $mtime);
		$mtime     = $mtime[1] + $mtime[0];
		$endtime   = $mtime;
		$totaltime = round (($endtime - $starttime), 5);
		return $totaltime;
	}
	
	/**
	 * Отделяет мух от супа.
	 * Другими словами, заменяет все повторения строки $dick
	 * на одно и вырезает все повторения $dick по "бокам" строки $chicken.
	 * @param string $chicken
	 * @param string $dick
	 * @return string
	 */

	public static function chicken_dick ($chicken, $dick = '/')
	{
		$chicken = preg_replace('/^(['.preg_quote($dick,'/').']+)/', '', $chicken);
		$chicken = preg_replace('/(['.preg_quote($dick, '/').']+)/', $dick, $chicken);
		$chicken = preg_replace('/(['.preg_quote($dick, '/').']+)$/', '', $chicken);
		return $chicken;
	} 
	
	public static function get_id (array $categories = [], $cat = '')
	{
	    if (!is_array($categories) or empty($categories)) {
			return false;
		}

		$cat = self::chickenDick($cat);
		
		foreach ($categories as $row)
		{
			if ($cat == self::getUrl($row['id'])){ //self::getLink
				$rowsId = $row['id'];
			} elseif ($cat == $row['url']){
				$rowsId = $row['id'];
			} elseif ($cat == $row['id']){
				$rowsId = $row['id'];
			} elseif ($cat == '') {
				$rowsId = 0;
			}
		} 
		return $rowsId;
	}
	
	/**
	 * Возвращает ссылку на категорию с id = $id, учитывая все категории-родители.
	 * Можно указать суффикс урлу, передав значение переменной $link.
	 * @param int $id
	 * @param string $link
	 * @return string
	 */
	
    public static function get_url($id, $link = '')
    {	
		global $categories;

		if (empty($categories)) {
			return false;
		}
		
		if ($categories[$id]['url']){
			$link = $categories[$id]['url'].($link ? '/'.$link : '');
		}

		if ($categories[$id]['parent']){
			$link = self::getUrl($categories[$id]['parent'], $link);
		}   
		
		return self::chickenDick($link);		
	}

	/**
	 * Возвращаем всех детей из таблиц 
	 * категорий, каталогов & etc c полями `id` и `parent`.
	 * @param array $field
	 * @author Yury D. Spolokh
	 * @param int $id
	 * @return string
	 */

	public static function get_children (array $categories = [], $id = 0)
	{
		static $end = 1, $result = [];

		if (empty($categories)) {
			return false;
		}
		if ($id !== '') {
			$result[] = $id;  
		}
		foreach ($categories as $k => $row) {	
			if ($row['parent'] == $id) {
				$result[] = $row['id'];
			}
		}
		
		$end++;
		$return = $result;
		$result = [];
		return reset($return) ? join(',', $return) : [];
	}

	public static function  get_tree(array $categories, $parent = 0)
	{
		$categories = self::buildTree($categories);

		//print_r($categories);

		if ( !isset ($categories[$parent]) ){
			return false;
		}

		$tree = PHP_EOL;

		foreach($categories[(int)$parent] as $row){
		
			$tree .= '<li><a href="">'.$row['name'].'</a>';
			$tree .=  self::get_tree( $categories, $row['id'] );
			$tree .= '</li>'. PHP_EOL;         
		}       

		return '<ul>' .$tree. '</ul>';        
	}
	
	/*public static function get_tree($cats, $parent = 0)
	{
		if (empty($cats))
		{
			return null;
		}

		if (!isset($cats[$parent])) //!is_array($categories[$parent]) or 
		{
			//return null;
		}
		
		//$cats = self::buildTree($cats);

		$tree = PHP_EOL;

		foreach($cats[$parent] as $row)
		{
			$tree.= '<li><a href="">'.$row['name'].'</a>';
			$tree.= self::getTree($cats, $row['id']);
			$tree.= '</li>'.PHP_EOL;         
		}
		return '<ul>' .$tree. '</ul>';
	}*/

	private static function build_tree( array $array, array $parents = [])
	{
		if (empty($array)) {
			return [];
		}
		foreach ($array as $k) {
			$parents[$k['parent']][] = $k;
		}
		return $parents;
	} 
	
	public static function order_by ($data, $field = 'order') {
		$code = "return strnatcmp(\$a['$field'], \$b['$field']);";
		usort($data, create_function('$a, $b', $code));
		return $data;
	}
	
	public static function is_dir ($path) {
		return is_dir($path) or mkdir($path, chmod, true);
	}
	
	public static function file_write($filepath, $result) {
		return file_put_contents(self::fileRead($filepath), $result) ? true : false;
	}
	
	public static function file_read ($filepath) {
		return is_file ($filepath) ? file_get_contents($filepath) : false;
	}
	
	public static function query_string (string $self, array $params = [], $result = []) : string
	{	
		if (empty($params)) {
			return $self;
		}
		
		foreach($params as $k => $v) {
			$result[] = $k.'='.urlencode($v);
		}
	
	    return $self.'?'.join('&', self::$_result);
	}

	public static function is_json($str = NULL)
	{
		if (is_string($str)) {
			json_decode($str);
			return (json_last_error() === JSON_ERROR_NONE);
		}
		return false;
	}

	public static function is_ajax()
	{
		$header = $_SERVER['HTTP_X_REQUESTED_WITH'];
		return ($header && strtolower($header) === 'xmlhttprequest') ? true : false;
	}
	
	public static function __callStatic($name, $arg)
	{
		$method = strtolower( preg_replace('/([a-z])([A-Z])/', '$1_$2', $name) );
		return call_user_func_array(['CN', $method], $arg);
    }
}
