<?php
class HttpRequest{
	
	private $_postRequestParams = array();
	private $_getRequestParams = array();
	
	function __construct($postRequestParams,$getRequestParams) {
		$this->_postRequestParams = $postRequestParams;
		$this->_getRequestParams = $getRequestParams;
	}
	
	function getParameter($key) {
		return (array_key_exists($key,$this->_postRequestParams))?$this->_postRequestParams[$key]:"";
	}
	
	function getGetParameter($key) {
		return (array_key_exists($key,$this->_getRequestParams))?$this->_getRequestParams[$key]:"";
	}
	
	function getParameters(){
		return $this->_postRequestParams;
	}
}