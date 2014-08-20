<?php 
use Paynova\request\RequestRemoveCustomerProfile;
use Paynova\request\RequestRemoveCustomerProfileCard;

class RemoveCustomerProfileCardDocument extends DocumentImpl {

	public function __construct($unprocessedContent) {
		parent::__construct($unprocessedContent);
		// TODO Auto-generated constructor stub
	}

	public function process(HttpRequest $request,HttpResponse $response, ResourceReader $resourceReader) {
		
    	$action = $request->getParameter("action");
    	if($action=="remove-customer-profile"){
    		$response->setContentType("application/json");
    		parent::setContentType(DocumentImpl::CONTENT_TYPE_JSON);
    		parent::setEmbedContentInTemplate(false);
    		$this->removeCustomerProfile( $request,  $response, $resourceReader);
		}else if($action=="remove-customer-profile-card"){
    		$response->setContentType("application/json");
    		parent::setContentType(DocumentImpl::CONTENT_TYPE_JSON);
    		parent::setEmbedContentInTemplate(false);
    		$this->removeCustomerProfileCard( $request,  $response, $resourceReader);
		}else {
			$response->setContentType("text/html");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), parent::getUnprocessedContent());
		}

	}
	
	
	public function removeCustomerProfile(HttpRequest $request,HttpResponse $response, ResourceReader $resourceReader) {
		$profileId = $request->getParameter("profileId");
		
		$removeCustomerProfileRequest = RequestRemoveCustomerProfile::factory(array(
				"profileId"=>$profileId
		));
		
		$removeCustomerProfileResponse = $removeCustomerProfileRequest->request();
		
		$status = $removeCustomerProfileResponse->getHttpEvent()->responseBody();
		$isSuccess = $removeCustomerProfileResponse->status()->isSuccess();
		$this->setRemoveCustomerProfileProcessed($isSuccess,$status);
	}
	
	public function setRemoveCustomerProfileProcessed($isSuccess,$status){
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "<br />Code used to call API server:");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:remove-customer-profile}");
		if($isSuccess){
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-success\">Request was sucessful.</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"removeProfile"), "1");
		}else {
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-warning\">The request containted errors</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:status-in-exception-usage}");
		}
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "The raw response from the API server:<br />");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_DISPLAY_AS_CODE|ContentDescriptor::ACTION_URL_ENCODE,"html"), "status-here");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"statusObject"), json_decode($status));
	}
	
	public function removeCustomerProfileCard(HttpRequest $request,HttpResponse $response, ResourceReader $resourceReader) {
		$cardId = $request->getParameter("cardId");
		$profileId =$request->getParameter("profileId");
		
		
		$removeCustomerProfileCardRequest = RequestRemoveCustomerProfileCard::factory(array(
				"profileId"=>$profileId,
				"cardId"=>$cardId
		));
		$removeCustomerProfileCardResponse = $removeCustomerProfileCardRequest->request();
		
		$status = $removeCustomerProfileCardResponse->getHttpEvent()->responseBody();
		$isSuccess = $removeCustomerProfileCardResponse->status()->isSuccess();
		
		$this->setRemoveCustomerProfileCardProcessed($isSuccess,$status);
	}
	
	public function setRemoveCustomerProfileCardProcessed($isSuccess,$status){
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "<br />Code used to call API server:");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:remove-customer-profile-card}");
		if($isSuccess){
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-success\">Request was sucessful.</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"removeCard"), "1");
		}else {
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-warning\">The request containted errors</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:status-in-exception-usage}");
		}
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "The raw response from the API server:<br />");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_DISPLAY_AS_CODE|ContentDescriptor::ACTION_URL_ENCODE,"html"), "status-here");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"statusObject"), json_decode($status));
	}
}
