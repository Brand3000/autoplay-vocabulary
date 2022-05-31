<?php

require_once("_init.php");

if ($inputData['word']) {
    $text = trim(preg_replace("/[^а-яА-Яa-zA-Z\s,]/iu", "", $inputData['word']));
    $text = str_replace("/", ",", $text);
    if (!file_exists("../audio/".$text.".mp3") || !@filesize("../audio/".$text.".mp3")) {
        $isEnglish = ord($text);
        if (($isEnglish >= 65 && $isEnglish <= 90) || ($isEnglish >= 97 && $isEnglish <= 122)) $language = "en-US"; else $language = "ru-RU";
        $data = array("input" => array("text" => $text), "voice" => array("languageCode" => $language, "ssmlGender" => "MALE"), "audioConfig" => array("audioEncoding" => "MP3"));
        $data_string = json_encode($data);
        $ch = curl_init('https://texttospeech.googleapis.com/v1beta1/text:synthesize');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'X-Goog-Api-Key:'.$googleApiKey, 'Content-Length: '.strlen($data_string)));
        
        $result = curl_exec($ch);
        $result = json_decode($result, 1);
        
        if ($result['audioContent']) {
            file_put_contents("../audio/".$text.".mp3", base64_decode($result['audioContent']));
        }
    }
    
    $res = array();
    $res['audioFile'] = "/audio/".$text.".mp3";
    
    echo(json_encode($res));
}
