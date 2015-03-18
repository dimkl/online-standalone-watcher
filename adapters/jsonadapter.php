<?php
namespace OnlineWacther\Adapters;

/**
 *
 */
class  JsonAdapter implements IAdapter
{
    
    /**
     * [$data description]
     * @var array
     */
    private $data = array();
    
    /**
     * [$specialization description]
     * @var array
     */
    private $specialization = array();
    
    /**
     * [$source description]
     * @var string
     */
    public $source = "";
    
    /**
     * [__construct description]
     * @param string $source [description]
     */
    public function __construct($source = "") {
        $this->source = $source;
    }
    
    /**
     * [save description]
     * @return [type] [description]
     */
    public function save() {
        file_put_contents($this->source, json_encode($this->data));
    }
    
    /**
     * [fetch description]
     * @param  boolean $incremental [description]
     * @return [type]               [description]
     */
    public function fetch($incremental = false) {
        if (!file_exists($this->source)) return;
        
        $this->data = json_decode(file_get_contents($this->source));
        
        unset($this->specialization);
        $this->specialization = & $this->data;
    }
    
    /**
     * [getData description]
     * @param  [type] $parameter [description]
     * @return [type]            [description]
     */
    public function &getData($parameter = null) {
        if (!is_null($parameter)) {
            $returned = isset($this->specialization->$parameter) ? $this->specialization->$parameter : array();
            return $returned;
        }
        return $this->specialization;
    }
    
    /**
     * [specializeData description]
     * @param  [type] $parameter [description]
     * @return [type]            [description]
     */
    public function specializeData($parameter) {
        if (isset($this->specialization->$parameter)) {
            
            $temp = & $this->specialization->$parameter;
            unset($this->specialization);
            
            $this->specialization = & $temp;
            unset($temp);
        }
        return $this;
    }
}
