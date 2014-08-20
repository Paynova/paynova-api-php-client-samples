<?php 
use Paynova\util\Util;

class BackFromPaymentDocument extends DocumentImpl {

	public function __construct($unprocessedContent) {
		parent::__construct($unprocessedContent);
	}

	
	public function process(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {
		$response->setContentType("text/html");
		parent::setShowHeader(false);
		parent::setShowFooter(false);
		$html = parent::getUnprocessedContent();
		
		$sessionId = $request->getParameter("SESSION_ID");
		
		$builder = "";
		$jsonBuilder = "";
		foreach($request->getParameters() as $key=>$value){
			
			$builder.="<tr><td>".$key."</td><td>".$value."</td></tr>";
			if($jsonBuilder!="") {
				$jsonBuilder.="<br />";
			}
			$jsonBuilder.=$key." = ".$value;
			$match = array();
			if(preg_match("/PAYMENT_([0-9])_STATUS/",$key,$match) && $value=="Authorized") {
				 
				$resourceReader->saveCallback("payment_STATUS",$sessionId, $value, false);
				//$num  = preg_replace("/PAYMENT_([0-9])_STATUS/","$1",$key);//key.replaceFirst("PAYMENT_([0-9])_STATUS","$1");
				$num = $match[1];
				$resourceReader->saveCallback("payment_AMOUNT",$sessionId, $request->getParameter("PAYMENT_".$num."_AMOUNT"), false);
				$resourceReader->saveCallback("payment_TRANSACTION_ID",$sessionId, $request->getParameter("PAYMENT_".$num."_TRANSACTION_ID"), false);
			}
		}
		
				
		//Save as a callback
		$resourceReader->saveCallback("user_BACK",$sessionId, $jsonBuilder, false);
		
		$html = str_replace("{postvariables-table-rows}",$builder,$html);//$html.replaceFirst("\\{postvariables-table-rows\\}", builder.toString());
		
		$rType = "urlRedirectSuccess";
		if($request->getGetParameter("back")=="cancel")$rType = "urlRedirectCancel";
		else if($request->getGetParameter("back")=="cancel")$rType = "urlRedirectPending";
		
		$html = str_replace("{redirect-type}",$rType,$html);//html = html.replaceAll("\\{redirect-type\\}", rType);
		
		
		//validate DIGEST
		$properties = new Properties();
		$properties->loadByFile("../paynova-samples.properties");
		$secretKey = $properties->getProperty("paynova.api.secretkey");
		
		$digestValidated = ((Util::verify_POST_REDIRECT_DIGEST($request->getParameters(), $secretKey))?"true":"false");
		
		$html = str_replace("{digest-validated}",$digestValidated,$html);//html = html.replaceAll("\\{digest-validated\\}", ""+digestValidated);
		
		
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), $html);

	}
}
