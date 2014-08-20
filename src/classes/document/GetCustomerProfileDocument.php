<?php
use Paynova\request\RequestGetCustomerProfile;

class GetCustomerProfileDocument extends DocumentImpl {

	public function __construct($unprocessedContent) {
		parent::__construct($unprocessedContent);
	}

	public function process(HttpRequest $request,HttpResponse $response, ResourceReader $resourceReader) {
		$action = $request->getParameter("action");
    	
    	if($action=="get-customer-profile"){
    		$response->setContentType("application/json");
    		parent::setContentType(DocumentImpl::CONTENT_TYPE_JSON);
    		parent::setEmbedContentInTemplate(false);
    		$this->getCustomerProfile( $request,  $response, $resourceReader);
		}else {
			$response->setContentType("text/html");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), parent::getUnprocessedContent());
		}

	}
	
	public function getCustomerProfile(HttpRequest $request,HttpResponse $response, ResourceReader $resourceReader) {
		$profileId = $request->getParameter("profileId");
		parent::storeSessionAttribute("profileId", $profileId);
		$getCustomerProfileRequest = RequestGetCustomerProfile::factory(array(
				"profileId"=>$profileId
		));
		
		
		$getCustomerProfileResponse = $getCustomerProfileRequest->request();
		$status = $getCustomerProfileResponse->getHttpEvent()->responseBody();
		$cardId = "";
		if($getCustomerProfileResponse->status()->isSuccess()){
			
			if($getCustomerProfileResponse->profileCards()->size()>0){
				$cardId = $getCustomerProfileResponse->profileCards()->offsetGet(0)->cardId();
				parent::storeSessionAttribute("cardId", $cardId);
			}
		}
		$this->setGetCustomerProfileProcessed($getCustomerProfileResponse->status()->isSuccess(), $status, $cardId);
	}
	
	public function setGetCustomerProfileProcessed($isSuccess, $status, $cardId){
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "<br />Code used to call API server:");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:get-customer-profile}");
		if($isSuccess){
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-success\">Request was sucessful, now all response is wrapped in an object</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE,"html"), "{snippet-file:get-customer-profile-response-object-usage}");
			if($cardId!=""){
				parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"cardId"), $cardId);
			}
		}else{
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-warning\">Request contained errors</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:status-in-exception-usage}");
		}
		
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "Raw response received from API server:<br />");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_DISPLAY_AS_CODE|ContentDescriptor::ACTION_URL_ENCODE,"html"), "status-here");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"statusObject"), json_decode($status));
	}
}
