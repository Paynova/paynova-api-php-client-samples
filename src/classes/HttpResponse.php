<?

class HttpResponse{
	private $_content = "";
	private $_contentType = "";
	function __construct(){
		
	}
	
	function setContentType($contentType) {
		$this->_contentType = $contentType;
	}
	function getContentType() {
		return $this->_contentType;
	}
	function append($content){
		$this->_content.=$content;	
	}
	
	function get(){
		return $this->_content;	
	}
	
	function setSessionVariable($key,$value) {
		$_SESSION[$key]=$value;
	}
}