<?php 
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'iadapter.php';

class JsonAdapter implements IAdapter{
	private $data=array();
	private $specialization=array();
	public $source="";
	
	public function __construct($source=""){
		$this->source=$source;
	}

	public function save(){
		file_put_contents($this->source, json_encode($this->data));
	}

	public function fetch($incremental=false){
		if(!file_exists($this->source)) return;

		$this->data = json_decode(file_get_contents($this->source));

		unset($this->specialization);
		$this->specialization=&$this->data;
	}

	public function &getData($parameter=null){
		if(!is_null($parameter)){
			$returned=isset($this->specialization->$parameter)?$this->specialization->$parameter:array();
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
} 