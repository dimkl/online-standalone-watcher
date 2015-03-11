<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'userseries.php';

$adapter = new JsonAdapter('./userdata.json');
$adapterRss = new RssAdapter();

$adapter->fetch();
$adapter->specializeData("dimitris")->specializeData("series");

if(!$adapterRss->isCacheExpired()){
	$adapterRss = new RssAdapter('./cacheRss.xml');
	$adapterRss->fetch();
}else{
	for ($i = 1; $i < 10; $i++) {
		$adapterRss->source='http://kickass.to/tv/?rss='. $i;
    	$adapterRss->fetch(true);
	}
}
$adapterRss->save();

$seriesObj = new UserSeries($adapterRss, $adapter);
// mapping 
if (isset($_GET["task"]) && method_exists($seriesObj, $_GET["task"])) {
    $seriesObj->{$_GET["task"]}();
}
$changes = $seriesObj->check();

?>
<head>
<base href="/online-watcher/">
</head>
<body>
<?php
foreach ($changes as $changed): ?>
<div class="unwatched-series">
	<div>
	<span>Σειρά</span>
	<span><?php
    echo $changed["series"] ?></span>
	</div>
	<div>
	<span>Σεζόν</span>
	<span><?php
    echo $changed["season"] ?></span>
	</div>
	<div>
	<span>Επεισόδιο</span>
	<span><?php
    echo $changed["episode"] ?></span>
	</div>
	<a href="index.php?task=watch&series=<?php
    echo $changed["series"] ?>&season=<?php
    echo $changed["season"] ?>&episode=<?php
    echo $changed["episode"] ?>"> Watched </a>
	<?php foreach($changed["links"] as $index=>$link):?>
    <a href="<?php echo $link?>"> Torrent link-<?php echo $index?></a>
	<?php endforeach; ?>
</div>
<?php
endforeach; ?>
</body>