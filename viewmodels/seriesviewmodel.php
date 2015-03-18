<?php

namespace OnlineWacther\ViewModels;
/**
 *
 */
class SeriesViewModel
{
    
    /**
     * [$series description]
     * @var [type]
     */
    public $series;
    
    /**
     * [$season description]
     * @var [type]
     */
    public $season;
    
    /**
     * [$episode description]
     * @var [type]
     */
    public $episode;
    
    /**
     * [$links description]
     * @var array
     */
    public $links = array();
    
    /**
     * [__construct description]
     * @param [type] $series  [description]
     * @param [type] $season  [description]
     * @param [type] $episode [description]
     * @param Array  $links   [description]
     */
    public function __construct($series, $season, $episode, Array $links) {
        $this->series = $series;
        $this->season = $season;
        $this->episode = $episode;
        $this->links = $links;
    }
}
