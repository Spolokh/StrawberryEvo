<?php
 
class Vkapi extends CuteParser
{
	private
	  $App
	, $Sec
	, $Res = [] 
	, $Ver = '4.0'
	, $Url = 'api.vk.com/api.php'
	, $params = []
	, $Config = []
	, $result = '';
	
	public $format = '';

	public function __construct($config) 
	{
		$this->App = $config['vk_api_uid'];
		$this->Sec = $config['vk_api_sec'];	
		$this->Url = strstr($this->Url, "http://") ? $this->Url : "http://".$this->Url;
	}
	
	public function api($method, array $params = [])
	{
		$params['api_id'] 	 = $this->App;
		$params['v'] 		 = $this->Ver;
		$params['method'] 	 = $method;
		$params['timestamp'] = time();
		$params['random'] 	 = rand(0, 10000);  
		
		ksort($params);
		$sig = '';
		
		foreach($params as $k => $v) {
			$sig.= $k.'='.$v;
		}
		
		$sig  		  .= $this->Sec;
		$params['sig'] = md5($sig);
		$query 	       = $this->Url.'?'.$this->params($params);
		$this->result  = file_get_contents ($query);
		return $this;
	}
	
	public function xml(){		//return simplexml_load_string($this->result);
		return $this->result ? simplexml_load_string($this->result): false;
	}
	
	public function json($arg = false){
		return $this->result ? json_decode($this->result, $arg): false;
	}
	
	public function run(){
		return $this->result;
	}
	
	private function params($params){
	  
		foreach($params as $k => $v) {
			$this->Res[] = $k.'='.urlencode($v);
		}
		
		return implode('&', $this->Res);
	}

	public function __toString()
	{
		return $this->result;
	}
}
