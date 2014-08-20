<?

class Properties{
	private $_contents;
	private $_lines;
	private $_map = array();
	function __construct(){
		
	}
	
	public function load($contents){
		$this->_contents = $contents;
		$lines = explode("\n",$contents);
		foreach($lines as $line){
			$this->_lines[] = $line;
			preg_match("/^([^=]*?)=(.*?)$/",$line,$match);
			if(isset($match[1]) && isset($match[2])) {
				$this->_map[$match[1]] = $match[2];	
			}
		}
	}
	
	public function loadByFile($file){
		$contents = file_get_contents($file);
		$this->load($contents);	
	}
	
	public function containsKey($key){
		return array_key_exists($key,$this->_map);	
	}
	
	public function getProperty($key) {
		return (array_key_exists($key,$this->_map))?$this->_map[$key]:"";
	}
}