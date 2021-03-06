<?php
namespace OnlineWacther\Controllers;

use \OnlineWacther\Adapters\IAdapter;
use \OnlineWacther\Patterns\APattern;

/**
 * SeriesController description
 */
class SeriesController
{
    
    /**
     * [$adapterUser description]
     * @var [type]
     */
    private $adapterUser;
    
    /**
     * [$adapterSeries description]
     * @var [type]
     */
    private $adapterSeries;
    
    /**
     * [$pattern description]
     * @var [type]
     */
    private $pattern;
    
    /**
     * [__construct description]
     * @param IAdapter $adapterSeries [description]
     * @param IAdapter $adapterUser   [description]
     * @param APattern $pattern       [description]
     */
    public function __construct(IAdapter $adapterSeries, IAdapter $adapterUser, APattern $pattern) {
        $this->adapterSeries = $adapterSeries;
        $this->adapterUser = $adapterUser;
        $this->pattern = $pattern;
    }
    
    /**
     * [check description]
     * @return [type] [description]
     */
    public function check() {
        $changes = array();
        $seriesNodes = $this->adapterSeries->getData();
        $updated = false;
        foreach ($seriesNodes as $seriesNode) {
            $matches = array();
            
            // match epsiode and season of series
            if (!$this->pattern->match($seriesNode->title, $matches)) continue;
            if (count($matches) != 3) continue;
            
            list($series, $season, $episode) = $matches;
            
            // trim data
            $series = trim(strtr($series, array('.' => ' ', '-' => ' ')));
            
            // series is not being watched
            $userSeries = & $this->adapterUser->getData($series);
            if (empty($userSeries)) continue;
            $updated = true;
            
            // update latest episodes
            if (!isset($userSeries->latest)) {
                $userSeries->latest = new stdClass();
            }
            $latest = & $userSeries->latest;
            $latest->season = $season;
            $latest->episode = $episode;
            
            // update episode list
            if (!isset($userSeries->episodes)) {
                $userSeries->episodes = new stdClass();
            }
            $episodes = & $userSeries->episodes;
            
            $key = "S{$season}E{$episode}";
            if (!isset($episodes->$key)) $episodes->$key = (string)$seriesNode->link;
            
            // check for watched episode
            if (!isset($userSeries->last_watched)) {
                $userSeries->last_watched = new stdClass();
            }
            $watched = & $userSeries->last_watched;
            
            if ($this->isEpisodeDifferent($season, $epsiode, $watched->episode, $watched->season)) continue;
            
            // check for multiple rss rows for the same series
            if (isset($changes[$series]) && !in_array((string)$seriesNode->link, $changes[$series]->links)) {
                $changes[$series]->links[] = (string)$seriesNode->link;
            } 
            else {
                $changes[$series] = new SeriesViewModel((string)$seriesNode->title, $season, $episode, [(string)$seriesNode->link]);
            }
        }
        
        // save
        if ($updated) $this->adapterUser->save();
        
        return $changes;
    }
    /**
     * [isEpisodeDifferent description]
     * @param  integer  $season          [description]
     * @param  integer  $epsiode         [description]
     * @param  integer  $watched_episode [description]
     * @param  integer  $watched_season  [description]
     * @return boolean                  [description]
     */
    private function isEpisodeDifferent($season, $epsiode, $watched_episode, $watched_season) {
        
        // find diffs of season and episode
        $seasonDiff = intval($season) - intval($watched_season);
        $episodeDiff = intval($episode) - intval($watched_episode);
        
        // check if episode is not new
        return ($seasonDiff < 0 || ($seasonDiff == 0 && $episodeDiff <= 0)) ? false : true;
    }
    
    /**
     * [watch description]
     * @return [type] [description]
     */
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
    
    /**
     * [create description]
     * @return [type] [description]
     */
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
