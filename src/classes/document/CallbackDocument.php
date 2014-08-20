<?php 
use Paynova\util\Util;

class CallbackDocument extends DocumentImpl {

	public function __construct($unprocessedContent) {
		parent::__construct($unprocessedContent);
	}

	public function process(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {
		$response->setContentType("text/html");
		parent::setEmbedContentInTemplate(false);
		
		$eventType = $request->getParameter("EVENT_TYPE");
		
		$builder = $this->insertDigestValidationResults($request->getParameters());
		
		foreach($request->getParameters() as $key=>$value){
			if($builder!="") {
				$builder.="<br />";
			}
			$builder.=$key." = ".$value;
		}
		
		$sessionId = $request->getParameter("SESSION_ID");
		
		
		$resourceReader->saveCallback($eventType,$sessionId, $builder, false);
		parent::putProcessedContent(new ContentDescriptor(ContentDescriptor::ACTION_NONE,"html"), "Thanks for serving");
	}
	
	private function insertDigestValidationResults($requestParameters) {
		$properties = new Properties();
		$properties->loadByFile("../paynova-samples.properties");
		$secretKey = $properties->getProperty("paynova.api.secretkey");
		
		$headerDigestValidated = Util::verify_EVENT_HOOK_HEAD_DIGEST($secretKey);
		
		$str = "HEADER-digest-validated = ".$headerDigestValidated."(calculated-in-sample-code)<br />";
		
		$bodyDigestValidated = Util::verify_EVENT_HOOK_BODY_DIGEST($requestParameters, $secretKey);
		
		$str.="BODY-digest-validated = ".$bodyDigestValidated."(calculated-in-sample-code)";
		
		return $str;
	}
}
