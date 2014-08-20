<?

interface Document {
	
	function process(HttpRequest $httpRequest, HttpResponse $httpResponse, ResourceReader $resourceReader);
	
	//public Map<ContentDescriptor, String> getContent();
	function getContent();
	
	function embedContentInTemplate();
	
	function getShowHeader();
	
	function getShowFooter();
	
	function getHttpResponseCode();
	
	function getFormatted();
	
}
