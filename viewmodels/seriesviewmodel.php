<?php
class SeriesViewModel
{
    public $series;
    public $season;
    public $episode;
    public $links = array();
    
    public function __construct($series, $season, $episode, Array $links) {
        $this->series = $series;
        $this->season = $season;
        $this->episode = $episode;
        $this->links = $links;
    }
}