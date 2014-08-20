<?
class ResourceReader {
	
	private $_callbackStoreFile = null;
	
	function __construct() {
	}
	
	public function getContentsOfFile($file) {
		$contents = null;
		$file=str_replace("//","/","resources/".$file);
		if(file_exists($file)){
			$contents = file_get_contents($file);
		}
		return $contents;
	}
	
	public function saveCallback($type, $sessionId, $value, $allowDuplicate) {
		$num = "";
		if($type=="PAYMENT"){
			
			$properties = $this->getCallbackStoragePropertyFile();
			$nr = 0;
			for($i=1;$i<=3;$i++){
				
				$key=$this->getCallbackStoreName($type,$sessionId,$i);
				if(!$properties->containsKey($key)){
					$nr=$i;
					break;
				}
			}
			$num="".$nr;
		}
		$key = $this->getCallbackStoreName($type,$sessionId,$num);
		$callbackFile =$this->getCallbackStoreFileName();
		
		file_put_contents($callbackFile,$key."=".$value."\n",FILE_APPEND);
		
	}
	
	
	private function getCallbackStoreName($type, $sessionId, $num){
		return $type.(($num=="")?"":"_".$num)."_".$sessionId;
	}
	public function getSavedCallback($type, $sessionId) {
		
		$key = $type."_".$sessionId;
		$properties = $this->getCallbackStoragePropertyFile();
		if($properties == null || !$properties->containsKey($key))return "";
		return $properties->getProperty($key);
	}
	
	private function getCallbackStoragePropertyFile(){
		$callbackFile = $this->getCallbackStoreFileName();
		$contents = file_get_contents($callbackFile);
		$properties = new Properties();
		$properties->load($contents);
		return $properties;
	}
	
	private function getCallbackStoreFileName() {
		if($this->_callbackStoreFile == null) {
			$properties = new Properties();
			$properties->loadByFile("../paynova-samples.properties");
			$this->_callbackStoreFile = $properties->getProperty("paynova.api.callback-store-file");
		}
		if($this->_callbackStoreFile == "") {
			echo "The property paynova.api.callback-store-file did not exist in paynova-samples.properties";
		}
		return $this->_callbackStoreFile;
	}
}	
