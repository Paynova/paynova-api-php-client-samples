<?php 
use Paynova\request\RequestAnnulAuthorization;

class AnnulAuthorizationDocument extends DocumentImpl {

	public function __construct($unprocessedContent) {
		parent::__construct($unprocessedContent);
	}

	public function process(HttpRequest $request,HttpResponse $response, ResourceReader $resourceReader) {
		$action = $request->getParameter("action");
    	if($action=="annul-authorization"){
    		$response->setContentType("application/json");
    		parent::setContentType(DocumentImpl::CONTENT_TYPE_JSON);
    		parent::setEmbedContentInTemplate(false);
    		$this->annulAuthorization( $request,  $response, $resourceReader);
		}else {
			$response->setContentType("text/html");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), parent::getUnprocessedContent());
		}
		
	}
	
	public function annulAuthorization(HttpRequest $request,HttpResponse $response, ResourceReader $resourceReader) {
		$orderId = parent::getSessionAttribute("orderId");
		$transactionId = parent::getSessionAttribute("transactionId");
		$totalAmount = parent::getSessionAttribute("amount");
		
		$requestAnnulAuthorization = new RequestAnnulAuthorization();
		$requestAnnulAuthorization->orderId($orderId)
		->transactionId($transactionId)
		->totalAmount($totalAmount);
		
		$responseAnnulAuthorization = $requestAnnulAuthorization->request();
		
		$this->setAnnulAuthorizationProcessed($responseAnnulAuthorization->status()->isSuccess(),$responseAnnulAuthorization->getHttpEvent()->responseBody());
	}
	
	public function setAnnulAuthorizationProcessed($isSuccess, $status){
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "<br />Code used to call API server:");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:annul-authorization}");
		if($isSuccess){
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-success\">Request was sucessful.</p>");
			
		}else {
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-warning\">The request containted errors</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:status-in-exception-usage}");
			
		}
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "The raw response from the API server:<br />");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_DISPLAY_AS_CODE|ContentDescriptor::ACTION_URL_ENCODE,"html"), "status-here");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"statusObject"), json_decode($status));
	}
}
