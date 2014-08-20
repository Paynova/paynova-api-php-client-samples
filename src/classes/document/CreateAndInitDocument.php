<?php

use Paynova\request\RequestInitializePayment;
use Paynova\request\RequestCreateOrder;
use Paynova\request\model\InterfaceOptions;

class CreateAndInitDocument extends DocumentImpl{

	function __construct($unprocessedHtml) {
		parent::__construct($unprocessedHtml);
	}

	public function process(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {

		$action = $request->getParameter("action");
		 
		if($action=="initialize-payment"){
			$response->setContentType("application/json");
			parent::setContentType(DocumentImpl::CONTENT_TYPE_JSON);
			parent::setEmbedContentInTemplate(false);
			$this->intializePayment( $request,  $response, $resourceReader);
		}else if($action=="create-order"){
			$response->setContentType("application/json");
			parent::setContentType(DocumentImpl::CONTENT_TYPE_JSON);
			parent::setEmbedContentInTemplate(false);
			$this->createDetailedOrder( $request,  $response, $resourceReader);
		}else {
			$response->setContentType("text/html");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), parent::getUnprocessedContent());
		}
	}

	private function intializePayment(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {
		$orderId = parent::getSessionAttribute("orderId");
		$urls = $this->getUrlsFromPropertyFile();
		
	
		
		$intializePaymentRequest = RequestInitializePayment::factory(array(
				"orderId"=>$orderId,
				"totalAmount"=>100.00,
				"paymentChannelId"=>RequestInitializePayment::PAYMENT_CHANNEL_WEB,
				"interfaceOptions"=>array(
						"interfaceId"=>InterfaceOptions::ID_AERO,
						"displayLineItems"=>true,
						"customerLanguageCode"=>"swe",
						"urlRedirectSuccess"=>$urls["successurl"],
						"urlRedirectCancel"=>$urls["cancelurl"],
						"urlRedirectPending"=>$urls["pendingurl"],
						"urlCallback"=>$urls["callbackurl"]
				),
				"profilePaymentOptions"=>array(
						"profileId" => "a-customer-123"
				),
				"lineItems" => array(
						array(
								"id" => "foo-123",
								"articleNumber" => "foo-123",
								"name" => "Foo line item name",
								"quantity" => 1,
								"unitMeasure" =>"st.",
								"unitAmountExcludingTax" => 80.00,
								"taxPercent" => 25,
								"totalLineTaxAmount" =>20.00,
								"totalLineAmount" => 100.00,
				
						)
				)
		));
		
		
		$initializePaymentResponse = $intializePaymentRequest->request();
		$status = $initializePaymentResponse->getHttpEvent()->responseBody();;
		$sessionId = "";
		$url = "";
		if($initializePaymentResponse->status()->isSuccess()==1) {
			$sessionId =  $initializePaymentResponse->sessionId();
			parent::storeSessionAttribute("sessionId", $initializePaymentResponse->sessionId());
			$url =  $initializePaymentResponse->url();
		}
		$this->setInitializePaymentProcessed($sessionId, $url, $status);
		
	}

	private function setInitializePaymentProcessed($sessionId, $url, $status) {
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<br />Code used to call API server:");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:initialize-payment}");
		if($sessionId!=""){
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-success\">Request was sucessful, now all response is wrapped in an object</p>");
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE,"html"), "{snippet-file:initialize-payment-response-object-usage}");
		}
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "Raw response received from API server:<br />");
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_DISPLAY_AS_CODE|ContentDescriptor::ACTION_URL_ENCODE,"html"), "status-here");

		if($sessionId!=""){
			$iframeHtml = "<h4>4 Lets pay</h4>Let the user pay using the url, in this example an iframe is used but it could also be a blank window/tab/popup. ".
			"<br />";
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), $iframeHtml);
		}else {
			parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class=\"bg-warning\">The request containted errors</p>");
		}

		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"sessionId"), $sessionId);
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"url"), $url);
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"statusObject"), json_decode($status,true));
	}

	private function createDetailedOrder(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader){
		
		$orderNumber = parent::getSessionAttribute("orderNumber");
		
		$status = "";
		$code = 201;
		
		$requestObj = RequestCreateOrder::factory(
				array(
						"orderNumber"=>$orderNumber,
						"currencyCode" =>"SEK",
						"totalAmount" => 100.00,
						"customer"=>array(
								"customerId" => "foo-123",
								"emailAddress" => "foo@foo.com",
								"name" => array(
									"companyName" => "Foo",
									"title" => "Boss",
									"firstName" =>"Foo",
									"lastName" =>"Fii"
								)
						),
						"lineItems" => array(
								array(
										"id" => "foo-123",
										"articleNumber" => "foo-123",
										"name" => "Foo line item name",
										"quantity" => 1,
										"unitMeasure" =>"st.",
										"unitAmountExcludingTax" => 80.00,
										"taxPercent" => 25,
										"totalLineTaxAmount" =>20.00,
										"totalLineAmount" => 100.00,
										
								)
						)
				)
		);
		
		$createOrderResponse = $requestObj->request();
		$status = $createOrderResponse->getHttpEvent()->responseBody();
		$orderId = "";
		if($createOrderResponse->status()->isSuccess()==1) {
			$orderId = $createOrderResponse->orderId();
			parent::storeSessionAttribute("orderId", $createOrderResponse->orderId());
		}
		$this->setCreateOrderProcessed($orderId,$status, $code);

	}

	

	private function setCreateOrderProcessed($orderId, $status, $httpCode) {
		if($httpCode==201) {
			$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "<br />Code used to call API server:");
			$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE ,"html"), "{snippet-file:create-order}");
			if($orderId!=""){
				$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class='bg-success'>Request was sucessful, now all response is wrapped in an object:</p>");
				$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE|ContentDescriptor::ACTION_DISPLAY_AS_CODE,"html"), "{snippet-file:create-order-response-object-usage}");
			}else {
				$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_URL_ENCODE,"html"), "<p class='bg-warning'>The request containted errors</p>");
			}
			$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "Raw response received from API server:<br />");
			$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_DISPLAY_AS_CODE|ContentDescriptor::ACTION_URL_ENCODE,"html"), "status-here");
			$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"orderId"), $orderId);
			$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"statusObject"), json_decode($status,true));
		}else{
			$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"errorMessage"), "API server replies with code ".$httpCode.", try to clear all properties and try again or contact Paynova support");
		}
		$this->putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"httpCode"), "".$httpCode);
	}



	private function  getUrlsFromPropertyFile(){
		$settings = new Properties();
		$settings->loadByFile("../paynova-samples.properties");
		$myUrl = $settings->getProperty("paynova.api.myurl");
		$map = array(
			"successurl"	=>str_replace("{myurl}",$myUrl,$settings->getProperty("paynova.api.successurl")),
			"cancelurl"		=>str_replace("{myurl}",$myUrl,$settings->getProperty("paynova.api.cancelurl")),
			"pendingurl"	=>str_replace("{myurl}",$myUrl,$settings->getProperty("paynova.api.pendingurl")),
			"callbackurl"	=>str_replace("{myurl}",$myUrl,$settings->getProperty("paynova.api.callbackurl"))
		);

		return $map;
	}
}

