<?

class ContentDescriptor {
	const ACTION_NONE = 0;
	const ACTION_URL_ENCODE = 1;
	const ACTION_DISPLAY_AS_CODE = 2;
	
	private $_action = self::ACTION_NONE;
	private $_key;
	function __construct($action, $key) {
		$this->_action = $action;
		$this->_key = $key;
	}
	
	public function getKey() {
		return $this->_key;
	}
	
	public function doAction($action) {
		return ($this->_action&$action)==$action;
	}
}
