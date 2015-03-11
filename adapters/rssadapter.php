<?php 
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'iadapter.php';

class RssAdapter implements IAdapter{
	private $data=array();
	private $rssData;
	private $specialization=array();
	public $source="";

	public function __construct($source=""){
		$this->source=$source;
	}

	public function save(){
		// if(filter_var($this->source, FILTER_VALIDATE_URL) && $this->cache!=null){
		// 	$this->cache->save($this->rssData->asXML());
		// }
		if(filter_var($this->source, FILTER_VALIDATE_URL)){
			file_put_contents('./cache/rss.xml',$this->rssData->asXML());
		}
		//else{
		//	file_put_contents($this->source,$this->rssData->asXML());
		//}
	}

	public function fetch($incremental=false){
		$this->data=$incremental?$this->data:array();

		$this->rssData = new SimpleXmlElement(file_get_contents($this->source));
	    foreach ($this->rssData->channel->item as $item) {
	        array_push($this->data, $item);
	    }
	    unset($this->specialization);
	    $this->specialization=&$this->data;
	}
	public function &getData($parameter=null){
		if(!is_null($parameter)){
			$returned=isset($this->specialization[$parameter])?$this->specialization[$parameter]:array();
			return $returned;
		}
		return $this->specialization;
	}

	public function specializeData($parameter){
		if(isset($this->specialization->$parameter)){
			$temp=&$this->specialization->$parameter;
			unset($this->specialization);

			$this->specialization=&$temp;	
			unset($temp);
		}
		return $this;
	}

	public function isCacheExpired(){
		$configFile='./config.json';
		//
		$config=json_decode(file_get_contents($configFile));
		$diff=time() - $config->time_last_cached;

		if($diff>=$config->cache_timeout){
			//refresh cache
			$config->time_last_cached=time();
			file_put_contents($configFile,json_encode($config));
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