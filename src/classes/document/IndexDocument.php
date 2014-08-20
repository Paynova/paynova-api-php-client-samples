<?
class IndexDocument extends DocumentImpl{
	
	function __construct($unprocessedHtml) {
		 parent::__construct($unprocessedHtml);
	}
	
	public function process(HttpRequest $httpRequest, HttpResponse $httpResponse, ResourceReader $resourceReader) {
		$httpResponse->setContentType("text/html");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), parent::getUnprocessedContent());
	}

	public function getHtmlFileName() {
		// TODO Auto-generated method stub
		return "index.html";
	}

}
