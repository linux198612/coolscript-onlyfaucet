<?php
include("header.php");

echo '<div class="text-center"><h1>Faucet -> STEP 3</h1></div>';
echo '<div class="container text-center">';

$minReward = $mysqli->query("SELECT value FROM settings WHERE name = 'min_reward' LIMIT 1")->fetch_assoc()['value'];
$maxReward = $mysqli->query("SELECT value FROM settings WHERE name = 'max_reward' LIMIT 1")->fetch_assoc()['value'];

if($_GET['c'] == "1"){

    if(!is_numeric($_POST['selectedCaptcha']))
        exit;

    $captchaCheckVerify = CaptchaCheck($_POST['selectedCaptcha'], $_POST, $mysqli);

    if(!$captchaCheckVerify){
        echo alert("danger", "Captcha is wrong. <a href='index.php?page=faucet'>Try again</a>.");
    } else {
        $VPNShield = $mysqli->query("SELECT * FROM settings WHERE id = '14' LIMIT 1")->fetch_assoc()['value'];
        $iphubApiKey = $mysqli->query("SELECT * FROM settings WHERE id = '22' LIMIT 1")->fetch_assoc()['value'];
        if(checkDirtyIp($realIpAddressUser, $iphubApiKey) == true AND $VPNShield == "yes"){
            echo alert("danger", "VPN/Proxy/Tor is not allowed on this faucet.<br />Please disable and <a href='index.php?page=faucet'>try again</a>.");
        } else {
            $nextClaim2 = time() - ($timer);
            $IpCheck = $mysqli->query("SELECT COUNT(id) FROM users WHERE ip_address = '$realIpAddressUser' AND last_claim >= '$nextClaim2'")->fetch_row()[0];
            if($IpCheck >= 1){
                echo alert("danger", "Someone else claimed in your network already.");
            } else {
               srand((double)microtime()*1000000);
                $payOut = rand($minReward, $maxReward);

                $level = $user['level'];
                $bonusPercentage = $level * 0.1;
                $bonusPayOut = floor(($bonusPercentage / 100) * $payOut); // Kerekítés nélkül egész szám

                $totalPayOut = $payOut + $bonusPayOut;
                $payOutBTC = $totalPayOut / 100000000;
                $timestamp = time();

                $mysqli->query("INSERT INTO transactions (userid, type, amount, timestamp) VALUES ('{$user['id']}', 'Faucet', '$payOutBTC', '$timestamp')");
                $mysqli->query("UPDATE users Set balance = balance + $payOutBTC, xp = xp + $xpreward, last_claim = '$timestamp' WHERE id = '{$user['id']}'");
                $mysqli->query("UPDATE settings SET value = value + 1 WHERE name = 'total_claims'");

                echo "<div class='alert alert-info'>You've claimed successfully {$payOut} {$faucetCurrencies[$websiteCurrency][1]} + {$bonusPayOut} {$faucetCurrencies[$websiteCurrency][1]} Bonus.</div>";
				echo "<a href='index.php?page=dashboard' class='btn btn-dark btn-lg'>Back Home</a>";
				
                $referralPercent = $mysqli->query("SELECT value FROM settings WHERE name = 'referral_percent' LIMIT 1")->fetch_assoc()['value'];
                $findReferralQuery = $mysqli->query("SELECT referred_by FROM users WHERE id = '{$user['id']}'");

                if ($findReferralQuery) {
                    $referralData = $findReferralQuery->fetch_assoc();
                    if (!empty($referralData['referred_by'])) {
                        $referralUserId = $referralData['referred_by'];
                        $referralPercentDecimal = $referralPercent / 100;
                        $referralCommission = $referralPercentDecimal * $payOutBTC;
                        $run = $mysqli->query("UPDATE users SET balance = balance + $referralCommission, refearn = refearn + $referralCommission WHERE id = '$referralUserId'");
                    }
                }
            }
        }
    }
}
echo "</div>";

include("footer.php");
?>
