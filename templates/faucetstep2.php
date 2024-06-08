<?php

include("header.php");



echo '<div class="text-center"><h1>Faucet -> STEP 2</h1></div>';
echo '<div class="container text-center">';
?>

<div class="text-center">
<!-- Advertise here  -->
</div><br>

<div class="row">
    <div class="col-md-3 text-center">
<!-- Advertise here  -->
    </div>
    <div class="col-md-6 text-center">
        <?php
	$timer = $mysqli->query("SELECT * FROM settings WHERE id = '5' LIMIT 1")->fetch_assoc()['value'];
	$nextClaim = $user['last_claim'] + ($timer);
	if(time() < $nextClaim){
		header("Location: index.php?page=dashboard");
		exit;
	}
	if(!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
	unset($_SESSION['token']);
	$_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));
	header("Location: index.php?page=dashboard");
	exit;
	}
	unset($_SESSION['token']);
	$_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));

	if(isset($_POST['verifykey'])){

			$hCaptchaPubKey = $mysqli->query("SELECT * FROM settings WHERE name = 'hcaptcha_pub_key'")->fetch_assoc()['value'];

			if($hCaptchaPubKey){
				$linksCaptcha .= "<a href='#' onCLick='showCaptcha(3)'>hCaptcha</a>";
				$captchaContentBox .= "<div id='hcaptcha-box'><center><script src='https://www.hCaptcha.com/1/api.js?recaptchacompat=off' async defer></script>
				<div class=\"h-captcha\" data-sitekey=\"{$hCaptchaPubKey}\"></div></center></div>";
			}

			$captchaContent .= "<strong>".$linksCaptcha."</strong><br /><br />
			".$captchaContentBox."
			<input type='hidden' id='selectedCaptcha__' name='selectedCaptcha' value='3' /><br />
			<script>
			if(document.getElementById('hcaptcha-box')){
				showCaptcha(3);
			} 
			}
			function showCaptcha(captcha){
				hideCaptchaBoxes();
				if(captcha == 3){
					document.getElementById('hcaptcha-box').style.display = 'block';
					document.getElementById('selectedCaptcha__').value = '3';
				}
			}
			function hideCaptchaBoxes(){
				if(document.getElementById('hcaptcha-box')){
					document.getElementById('hcaptcha-box').style.display = 'none';
				}
			}
			</script>";

			
			echo "<form method='post' action='index.php?page=faucetstep3&c=1'>
			<div class='form-group'>
				".$captchaContent."
			</div><br>
			<input type='hidden' name='verifykey' value='".$user['claim_cryptokey']."'/>
			<input type='hidden' name='token' value='".$_SESSION['token']."'/>
			<button type='submit' class='btn btn-success'>Claim</button>
			</form><br>
			";
		
	} else {
		echo alert("danger", "Abusing the system is not allowed. <a href='index.php?page=dashboard'>Go back</a>");
	}

?>
</div><br><br>
<div class="col-md-3  text-center">
<!-- Advertise here  -->
</div>
</div>
</div>

<div class="text-center">
<!-- Advertise here  -->
</div>

<?php

include("footer.php");
?>
   

	
