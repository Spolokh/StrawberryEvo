<?php
/**
 * @package Public
 * @access public
 */

namespace classes;

use classes\Blitz;
use classes\CuteParser;

final class Main extends CuteParser implements interRunningMain
{
	const OFFSET = 0;
	const NUMBER = 6;
	const MODULE = 'main';

	private $tpl = [];
	private $category;
	private $output;

	public function __construct (array $config = [])
	{
		parent::__construct($config);
		$this->run();
	}

	public function run()
	{
		$this->category = category_get_id($_GET['category']);

		if (!$query = $this->sql())
		{
			return;
		}

		foreach ($query as $k => $row)
		{
			if ($this->category && $this->category == $row['category'])
			{
				continue;
			}

			if ($category!= $row['category'])
			{   
				$category = $row['category'];
				$this->tpl['row'][$k]['category'] = category_get_title($category);
			}

			$this->tpl['row'][$k]['link']  = cute_get_link($row);
			$this->tpl['row'][$k]['date']  = langdate('d.m.Y H:i:s', $row['date']);
			$this->tpl['row'][$k]['image'] = UPIMAGE.'/posts/'.($row['image'] ? $row['image'] : 'default.png');
			$this->tpl['row'][$k]['title'] = $this->value($row['title'], true);
			$this->tpl['row'][$k]['short'] = $this->value($row['short'], true);
			$this->tpl['row'][$k]['comms'] = (int) $row['comments'];
			$this->tpl['row'][$k]['views'] = (int) $row['views'];
		}

		$view = themes_directory.'/test.tpl';
		$this->ouput = (new Blitz($view))->parse($this->tpl);
	}

	private function sql()
	{
		$field = '`id`, `date`, `title`, `image`, `category`, `url`, `type`, `views`, `comments`, `short`';
		$query = "(SELECT $field FROM `" .PREFIX. "news` JOIN `" .PREFIX. "story` USING(`id`)
				WHERE category = 2 AND hidden = 0 AND date <= ".time." ORDER BY 1 DESC LIMIT ".self::NUMBER.")
			UNION (SELECT $field FROM `" .PREFIX. "news` JOIN `" .PREFIX. "story` USING(`id`)
				WHERE category = 3 AND hidden = 0 AND date <= ".time." ORDER BY 1 DESC LIMIT ".self::NUMBER.")
			UNION (SELECT $field FROM `" .PREFIX. "news` JOIN `" .PREFIX. "story` USING(`id`)
				WHERE category = 5 AND hidden = 0 AND date <= ".time." ORDER BY 1 DESC LIMIT ".self::NUMBER.")
			UNION (SELECT $field FROM `" .PREFIX. "news` JOIN `" .PREFIX. "story` USING(`id`)
				WHERE category = 6 AND hidden = 0 AND date <= ".time." ORDER BY 1 DESC LIMIT ".self::NUMBER.")
		";

		$query = $this->query($query);
		return reset($query) ? $query : false;
	}

	public function __toString()
	{
		return $this->ouput;
	}
}
