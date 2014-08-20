<?php
use Paynova\request\RequestFinalizeAuthorization;

class FinalizeAuthorizationDocument extends DocumentImpl {

	public function __construct($unprocessedContent) {
		parent::__construct($unprocessedContent);
		// TODO Auto-generated constructor stub
	}

	public function process(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {
		$action = $request->getParameter("action");
    	if($action=="finalize-authorization") {
    		$response->setContentType("application/json");
    		parent::setContentType(parent::CONTENT_TYPE_JSON);
    		parent::setEmbedContentInTemplate(false);
    		$this->finalizeAuthorization( $request,  $response, $resourceReader);
    		
		}else {
			$response->setContentType("text/html");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), parent::getUnprocessedContent());
		}

	}
	
	private function finalizeAuthorization(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {
		$orderId = parent::getSessionAttribute("orderId");
		$transactionId = parent::getSessionAttribute("transactionId");
		$amount = parent::getSessionAttribute( "amount"); 
		
		$finalizeAuthorizationRequest = RequestFinalizeAuthorization::factory(array(
				"orderId"=>$orderId,
				"transactionId"=>$transactionId,
				"totalAmount"=>$amount
		));
		
		$finalizeAuthorizationResponse = $finalizeAuthorizationRequest->request();
		$status = $finalizeAuthorizationResponse->getHttpEvent()->responseBody();
		$isSuccess = $finalizeAuthorizationResponse->status()->isSuccess();
		
		$this->setFinalizeAuthorizationProcessed($isSuccess, $status);
	}
	
	private function setFinalizeAuthorizationProcessed($isSuccess, $status) {
		
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "<br />Code used to call API server:");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:finalize-authorization}");
		if($isSuccess){
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-success\">Request was sucessful, now all response is wrapped finalizeAuthorizationResponse</p>");		
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE,"html"), "{snippet-file:finalize-authorization-response-object-usage}");
		}else{
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-warning\">The request containted errors</p>");
		}
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "Raw response received from API server (or Exception in Http):<br />");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_DISPLAY_AS_CODE|ContentDescriptor::ACTION_URL_ENCODE,"html"), "status-here");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"statusObject"), json_decode($status,true));
	}

}
