<?php
/*
	Class to send push notifications using Firebase Cloud Messaging
	Esta clase usa para el envio de notificaciones Firebase Cloud Messaging
	
	Example usage
	Ejemplos de uso
	
	-----------------------
	$obj = new FCMPush($apiKey); //the key server is only one
	$obj->setDevices($devices); //devices is array
	$response = $obj->send($title,$body,$data); //data is array
	-----------------------
	
	$apiKey Your FCM api key // Es la clave de servidor que encuentra en la consola de Firebase 
	$devices An array or string of registered device tokens // un array con los token de los dispositivos a los cuales desea mandarle una notificacion
	$message The mesasge you want to push out //mensaje que desea mandar
	@author Federico Penin 
*/
class FCMPush{
	var $url = "https://fcm.googleapis.com/fcm/send";
	var $serverApiKey = "";
	var $devices = array();
	
	function __construct($serverKey){
		$this->serverApiKey = $serverKey;
	}
	
	//here are the devices (array or individual hash)
	function setDevices($deviceHash){
	
		if(is_array($deviceHash)){
			$this->devices = $deviceHash;
		} else {
			$this->devices = array($deviceHash);
		}
	
	}
	
	//the function that send the message
	function send($title, $body, $data = false){

		if(!is_array($this->devices) || count($this->devices) == 0){
			return "No devices set";
		}
		
		if(strlen($this->serverApiKey) < 8){
			return "Server API Key not set";
		}
		
		if(is_array($data)){
			foreach ($data as $key => $value) {
				$valores[$key] = $value;
			}
		}
	
		$fcmMsg = array(
			'title' => $title,
			'body'=>$body
		);
			
		 $fcmFields = array(
			'registration_ids' => $this->devices,
		    'priority' => 'high',
			'notification' => $fcmMsg
		);
		if(is_array($data)){
					foreach ($data as $key => $value) {
						$fcmFields['data'][$key] = $value;
					}
				}
		
		$headers = array(
			'Authorization: key='.$this->serverApiKey,
			'Content-Type: application/json'
		);
		 
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt($ch,CURLOPT_POST, true );
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fcmFields));
		$result = curl_exec($ch);
		curl_close($ch);
					
		
		return $result;
	}
	
}
