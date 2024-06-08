<?php
include("config.php");

// Dátum 48 órával ezelőtt
$expiry_threshold = time() - (48 * 60 * 60);

// transactions tábla törlése
$sql = "DELETE FROM `transactions` WHERE `timestamp` < $expiry_threshold";
if ($mysqli->query($sql) === TRUE) {
    echo "Records deleted successfully from transactions\n<br>";
} else {
    echo "Error deleting records from transactions: " . $mysqli->error . "\n";
}

// Kapcsolat lezárása
$mysqli->close();
?>
