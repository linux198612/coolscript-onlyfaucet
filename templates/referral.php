<?php

include("header.php");

   echo '<div class="container">';
   $referralPercent = $mysqli->query("SELECT * FROM settings WHERE name = 'referral_percent' LIMIT 1")->fetch_assoc()['value'];
   
   if ($referralPercent != "0") {
       echo '
               <div class="row">
                   <div class="col-sm-6">
                       <div class="card">
                           <div class="card-body">
                               <p class="card-text">Reflink: <code>' . $Website_Url . '?ref=' . $user['id'] . '</code></p>
                           </div>
                       </div>
                   </div>
                   <div class="col-sm-6">
                       <div class="card">
                           <div class="card-body">
                               <p class="card-text">Share this link with your friends and earn ' . $referralPercent . '% referral commission</p>
                           </div>
                       </div>
                   </div>
               </div>
           ';
   }
$refEarn = $user['refearn'];   
echo "<p>All referral earning: " . $refEarn . " ZER</p>";

$userId = $user['id'];
$query = "SELECT id, address, last_activity FROM users WHERE referred_by = $userId";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Address</th>';
    echo '<th>Last Activity</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $address = $row['address'];
        $visiblePart = substr($address, 0, -5);
        $hiddenPart = str_repeat('*', 5);
        $maskedAddress = $visiblePart . $hiddenPart;
        $lastActivityTimestamp = $row['last_activity'];
        $lastActivity = date("d-m-Y H:i:s", $lastActivityTimestamp);

        
        echo '<tr>';
        echo "<td>{$maskedAddress}</td>";
        echo "<td>{$lastActivity}</td>";
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    // Említettük a hivatkozott felhasználókat, most bezárjuk a kapcsolatot
    $mysqli->close();
} else {
    echo "No referrals found.";
}

echo '</div>';
include("footer.php");
?>