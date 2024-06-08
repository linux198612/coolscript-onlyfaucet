<?php


function alert($type, $content){
	$alert = "<div class='alert alert-".$type."' role='alert'>".$content."</div>";
	return $alert;
}

function toSatoshi($amount){
	$satoshi = $amount * 100000000;
	return $satoshi;
}

function checkDirtyIp($ip, $apiKey){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, "10");
	curl_setopt($ch, CURLOPT_URL, "http://v2.api.iphub.info/ip/".$ip);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Key: '.$apiKey));
	$response=curl_exec($ch);
	curl_close($ch);
	$iphub = json_decode($response);
	if($iphub->block >= 1){
		return true;
	} else {
		return false;
	}
}

function findTimeAgo($past) {
	$secondsPerMinute = 60;
	$secondsPerHour = 3600;
	$secondsPerDay = 86400;
	$secondsPerMonth = 2592000;
	$secondsPerYear = 31104000;

	$past = $past;
	$now = time();

	$timeAgo = "";

	$timeDifference = $now - $past;

	if($timeDifference <= 29) {
		$timeAgo = "less than a minute";
	} else if($timeDifference > 29 && $timeDifference <= 89) {
		$timeAgo = "1 minute";
	} else if($timeDifference > 89 && $timeDifference <= (($secondsPerMinute * 44) + 29)){
		$minutes = floor($timeDifference / $secondsPerMinute);
		$timeAgo = $minutes." minutes";
	} else if($timeDifference > (($secondsPerMinute * 44) + 29) && $timeDifference < (($secondsPerMinute * 89) + 29)){
		$timeAgo = "about 1 hour";
	} else if($timeDifference > (($secondsPerMinute * 89) + 29) && $timeDifference <= (($secondsPerHour * 23) + ($secondsPerMinute * 59) + 29)){
		$hours = floor($timeDifference / $secondsPerHour);
		$timeAgo = $hours." hours";
	} else if($timeDifference > (($secondsPerHour * 23) + ($secondsPerMinute * 59) + 29) && $timeDifference <= (($secondsPerHour * 47) + ($secondsPerMinute * 59) + 29)){
		$timeAgo = "1 day";
	} else if($timeDifference > (($secondsPerHour * 47) + ($secondsPerMinute * 59) + 29) && $timeDifference <= (($secondsPerDay * 29) + ($secondsPerHour * 23) + ($secondsPerMinute * 59) + 29)){
		$days = floor($timeDifference / $secondsPerDay);
		$timeAgo = $days." days";
	} else if($timeDifference > (($secondsPerDay * 29) + ($secondsPerHour * 23) + ($secondsPerMinute * 59) + 29) && $timeDifference <= (($secondsPerDay * 59) + ($secondsPerHour * 23) + ($secondsPerMinute * 59) + 29)){
		$timeAgo = "about 1 month";
	} else if($timeDifference > (($secondsPerDay * 59) + ($secondsPerHour * 23) + ($secondsPerMinute * 59) + 29) && $timeDifference < $secondsPerYear){
		$months = round($timeDifference / $secondsPerMonth);

		if($months == 1) {
			$months = 2;
		}

		$timeAgo = $months." months";
	} else if($timeDifference >= $secondsPerYear && $timeDifference < ($secondsPerYear * 2)){
		$timeAgo = "about 1 year";
	} else {
		$years = floor($timeDifference / $secondsPerYear);
		$timeAgo = "over ".$years." years";
	}

	return $timeAgo." ago";
}


function CaptchaCheck($selectedCaptcha, $captchaData, $mysqli){
		if($selectedCaptcha == 3){
		$hCaptchaPrivKey = $mysqli->query("SELECT * FROM settings WHERE name = 'hcaptcha_sec_key' LIMIT 1")->fetch_assoc()['value'];

		$data = array(
			'secret' => $hCaptchaPrivKey,
			'response' => $captchaData['h-captcha-response']
		);

		$verify = curl_init();
		curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
		curl_setopt($verify, CURLOPT_POST, true);
		curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($verify);

		$responseData = json_decode($response);
		if($responseData->success)
			return true;
		else
			return false;

	}
}
?>