<?php
require_once BASEPATH .'adapters' . DIRECTORY_SEPARATOR . 'iadapter.php';
require_once BASEPATH . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'seriesmodel.php';
require_once BASEPATH . DIRECTORY_SEPARATOR . 'viewmodels' . DIRECTORY_SEPARATOR . 'seriesviewmodel.php';

class SeriesController
{
    public $adapterUser;
    public $adapterSeries;
    
    public function __construct(IAdapter $adapterSeries, IAdapter $adapterUser) {
        $this->adapterSeries = $adapterSeries;
        $this->adapterUser = $adapterUser;
    }
    
    public function check() {
        $changes = array();
        $seriesNodes = $this->adapterSeries->getData();
        $updated = false;
        
        foreach ($seriesNodes as $seriesNode) {
            // match epsiode and season of series
            if (!preg_match('/(.*)s([0-9]{2})e([0-9]{2})/i', $seriesNode->title, $matches)) continue;
            if (count($matches) != 4) continue;
            
            // remove full match from array
            array_shift($matches);
            list($series, $season, $episode) = $matches;
            
            // trim data
            $series = trim(strtr($series, array('.' => ' ', '-' => ' ')));

            // series is not being watched
            $userSeries = & $this->adapterUser->getData($series);
            if (empty($userSeries)) continue;
            $updated = true;
            
            // update latest episodes
            $latest = & $userSeries->latest;
            $latest->season = $season;
            $latest->episode = $episode;
            
            // update episode list
            $episodes = & $userSeries->episodes;
            $key = "S{$season}E{$episode}";
            if (!isset($episodes->$key)) $episodes->$key = (string)$seriesNode->link;
            
            // check for watched episode
            $watched = & $userSeries->last_watched;
            
            // find diffs of season and episode
            $seasonDiff = intval($season) - intval($watched->season);
            $episodeDiff = intval($episode) - intval($watched->episode);
            
            // check if episode is not new
            if ($seasonDiff < 0 || ($seasonDiff == 0 && $episodeDiff <= 0)) continue;
            
            // check for multiple rss rows for the same series
            if (isset($changes[$series]) && !in_array((string)$seriesNode->link, $changes[$series]->links)) {
                $changes[$series]->links[] = (string)$seriesNode->link;
            } 
            else {
                $changes[$series] = new SeriesViewModel ((string)$seriesNode->title,$season, $episode,[(string)$seriesNode->link]);
            }
        }
        // save
        if ($updated) $this->adapterUser->save();
        
        return $changes;
    }
    
    public function watch() {
        if (!isset($_GET["series"], $_GET["season"], $_GET["episode"])) return;
        
        list($series, $season, $episode) = array($_GET["series"], $_GET["season"], $_GET["episode"]);
        
        //
        $this->adapterUser->fetch();
        $userSeries = $this->adapterUser->getData($series);
        if (empty($userSeries)) return;
        
        //update data
        $watched = & $userSeries->watched;
        $watched->season = $season;
        $watched->episode = $episode;
        
        //
        $this->adapterUser->save();
    }
    
    public function create() {
        if (!isset($_GET["series"], $_GET["season"], $_GET["episode"])) return;
        list($series, $season, $episode) = array($_GET["series"], $_GET["season"], $_GET["episode"]);
        
        $this->adapterUser->fetch();
        $userSeries = $this->adapterUser->getData($series);
        if (!empty($userSeries)) return;
        
        //update data
        $watched = new stdClass();
        $watched->season = $season;
        $watched->episode = $episode;
        
        $new_series = & $this->adapterUser->getData();
        $new_series->$series = new SeriesModel($watched);
        
        $this->adapterUser->save();
    }
}
