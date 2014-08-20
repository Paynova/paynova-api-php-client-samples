<?




abstract class DocumentImpl implements Document {
	
	const CONTENT_TYPE_HTML = 1;
	const CONTENT_TYPE_JSON = 2;
	
	private $_unprocessedContent;
	private $_processedContent = array();
	
	private $_contentType;
	
	private $_embedInTemplate;
	private $_showHeader;
	private $_showFooter;
	
	private $_paynovaClient; 
	
	function __construct($unprocessedContent) {
		$this->_unprocessedContent = $unprocessedContent;
		$this->_embedInTemplate = true;
		$this->setShowHeader(true);
		$this->setShowFooter(true);
		$this->_contentType = self::CONTENT_TYPE_HTML;
		$this->_paynovaClient = null;
	}
	
	protected function getUnprocessedContent() {
		return $this->_unprocessedContent ;
	}
	
	protected function putProcessedContent(ContentDescriptor $descriptor, $content) {
		$this->_processedContent[count($this->_processedContent)]=array($descriptor,$content);
	}
	
	protected function setProcessedContent($content, $embedInTemplate, $contentType) {
		$this->_processedContent = $content;
		$this->_embedInTemplate = $embedInTemplate;
		$this->_contentType = $contentType;
	}
	public function &getContent() {
		return $this->_processedContent;
	}
	protected function setContentType($contentType) {
		$this->_contentType = $contentType;
	}
	protected function getContentType() {
		return $this->_contentType;
	}
	public function embedContentInTemplate() {
		return $this->_embedInTemplate;
	}
	
	public function setEmbedContentInTemplate($embedInTemplate) {
		$this->_embedInTemplate = $embedInTemplate;
	}
	
	
	public function getFormatted() {
		$map = array();
		foreach($this->_processedContent as $entry){
			$key = $entry[0]->getKey();
			$value = $entry[1];
			if(!isset($map[$key])) {
				$map[$key]=$value;
			} else {
				$map[$key].=$value;
			}
			
		}
		return $this->buildString($map);
	}
	

	private function buildString($map) {
		$builder = "";
		
		if($this->getContentType()==self::CONTENT_TYPE_JSON){
			$builder = json_encode($map);
		}else{
			$builder=implode("",$map);
		}
		return $builder;
	}
	
	public function getHttpResponseCode() {
		return 200;
	}
	
	protected function storeSessionAttribute($key, $value) {
		 $_SESSION[$key]=$value;
	}
	
	protected function getSessionAttribute($key) {
		return (array_key_exists($key,$_SESSION))?$_SESSION[$key]:"";
	}

	public function getShowHeader() {
		return $this->_showHeader;
	}

	protected function setShowHeader($showHeader) {
		$this->_showHeader = $showHeader;
	}

	public function getShowFooter() {
		return $this->_showFooter;
	}

	protected function setShowFooter($showFooter) {
		$this->showFooter = $showFooter;
	}
}
