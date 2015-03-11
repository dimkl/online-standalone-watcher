<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'adapters.php'; 
class UserSeries
{
    public $adapterUser;
    public $adapterSeries;

    public function __construct(IAdapter $adapterSeries,IAdapter $adapterUser) {
        $this->adapterSeries = $adapterSeries;
        $this->adapterUser = $adapterUser;
    }
    
    public function check() {
        $changes = array();
        $seriesNodes=$this->adapterSeries->getData();
        
        foreach ($seriesNodes as $seriesNode) {
            if (!preg_match('/(.*)s([0-9]{2})e([0-9]{2})/i', $seriesNode->title, $matches)) continue;
            if (count($matches) != 4) continue;
            //remove full match from array
            array_shift($matches);
            list($series, $season, $episode) = $matches;
            //trim data
            $series=trim(strtr($series,array('.'=>' ','-'=>' ')));
            //series is not being watched
            $userSeries=&$this->adapterUser->getData($series);
            if (empty($userSeries)) continue;
            
            //updated latest episodes
            $latest = &$userSeries->latest;
            $latest->season = $season;
            $latest->episode = $episode;
            //
            $this->adapterUser->save();
            //check for watched episode
            $watched = &$userSeries->watched;
            // find diffs of season and episode
            $seasonDiff = intval($season) - intval($watched->season);
            $episodeDiff = intval($episode) - intval($watched->episode);
            //check if episode is not new
            if ($seasonDiff < 0 || ($seasonDiff == 0 && $episodeDiff <= 0)) continue;
            //check for multiple rss rows for the same series
            if(isset( $changes[$series]) && !in_array((string)$seriesNode->link, $changes[$series]["links"])){
                $changes[$series]["links"][]=(string)$seriesNode->link;
            } else{
                $changes[$series] = array(
                 "series" =>(string) $seriesNode->title,
                 "season" => $season, 
                 "episode" => $episode,
                 "links" => [(string)$seriesNode->link]);
            }
        }
        return $changes;
    }
    
    public function watch() {
        if (!isset($_GET["series"], $_GET["season"], $_GET["episode"])) return;
        
        list($series, $season, $episode) = array($_GET["series"], $_GET["season"], $_GET["episode"]);
        //
        $this->adapterUser->fetch();
        $userSeries=$this->adapterUser->getData($series);
        if(empty($userSeries)) return;
        //update data
        $watched = &$userSeries->watched;
        $watched->season = $season;
        $watched->episode = $episode;
        //
        $this->adapterUser->save();
    }
}
