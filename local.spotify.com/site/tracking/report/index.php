<?php

$response = array();
header('Content-Type: application/json');

define('MODULE','TRACKING');

try {

	if(!isset($_REQUEST["data"]) && !isset($_REQUEST["number"])) {
		throw new Exception("Missing parameters, request will not be processed.");
	}

	include '../config/config.php';

	if(!isset($_REQUEST["number"])) {
		// HTTP Request

		$vehicle = new Vehicle();
		if($vehicle->handleIncomingHTTPRequest($_REQUEST["data"])) {
			$response["result"] = true;
		} else {
			throw new Exception($vehicle->getError());
		}

	} else {
		// SMS Request

		$sms = new Message();

		$number = null;
		if(isset($_REQUEST["number"]))
			$number = $_REQUEST["number"];

		if(isset($_REQUEST["MessageSid"])) {
			$item = array();
			$item["provider"] = "TWILIO";
			$item["body"] = $_REQUEST["Body"];
			$item["date"] = date("Y-m-d H:i:s");
			$item["sender"] = $_REQUEST["From"];
			$item["recipient"] = $_REQUEST["To"];
			$item["remoteID"] = $_REQUEST["MessageSid"];
			$result = $sms->processIncomingText($item,$number);
			if($result) {
				
				// Special XML repsonse for Twilio
				header("Content-type: text/xml");
				$xml = new DOMDocument('1.0', 'utf-8');
				$xml->formatOutput = true;
				$container = $xml->createElement('Response');
				$xml->appendChild($container);
				echo $xml->saveXML();
				exit();

			} else {
				$response["result"] = false;
				$response["message"] = $sms->getError();
			}
		} else if(isset($_REQUEST["ceptel"]) && isset($_REQUEST["mesaj"])) {
			// NETGSM HTTP API Incoming Request
			$item = array();
			$item["provider"] = "NETGSM";
			$item["body"] = $_REQUEST["mesaj"];
			$item["sender"] = $_REQUEST["ceptel"];
			$item["date"] = date("Y-m-d H:i:s");
			$result = $sms->processIncomingText($item,$number);
			if($result) {
				$response["result"] = true;
			} else {
				$response["result"] = false;
				$response["message"] = $sms->getError();
			}
		} else {
			// JETSMS XML API Incoming Request
			$request = file_get_contents('php://input');

			if($sms->isXMLValid($request)) {
				$xml = simplexml_load_string($request);
				if(isset($xml->message)) {
					foreach($xml->message as $message) {
						$message = (array)$message;
						
						//var_dump($message); exit();
						$item = array();
						$item["provider"] = "JETSMS";
						$item["remoteID"] = $message["message-id"];
						$item["sender"] = $message["sender-gsm"];
						$item["recipient"] = $message["gsm-operator"];
						$messageDate = (string)$message["message-date"];
						$item["date"] = DateTime::createFromFormat("dmYHi",substr($messageDate,0,12))->format("Y-m-d H:i:s");
						$item["body"] = $message["message-body"];
						if(!$sms->processIncomingText($item,$number))
							throw new ExceptionLogger("Incoming SMS cannot be processed.");
					}	
					$response["result"] = true;
					// SPECIAL CASE
					print "00"; exit();
				} else {
					$response["result"] = false;
					$response["message"] = "Missing parameters";
				}
				
			} else {
				$response["result"] = false;
				$response["message"] = "Missing parameters";
			}
		}
	}

} catch(Exception $e) {
	$response["result"] = false;
	$response["message"] = $e->getMessage();
}

echo json_encode($response);
exit();

?>