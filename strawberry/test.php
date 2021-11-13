<?php

/**
Митинг за КПСС. За вечную власть кумунистов, они устанавливают нужные им рамки каждый раз. А бараны каждый раз как в первый раз :) Навальный и Госдума , теперь дубль 2, мосдума. Одно и тоже. Фото улавливает самую суть. Супер


*/


///////////////// для ссылок в тексте //////////////////////////////////
$text = 'Как видим, он отработал корректно и все <a class="qwerty" href="index.php">ссылки</a> были найдены. Давайте теперь разберем его.';

$pattern = '/<a class="[^<]+?" href="(.*?)">(.*?)<\/a>/i';

echo preg_replace($pattern, '$1:$2', $text);

/*

function ... ($text) {
	
	$pattern = '/<a href="(.*?)">(.*?)<\/a>/i';
	return preg_replace($pattern, '$1:$2', $text);
}

/*
Для массива размерности N, вида:
	 * [
	 *    1 => [1,  "Title 1", null, '/'],
	 *    2 => [2,  "Title 2", 1,    '/about/'],
	 *    3 => [3,  "Title 3", 1,    '/contacts/'],
	 *    4 => [4,  "Title 4", 2,    '/about/company/'],
	 *    5 => [5,  "Title 5", 2,    '/about/history/'],
	 *    8 => [8,  "Title 8", null, '/eng/'],
	 *    ...
	 *    35 => [35, "Title 35", 8,   '/eng/about/']
	 *    ...
	 *    int 'id' => [int 'id', string 'title', int|null 'parent_id', string 'url']
	 * ]
	 *
	 * напишите функцию, трансформирующую представленный массив в многомерный массив вида:
	 * [
	 *    1 => [
	 *       2 => [
	 *          4 => [],
	 *          5 => []
	 *       ],
	 *       3 => []
	 *    ],
	 *    8 => [
	 *       35 => []
	 *    ],
	 *    ...
	 * ]
	 *
	 * где в качестве ключей используется значение поля 'id', в качестве значений - массив "дочерних" элементов
	 */

$linear = [
		1  => [1,   "Title 1",  0, '/'],
		2  => [2,   "Title 2",  1, '/about/'],
		3  => [3,   "Title 3",  1, '/contacts/'],
		4  => [4,   "Title 4",  2, '/about/company/'],
		5  => [5,   "Title 5",  2, '/about/history/'],
		8  => [8,   "Title 8",  0, '/eng/'],
		35 => [35,  "Title 35", 8, '/eng/about/']
	];

$cat_tree = [];
        
foreach ( $linear AS $k => $row ) {
	$cat_tree[$row[2]][] = $row;
}


function  get_tree(array $cat_tree, $parent = 0)
{
	if (isset ($cat_tree[$parent]))
	{

		$tree = '<ul>' . PHP_EOL;

		foreach($cat_tree [$parent] as $v){
			$tree.= '<li><a href="'.$v[3].'">' .$v[1]. '</a>';
			$tree.=  get_tree($cat_tree, $v[0]). PHP_EOL;
			$tree.= '</li>'. PHP_EOL;         
		}

		$tree.= '</ul>'. PHP_EOL;
	} else return null;          

	return $tree;        
}


echo get_tree($cat_tree);
		