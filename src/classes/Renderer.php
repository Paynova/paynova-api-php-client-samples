<?


class Renderer {
	
	const TEMPLATE_TWITTER_BOOTSTRAP = "bootstrap";
	
	const CONSTANT_HEADER = "{header}";
	const CONSTANT_CONTENT = "{content}";
	const CONSTANT_FOOTER = "{footer}";
	const CONSTANT_TOPMENU = "{topmenu}";
	const CONSTANT_WEBROOT_PATH = "{webroot-path}";
	const CONSTANT_TEMPLATE_PATH = "{template-path}";
	const CONSTANT_VARIABLES_BOX = "{variables-box}";
	
	const WEBROOT_FOLDER = "resources/public";
	const WEBROOT_TEMPLATE_FOLDER = "resources/public/templates";
	
	private $_template;
	
	private $_templateContents;
	
	private $_resourceReader;
	
	function __construct(ResourceReader $resourceReader, $template) {
		$this->_resourceReader = $resourceReader;
		$this->_template = $template;
		
		$this->setUpTemplate();
		
		
	}
	
	private function setUpTemplate() {
		$this->_templateContents = $this->getTemplateResource($this->_template, "html/index.html");
		
		
		$this->_templateContents = str_replace(self::CONSTANT_WEBROOT_PATH, self::WEBROOT_FOLDER,$this->_templateContents);
		$this->_templateContents = str_replace(
							self::CONSTANT_TEMPLATE_PATH, 
							self::WEBROOT_TEMPLATE_FOLDER."/".$this->_template,
							$this->_templateContents);
		
		
	}
	
	private function replaceSnippets($str){
		$str = preg_replace(
			"/\{code-snippet\}([^€]*?)\{\/code-snippet\}/",
			"<pre class='prettyprint lang-php'>$1</pre>",
			$str
			);
		$str = $this->loadAndReplaceSnippetFiles($str);	
		return $str;
	}
	
	private function loadAndReplaceSnippetFiles($str) {
		$matches = array();
		preg_match_all("/\{snippet-file:([^\}]*?)\}/",$str,$matches);
		
		for($i=0;$i<count($matches[0]);$i++){
			$codeSnippet = $this->_resourceReader->getContentsOfFile(
				"/public/code-snippets/".$matches[1][$i].".txt"
			);
			if($codeSnippet != null) {
				$str = str_replace($matches[0][$i],$codeSnippet, $str);
			}
		}
		
		return $str;
	}
	
	
	
	public function renderDocument(Document &$document, $variables){
		$map = &$document->getContent();
		
		for($i=0;$i<count($map);$i++){
			
			$entry = $map[$i];
			//ContentDescriptor 
			$desc = $entry[0];
			if($desc->doAction(ContentDescriptor::ACTION_DISPLAY_AS_CODE)) {
				
				$map[$i][1] = "{code-snippet}".$map[$i][1]."{/code-snippet}";
				
				
				$map[$i][1] = $this->replaceSnippets( $map[$i][1]);
				//echo $map[$i][1]."";
				
				$map[$i][1] = $this->replaceVariables( $variables, $map[$i][1]);
				
				 
			}
			if($desc->doAction(ContentDescriptor::ACTION_URL_ENCODE)) {
				$map[$i][1] = urlencode($map[$i][1]);
			}
		}
		$contents = $document->getFormatted();
		
		if($document->embedContentInTemplate()){
			$contents = str_replace(self::CONSTANT_CONTENT, $contents, $this->_templateContents);
		}
		if($document->getShowHeader()) {
			$contents = str_replace(
				self::CONSTANT_HEADER, 
				$this->_resourceReader->getContentsOfFile("/public/components/header.html"), 
				$contents);
			$contents = str_replace(
				self::CONSTANT_TOPMENU, 
				$this->_resourceReader->getContentsOfFile("/public/components/topmenu.html"), 
				$contents);
		}
		if($document->getShowFooter()) {
			$contents = str_replace(
				self::CONSTANT_FOOTER, 
				$this->_resourceReader->getContentsOfFile("/public/components/footer.html"), 
				$contents);
		}
		$contents = str_replace(
			self::CONSTANT_VARIABLES_BOX, 
			$this->_resourceReader->getContentsOfFile("/public/components/variables-box.html"),
			$contents);
		$contents = $this->replaceVariables($variables, $contents);
		$contents = $this->replaceSnippets($contents);
		$contents = preg_replace("/\{[a-zA-Z:]*?\}/", "",$contents);
		
		return $contents;
	}
	
	public function getTemplateResource($template, $file){
		$path = "/public/templates/".$template."/".$file;
		return $this->_resourceReader->getContentsOfFile($path);
	}
	
	public function replaceVariables($pairs, $template) {
		foreach($pairs as $key=>$value){
			$template = str_replace("{".$key."}", $value, $template);
		}
		return $template;
	}
}
