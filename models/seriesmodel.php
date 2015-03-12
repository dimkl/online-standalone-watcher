<?php
class SeriesModel
{
    public $watched;
    public $latest;
    public $episodes;
    
    public function __construct(stdClass $watched, stdClass $latest = null, Array $episodes = null) {
        $this->watched = $watched;
        $this->latest = !is_object($latest) ? new stdClass() : $latest;
        $this->episodes = !is_array($episodes) ? array() : $episodes;
    }
    
    public function __toString() {
        return json_encode(array("watched" => $this->watched, "latest" => $this->latest, "episodes" => $this->episodes));
    }
}