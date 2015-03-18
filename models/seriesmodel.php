<?php
namespace OnlineWacther\Models;
/**
 *
 */
class SeriesModel
{
    
    /**
     * [$watched description]
     * @var [type]
     */
    public $watched;
    
    /**
     * [$latest description]
     * @var [type]
     */
    public $latest;
    
    /**
     * [$episodes description]
     * @var [type]
     */
    public $episodes;
    
    /**
     * [__construct description]
     * @param stdClass      $watched  [description]
     * @param stdClass|null $latest   [description]
     * @param Array|null    $episodes [description]
     */
    public function __construct(stdClass $watched, stdClass $latest = null, Array $episodes = null) {
        $this->watched = $watched;
        $this->latest = !is_object($latest) ? new stdClass() : $latest;
        $this->episodes = !is_array($episodes) ? array() : $episodes;
    }
    
    /**
     * [__toString description]
     * @return string [description]
     */
    public function __toString() {
        return json_encode(array("watched" => $this->watched, "latest" => $this->latest, "episodes" => $this->episodes));
    }
}
