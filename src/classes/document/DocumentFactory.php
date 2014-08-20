<?
class DocumentFactory {

	private function __construct() {
		// TODO Auto-generated constructor stub
	}
	public static function createDocument($htmlFile, HttpRequest $httpRequest, HttpResponse $httpResponse, ResourceReader $resourceReader) {
		$document = null;
		$html = $resourceReader->getContentsOfFile("/public/documents/".preg_replace("/\.(.*)$/","",$htmlFile).".html");
		if($html != null) {
			if ($htmlFile=="index.html") {
				$document = new IndexDocument($html);
			}else if ($htmlFile=="create-and-init.html") {
				$document = new CreateAndInitDocument($html);
			}else if ($htmlFile=="finalize-authorization.html") {
				$document = new FinalizeAuthorizationDocument($html);
			}else if ($htmlFile=="refund-payment.html") {
				$document = new RefundPaymentDocument($html);
			}else if ($htmlFile=="annul-authorization.html") {
				$document = new AnnulAuthorizationDocument($html);
			}else if ($htmlFile=="get-customer-profile.html") {
				$document = new GetCustomerProfileDocument($html);
			}else if ($htmlFile=="remove-customer-profile-card.html") {
				$document = new RemoveCustomerProfileCardDocument($html);
			}else if (preg_match("/^back-from-payment/",$htmlFile)) {
				$document = new BackFromPaymentDocument($html);
			}
		}else if ($htmlFile=="call.back") {
			$document = new CallbackDocument("");
		}
		
		if($document == null) {
			$document = new NotFoundDocument($resourceReader->getContentsOfFile("/public/documents/404.html"));
		}
		$document->process($httpRequest, $httpResponse, $resourceReader);
		return $document;
	}
}
