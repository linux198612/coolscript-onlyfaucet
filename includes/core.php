<?php
ini_set('session.gc_maxlifetime', 1800);

// Session indítása
session_start();

// Ellenőrizzük a session időtúllépését
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    // A session lejárt, itt kijelentkeztetheted a felhasználót vagy más tevékenységeket végezhetsz
    // Például kijelentkeztetés és/vagy átirányítás
    session_unset();
    session_destroy();
    header("Location: ./");
    exit();
}

// Frissítjük az utolsó tevékenység időpontját
$_SESSION['last_activity'] = time();

include("config.php");
include("session.php");
include("functions.php");


$refer = 0; // Alapértelmezett érték a hivatkozásnak

if (!empty($_GET['ref'])) {
    $refer = $mysqli->real_escape_string($_GET['ref']);
} elseif (!empty($_GET['r'])) {
    $addyRefer = $mysqli->real_escape_string($_GET['r']);
    $checkReferID = $mysqli->query("SELECT id FROM users WHERE address = '$addyRefer'")->fetch_assoc()['id'];
    if ($checkReferID) {
        $refer = $checkReferID;
    }
}

setcookie("refer", $refer, time() + (3600 * 24));

// Cloudflare IP

$reverseProxy = $mysqli->query("SELECT value FROM settings WHERE name = 'reverse_proxy'")->fetch_assoc()['value'];

if($reverseProxy == "yes"){
	// check whether IP is Cloudflare

	$cloudFlareIpList = array("173.245.48.0", "103.21.244.0", "103.22.200.0", "103.31.4.0", "141.101.64.0", "108.162.192.0", "190.93.240.0", "188.114.96.0", "197.234.240.0", "198.41.128.0", "162.158.0.0", "104.16.0.0", "172.64.0.0", "131.0.72.0");

	if(in_array($_SERVER['REMOTE_ADDR'], $cloudFlareIpList)){
		if(filter_var($_SERVER["HTTP_CF_CONNECTING_IP"], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
	        $realIpAddressUser = $_SERVER["HTTP_CF_CONNECTING_IP"];
	    } else {
	        $realIpAddressUser = $_SERVER['REMOTE_ADDR'];
	    }
	} else {
		echo "Warning: We only support Cloudflare as reverse proxy.";
		$realIpAddressUser = $_SERVER['REMOTE_ADDR'];
	}
} else {
	$realIpAddressUser = $_SERVER['REMOTE_ADDR'];
}

// CSRF PROTECTION

if($_SESSION['token'] == ""){
	$_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));
}

// Faucet currencies

$faucetCurrencies = array("Zerocoin" => array("ZERO", "Zatoshi", "zero"));

$websiteCurrency = $mysqli->query("SELECT value FROM settings WHERE name = 'faucet_currency'")->fetch_assoc()['value'];

function formatAmount($amount) {
    return number_format($amount, 8, '.', '');
}

$balance = $user['balance'];




// function updateUserLevelAndXP($userId, $xpThreshold, $maxLevel) {
//     global $mysqli;

//     // Fetch the user's current XP
//     $user = $mysqli->query("SELECT * FROM users WHERE id = '$userId'")->fetch_assoc();
//     $currentXP = $user['xp'];
//     $currentLevel = $user['level'];

//     // Calculate the new level based on the current XP
//     $newLevel = floor($currentXP / $xpThreshold);

//     // Ensure the user doesn't exceed the maximum level
//     if ($newLevel > $maxLevel) {
//         $newLevel = $maxLevel;
//     }

//     // Update the user's level, XP, and lvlreward
//     $mysqli->query("UPDATE users SET level = '$newLevel', xp = '$currentXP' WHERE id = '$userId'");

//     return true; // User's level and XP updated
// }

// $xpThreshold = 100; // XP required for each level up
// $maxLevel = 200; // Maximum level
// updateUserLevelAndXP($user['id'], $xpThreshold, $maxLevel);

// // Például csökkentjük a felhasználó XP-jét az adatbázisból kinyert érték alapján
// $userId = $user['id'];

// // SQL lekérdezés az új XP lekérdezéséhez az adatbázisból
// $newXPQuery = $mysqli->query("SELECT xp FROM users WHERE id = '$userId'");
// $newXPData = $newXPQuery->fetch_assoc();
// $newXP = $newXPData['xp'];

// if ($newXP < $user['xp']) {
//     // Ha az új XP kevesebb, akkor frissítjük a felhasználó szintjét és XP-jét
//     updateUserLevelAndXP($userId, $xpThreshold, $maxLevel);

//     // Most a felhasználó szintje és XP-je a helyes értékeket fogja tartalmazni
// }


// $xpreward = 1;



/*
Site version information:
*/
$version = "1.00";

?>