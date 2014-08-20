<?
class NotFoundDocument extends DocumentImpl {

	function __construct($unproccessedHtml) {
		parent::__construct($unproccessedHtml);
	}

	public function process(HttpRequest $httpRequest, HttpResponse $response, ResourceReader $resourceReader) {
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), parent::getUnprocessedContent());
	}

	public function getHtmlFileName() {
		return "404.html";
	}
	
	public function getHttpResponseCode() {
		return 404;
	}

}
