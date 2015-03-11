<?php 
class Cache
{
	private $source;
	private $config;
	private $entity;

	public function __construct($source){
		$this->source=$source;
	}

	public function save($data){
		$this->update();
		//save cached data 
	}
	public function update(){
		//refresh time
		$this->config->time_last_cached=time();
		file_put_contents($this->source,json_encode($this->config));
	}

	public function fetch(){
		$this->config=json_decode(file_get_contents($this->source));
	}

	public function isExpired(){
		$config=$this->config;
		if($this->entity!=null && isset($this->config->{$this->entity})){
			$config=$this->config->{$this->entity};
		}
		$diff=time() - $config->time_last_cached;

		if($diff>=$config->cache_timeout){
			$this->update();
			return true;
		}

		return false;
	}

}