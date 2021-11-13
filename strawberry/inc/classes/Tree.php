<?php

namespace classes;

class Tree implements interRunningMain
{
    private $categories = [
		0 => [
			'id' => 1,
			'url' => 'index',
			'name' => 'Home',
			'parent' => 0
		],
		1 => [
			'id' => 2,
			'url' => 'news',
			'name' => 'News',
			'parent' => 0
		],
		2 => [
			'id' => 3,
			'url' => 'blog',
			'name' => "Blog",
			'parent' => 0
		],
		3 => [
			'id' => 4,
			'url' => 'mail',
			'name' => 'Mail',
			'parent' => 1
		],
		4 => [
			'id' => 5,
			'url' => 'map',
			'name' => 'Map',
			'parent' => 1
		]
	];

	private $parents  = [];
	private $patterns = ['/{(id|name|url|parent)}/', '/\[php\](.*?)\[\/php\]/'];
	private $template = '<a href="[php]cute_get_link($row, category)[/php]">{name}</a>';

    public function __construct( $categories = null )
	{
		$this->categories = $categories ?? $this->categories;
		
		if ( empty($this->categories) )
		{
			return false;
		}

		foreach ( $this->categories AS $row )
		{
			$this->parents[$row['parent']][] = $row;
		}
    }

    private function getTree( $parent = 0, $level = 0, $template = null, $result = '' )
	{
		if ( !isset($this->parents[$parent]) )
		{
			return null;
		}

		$template = $template ?? $this->template;
			 
		foreach ( $this->parents[$parent] AS $row )
		{	
			$level++;
			$result.= '<li>';
			$result.= preg_replace_callback( $this->patterns, function($m) use ($row)
			{
				return isset( $row[$m[1]] ) ?
					$row[$m[1]] : eval('return ' .$m[1]. ';');

			}, $template );

			$result.= $this->getTree($row['id'], $level);
			$result.= '</li>';
			$level--;
		}
		return sprintf ( '<ul>%s</ul>', $result );
    }
	
	public function run( $categories = [] )
	{
		return $this->getTree();
	}
	
	public function __toString()
	{
		return $this->getTree();
	}
}
