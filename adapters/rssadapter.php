<?php
namespace OnlineWacther\Adapters;

/**
 *	RssAdapter Class description
 */
class RssAdapter implements IAdapter
{
    
    /**
     * [$data description]
     * @var array
     */
    private $data = array();
    
    /**
     * [$rssData description]
     * @var [type]
     */
    private $rssData;
    
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
        
        // if(filter_var($this->source, FILTER_VALIDATE_URL) && $this->cache!=null){
        // 	$this->cache->save($this->rssData->asXML());
        // }
        if (filter_var($this->source, FILTER_VALIDATE_URL)) {
            file_put_contents('./cache/rss.xml', $this->rssData->asXML());
        }
        
        //else{
        //	file_put_contents($this->source,$this->rssData->asXML());
        //}
        
    }
    
    /**
     * [fetch description]
     * @param  boolean $incremental [description]
     * @return [type]               [description]
     */
    public function fetch($incremental = false) {
        $this->data = $incremental ? $this->data : array();
        
        $this->rssData = new \SimpleXmlElement(file_get_contents($this->source));
        foreach ($this->rssData->channel->item as $item) {
            array_push($this->data, $item);
        }
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
            $returned = isset($this->specialization[$parameter]) ? $this->specialization[$parameter] : array();
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
    
    /**
     * [isCacheExpired description]
     * @return boolean [description]
     */
    public function isCacheExpired() {
        $configFile = './config.json';
        
        //
        $config = json_decode(file_get_contents($configFile));
        $diff = time() - $config->time_last_cached;
        
        if ($diff >= $config->cache_timeout) {
            
            //refresh cache
            $config->time_last_cached = time();
            file_put_contents($configFile, json_encode($config));
            
            //
            return true;
        }
        return false;
    }
    
    // public function activateCaching(Cache $cache){
    // 	$this->cache=$cache;
    
    // }
    // public function deactivateCaching(){
    // 	$this->cache=null;
    // }
    
    
}
