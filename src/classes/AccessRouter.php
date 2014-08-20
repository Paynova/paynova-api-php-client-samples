<?php

class AccessRouter {
	private $_render;
	private $_resourceReader;
	
	function __construct(Renderer $renderer, ResourceReader $resourceReader) {
		$this->_renderer = $renderer;
		$this->_resourceReader = $resourceReader;
	}
	
	function handleDocument($htmlFile, HttpRequest $httpRequest, HttpResponse $httpResponse) {
		if($htmlFile=="/")$htmlFile = "index.html";
		else $htmlFile = $htmlFile = preg_replace("/^\//", "", $htmlFile);
		
		$document = DocumentFactory::createDocument($htmlFile, $httpRequest, $httpResponse, $this->_resourceReader);
		
		$httpResponse->append($this->_renderer->renderDocument($document, $_SESSION));
	}
}