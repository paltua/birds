<?php
/**
* This class contains all api method
*/
class Api
{
	private static $mplaceSecreteKey = 'uYMsnSwvpF7TWr8LrKlmMhCw3GZ5niBPg7w0EiJfwdnGUa4VpB28';
	private static $url = 'http://boostenergyefficiency.com/apicall/index/';
	//private static $url = 'http://stage.en-view.com/DIY3api/index.php';
	private $_mplaceApiKey;
	private $_postData;
	public function __construct(){
		$this->_mplaceApiKey = '';
		$this->_postData = array();
	}

	public function callDiy1($dataArray = array(),$keys = 'B'){
		$this->_mplaceApiKey = 'al0RYN8W2sECyJG2NNlBSDThxwZurjSBFTsDHBAJ3flCZ2EMP024';
		$returnArray['status'] = 'error';
		$returnArray['data'] = 'Please provide correct data.';
	    if(count($dataArray) > 0 && $keys != ''){
			$arrF = array('filepath' => $dataArray, "keys" => $keys);
			$this->_postData['dataset'] = json_encode($arrF);
	      	$resultArr = $this->_call();
	      	if(isset($resultArr['status']) && isset($resultArr['message'])){
	        	$returnArray['status'] = 'success';
	        	$returnArray['data'] = $resultArr['message'];
	      	}
	    } 
	    return $returnArray;
	}

	public function callDiy3($dataSet = array()){
		$this->_mplaceApiKey = 'al0RYN8W2sECyJG2NNlBSDThxwZurjSBFTsDHBAJ3flCZ2EMP024';
		$returnArray['status'] = 'error';
		$returnArray['data'] = 'Please provide correct data.';
	    if(count($dataSet)>0){
			$this->_postData['dataset'] = json_encode($dataSet);
	      	$resultArr = $this->_call();
	      	if(isset($resultArr['status']) && isset($resultArr['message'])){
	        	$returnArray['status'] = 'success';
	        	$returnArray['data'] = $resultArr['message'];
	      	}
	    } 
	    return $returnArray;
	}


	private function _call(){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array( 
		            'MPLACE-API-KEY: '.$this->_mplaceApiKey,
		            'MPLACE-SECRETE-KEY: '.self::$mplaceSecreteKey));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postData);
		$result = curl_exec($ch);
		curl_close ($ch);
		return json_decode($result,true);
	}
}

?>

