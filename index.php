<?php
use OnlineWacther\Adapters\JsonAdapter;
use OnlineWacther\Adapters\RssAdapter;
use OnlineWacther\Controllers\SeriesController;
use OnlineWacther\Patterns\ShowRssPattern;

define("BASEPATH", __DIR__ . DIRECTORY_SEPARATOR);

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'OnlineWatcher\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

$usersDataFile = './data/users.json';
$rssCacheFile = './cache/rss.xml';

$adapter = new JsonAdapter($usersDataFile);
$adapterRss = new RssAdapter();

$adapter->fetch();
$adapter->specializeData("dimitris")->specializeData("series");

if (!$adapterRss->isCacheExpired()) {
    $adapterRss = new RssAdapter($rssCacheFile);
    $adapterRss->fetch();
} 
else {
    $adapterRss = new RssAdapter('http://showrss.info/feeds/all.rss');
    $adapterRss->fetch();
    $adapterRss->save();
}

$seriesObj = new SeriesController($adapterRss, $adapter, new ShowRssPattern());

// mapping
if (isset($_GET["task"]) && method_exists($seriesObj, $_GET["task"])) {
    $seriesObj->{$_GET["task"]}();
}
$changes = $seriesObj->check();

require BASEPATH . 'templates' . DIRECTORY_SEPARATOR . 'changed-template.php';
?>
