<?php
/**
 * @package Show
 * @access private
 */

include_once 'strawberry/head.php';

header('Content-type: text/xml');

$xml = new DOMDocument ('1.0', $config['charset']);
$xml ->formatOutput = true; //под вопросом

$set = $xml->createElement('urlset');
$set ->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
$xml ->appendChild($set);

$url = $xml->createElement('url');
$set ->appendChild($url);

$loc = $xml->createElement('loc', $config['http_script_dir']); 
$mod = $xml->createElement('lastmod', langdate('Y-m-d H:i:s', $config['timestamp_registered_site'])); 
$pri = $xml->createElement('priority', '1.0');

$url ->appendChild($loc);
$url ->appendChild($mod);
$url ->appendChild($pri);

$query = [ 'news', 'select' => ['id', 'url', 'date', 'author', 'title', 'type'],  'where' => ['hidden = 0'] ];
$query = $sql->select($query);

if ( !reset($query) )
{
	return $xml->saveXML();
}

foreach ($query as $row) { 
		
	$url = $xml->createElement('url');
	$set ->appendChild($url);

	$loc = $xml->createElement('loc', cute_get_link($row));
	$mod = $xml->createElement('lastmod', langdate('Y-m-d H:i:s', $row['date'])); 
	$pri = $xml->createElement('priority', '0.8');

	$url ->appendChild($loc);
	$url ->appendChild($mod);
	$url ->appendChild($pri);
}

echo $xml->saveXML();
