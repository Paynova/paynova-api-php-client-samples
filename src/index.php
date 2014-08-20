<?php
session_start();
error_reporting(E_ALL ^ E_STRICT);
ini_set('display_errors', 1);


/*
 * If you not are using composer - change this path to point to Paynova.php
 */
$paynovaLibPath = "../vendor/autoload.php";
if(!is_file($paynovaLibPath)){
	die($paynovaLibPath." is not found. Specify at ".__FILE__." line ".(__LINE__-2)." where Paynova API PHP Client library can be found");
}
include($paynovaLibPath);

use Paynova\PaynovaConfig;


include "classes/util/Properties.php";
include "classes/util/ResourceReader.php";
include "classes/util/AjaxHelper.php";
include "classes/Renderer.php";
include "classes/AccessRouter.php";
include "classes/HttpResponse.php";
include "classes/HttpRequest.php";
include "classes/document/DocumentFactory.php";
include "classes/document/Document.php";
include "classes/document/ContentDescriptor.php";
include "classes/document/DocumentImpl.php";
include "classes/document/NotFoundDocument.php";
include "classes/document/IndexDocument.php";
include "classes/document/CreateAndInitDocument.php";
include "classes/document/BackFromPaymentDocument.php";
include "classes/document/CallbackDocument.php";
include "classes/document/FinalizeAuthorizationDocument.php";
include "classes/document/AnnulAuthorizationDocument.php";
include "classes/document/RefundPaymentDocument.php";
include "classes/document/GetCustomerProfileDocument.php";
include "classes/document/RemoveCustomerProfileCardDocument.php";

$settings = new Properties();
$settings->loadByFile("../paynova-samples.properties");
try{
	PaynovaConfig::endpoint(	$settings->getProperty("paynova.api.endpoint")			);//The API SERVER URL
	PaynovaConfig::username(	$settings->getProperty("paynova.api.username")			);//Merchant id at Paynova
	PaynovaConfig::password(	$settings->getProperty("paynova.api.password")			);//Merchant password at paynova
}catch(InvalidArgumentException $exception) {
	die("Make sure to set your Paynova credentials in the paynova-samples.properties file");
}

if(!is_writable($settings->getProperty("paynova.api.callback-store-file"))){
	die($settings->getProperty("paynova.api.callback-store-file")." has to be writable for the webserver to be able to store Event Hook Notifications (EHN)");
}

$resourceReader = new ResourceReader();
$renderer  = new Renderer($resourceReader,Renderer::TEMPLATE_TWITTER_BOOTSTRAP);
$accessRouter = new AccessRouter($renderer, $resourceReader);
$httpResponse = new HttpResponse();	

$httpRequest = new HttpRequest($_POST,$_GET);

if($httpRequest->getGetParameter("ajax")=="util"){
	$ajaxHelper = new AjaxHelper();
	$ajaxHelper->processAjaxRequest($httpRequest, $httpResponse, $resourceReader);
}else {
	$page = "index.html";
	if($httpRequest->getGetParameter("service")!="")$page = $httpRequest->getGetParameter("service").".html";
	else if($httpRequest->getGetParameter("back")!="")$page = "back-from-payment.html";
	else if($httpRequest->getGetParameter("call")=="back")$page = "call.back";
	
	$accessRouter->handleDocument($page, $httpRequest, $httpResponse);
}
echo header("Content-Type: ".$httpResponse->getContentType());
echo ($httpResponse->getContentType()=="application/json")?str_replace("\n","",$httpResponse->get()):$httpResponse->get();