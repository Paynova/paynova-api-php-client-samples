<?php
use Paynova\request\RequestRefundPayment;

class RefundPaymentDocument extends DocumentImpl {

	public function __construct($unprocessedContent) {
		parent::__construct($unprocessedContent);
	}

	public function process(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {
		
    	$action = $request->getParameter("action");
    	if($action=="refund-payment") {
    		$response->setContentType("application/json");
    		parent::setContentType(DocumentImpl::CONTENT_TYPE_JSON);
    		parent::setEmbedContentInTemplate(false);
    		$this->refundPayment( $request,  $response, $resourceReader);
    		
		}else {
			$response->setContentType("text/html");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), parent::getUnprocessedContent());
		}

	}
	
	private function refundPayment(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {
		$orderId = parent::getSessionAttribute("orderId");
		$transactionId = parent::getSessionAttribute("transactionId");
		$amount = parent::getSessionAttribute("amount"); 
		
		$requestRefundPayment = RequestRefundPayment::factory(array(
				"orderId"=>$orderId,
				"transactionId"=>$transactionId,
				"totalAmount"=>$amount
		));
		
		
		$responseRefundPayment = $requestRefundPayment->request();
		
		
		$this->setRefundPaymentProcessed($responseRefundPayment->status()->isSuccess(), $responseRefundPayment->getHttpEvent()->responseBody());
	}
	
	private function setRefundPaymentProcessed($isSuccess, $status) {
		
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "<br />Code used to call API server:");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:refund-payment}");
		
		if($isSuccess){
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-success\">Request was sucessful, now all response is wrapped refundPaymentResponse</p>");		
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE,"html"), "{snippet-file:refund-payment-response-object-usage}");
			
		}else{
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-warning\">The request containted errors</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:status-in-exception-usage}");
		}
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "Raw response received from API server:<br />");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_DISPLAY_AS_CODE|ContentDescriptor::ACTION_URL_ENCODE,"html"), "status-here");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"statusObject"), json_decode($status));
	}

}
