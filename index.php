<?php
define("BASEPATH",dirname(__FILE__).DIRECTORY_SEPARATOR);

require BASEPATH.'controllers'.DIRECTORY_SEPARATOR.'seriescontroller.php';
require BASEPATH.'adapters'.DIRECTORY_SEPARATOR.'jsonadapter.php';
require BASEPATH.'adapters'.DIRECTORY_SEPARATOR.'rssadapter.php';

$usersDataFile='./data/users.json';
$rssCacheFile='./cache/rss.xml';

$adapter = new JsonAdapter($usersDataFile);
$adapterRss = new RssAdapter();

$adapter->fetch();
$adapter->specializeData("dimitris")->specializeData("series");

if(!$adapterRss->isCacheExpired()){
	$adapterRss = new RssAdapter($rssCacheFile);
	$adapterRss->fetch();
}else{
	for ($i = 1; $i < 10; $i++) {
		$adapterRss->source='http://kickass.to/tv/?rss='. $i;
    	$adapterRss->fetch(true);
	}
	$adapterRss->save();
}
$seriesObj = new SeriesController($adapterRss, $adapter);
// mapping 
if (isset($_GET["task"]) && method_exists($seriesObj, $_GET["task"])) {
    $seriesObj->{$_GET["task"]}();
}
$changes = $seriesObj->check();

require BASEPATH.'templates'.DIRECTORY_SEPARATOR.'changed-template.php'; 
?>
