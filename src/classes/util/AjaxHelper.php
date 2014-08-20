<?

class AjaxHelper {

	function __construct() {
		// TODO Auto-generated constructor stub
	}
	
	 public static function processAjaxRequest(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader){
		$action = $request->getParameter("action");
		$reply = array();
		if($action=="random-order-number") {
			$reply = self::actionRandomOrderNumber($response);
		}else if($action=="get-callback-info") {
			$reply = self::actionGetCallBackInfo($request, $response, $resourceReader);
		}else if($action=="get-payment-success-properties") {
			$reply = self::actionGetPaymentSuccessProperties($request, $response, $resourceReader);
		}else if($action=="clear-variables") {
			session_unset();
		}
		$response->setContentType("application/json");
		$response->append(json_encode($reply));
	 }
	 
	

	private static function actionRandomOrderNumber(HttpResponse $response) {

		$orderNumber = "merchant-order-nr-".rand(10000,99999);
		
		$response->setSessionVariable("orderNumber", $orderNumber);
		 return array("orderNumber"=>$orderNumber);
	 }
	 
	 private static function actionGetCallBackInfo(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader){
		$sessionId = $request->getParameter("sessionId");
		$types = explode(",",$request->getParameter("types"));
		$reply = array("html"=>array(),"json"=>array());
		
		for($i=0;$i<count($types);$i++) {
			$callback = $resourceReader->getSavedCallback($types[$i], $sessionId);
			
			if($callback != null && $callback!=""){
				$reply["html"][$types[$i]] = urlencode($callback);
				
				$jsonIfied = str_replace(" = ", "\":\"", urlencode($callback));
				$reply["json"][$types[$i]] = $jsonIfied;
				
				
			}
		}
		
		return $reply;
	 }
	 
	 private static function actionGetPaymentSuccessProperties(HttpRequest $request, HttpResponse $response, ResourceReader $resourceReader) {
	 
		 $sessionId = $request->getParameter("sessionId");
		 $tryNum = $request->getParameter("tryNum");
		 $payment_STATUS = $resourceReader->getSavedCallback("payment_STATUS", $sessionId);
		 $payment_AMOUNT = $resourceReader->getSavedCallback("payment_AMOUNT", $sessionId);
		 $payment_TRANSACTION_ID = $resourceReader->getSavedCallback("payment_TRANSACTION_ID", $sessionId);
		
		 $reply = array("tryNum"=>$tryNum);
		 if($payment_STATUS!=null && $payment_STATUS!="") {
			 //Save them as session vars
			
			 $response->setSessionVariable("amount", 		$payment_AMOUNT);
			 $response->setSessionVariable("transactionId", $payment_TRANSACTION_ID);
			 $reply["status"] = urlencode($payment_STATUS);
			 $reply["amount"] = urlencode($payment_AMOUNT);
			 $reply["transactionId"] = urlencode($payment_TRANSACTION_ID);
			
		 }else{
			//Returned them to client
			 if($tryNum=="1"){
			 	$reply["message"] = "The Event Hook Notifications has not been received yet, lets check once more";
				
			 }else{
			 	$reply["message"] = "Something went wrong, reset and clear again";
			 	
			 }
		 }
		 return $reply;
	 }
}
