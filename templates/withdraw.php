<?php

include("header.php");


?>
<div class="container">
    <div class="card-deck">

        <div class="card">
                <div class="card-header">
                    <h3>Stats</h3>
                </div>
            <div class="card-body">
                    <?php
						echo "<h3>Balance</h3>";
						$amount = formatAmount($balance);
						$currencyName = "ZER";
						$satoshi = toSatoshi($user['balance']);
						$satoshiName = $faucetCurrencies[$websiteCurrency][1];

						echo "$amount $currencyName ($satoshi $satoshiName)<br /><br />";

						$minWithdraw = $mysqli->query("SELECT value FROM settings WHERE name = 'min_withdrawal_gateway' LIMIT 1")->fetch_assoc()['value'];
						$progress = toSatoshi($user['balance']) / $minWithdraw;
						$progressWithdraw = $progress * 100;

						// Korlátozzuk a progressz értékét 100%-ra, ha az 100% feletti
						if ($progressWithdraw > 100) {
							$progressWithdraw = 100;
						}

							echo '<div class="progress" style="height: 20px; position: relative; background-color: #f3f3f3;">
								<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progressWithdraw.'"
								aria-valuemin="0" aria-valuemax="100" style="width:'.$progressWithdraw.'%; background-color: #5bc0de;">
									<span style="position: absolute; width: 100%; text-align: center; color: black; font-weight: bold;">'.number_format($progressWithdraw, 2).'%</span>
								</div>
							</div>';
						echo '<br>';
							$totalUserWithdrawn = $mysqli->query("SELECT SUM(amount) FROM withdraw_history where userid = '{$user['id']}'")->fetch_row()[0];
						echo "<p><strong>Total withdraw:</strong> $totalUserWithdrawn ZER</p>";
                    ?>
            </div>
        </div>
        <div class="card">
                <div class="card-header">
                    <h3>Withdraw</h3>
                </div>
            <div class="card-body">
                    <?php

                            $userAddress = $user['address'];
                            $balance = $user['balance'];

                            
                            if($_GET['withdr']){
                                if($_GET['withdr'] == "fp"){
                                    if(toSatoshi($user['balance']) < $minWithdraw){
                                        $minWith = $minWithdraw/100000000;
                                        echo alert("warning", "Withdrawal threshold of ".$minWith." {$faucetCurrencies[$websiteCurrency][1]} hasn't been reached yet.");
                                    } else {
                                            $mysqli->query("UPDATE users Set balance = '0' WHERE id = '{$user['id']}'");
                                            
                                            $ZC_API_Key = $mysqli->query("SELECT value FROM settings WHERE name = 'zerochain_api' LIMIT 1")->fetch_assoc()['value']; //get your free API Key from Zerochain (https://zerochain.info/api)
                                            $pk = $mysqli->query("SELECT value FROM settings WHERE name = 'zerochain_privatekey' LIMIT 1")->fetch_assoc()['value'];
                                        
                                            $result = file_get_contents("https://zerochain.info/api/rawtxbuild/".$pk."/".$userAddress."/".$balance."/0/1/".$ZC_API_Key."");

                                        $TxID = "";
                                        if (strpos($result, '"txid":"') !== false) {
                                            $pieces = explode('"txid":"', $result);
                                            $pieces = explode('"', $pieces[1]);
                                            $TxID = $pieces[0];
                                        }

                                        if($TxID != "") {

                                            $mysqli->query("INSERT INTO withdraw_history (userid, address, amount, txid, timestamp) VALUES ('{$user['id']}', '{$user['address']}', '{$user['balance']}', '$TxID', UNIX_TIMESTAMP(NOW()))");
                                                echo "Successful payment: " . $balance . " ZER";
                                                

                                        } else {
                                            echo "Error---";
                                            $mysqli->query("UPDATE users Set balance = '{$user['balance']}' WHERE id = '{$user['id']}'");
                                        }
                                                    }
                                                } 
                                            } else {

                                            if(toSatoshi($user['balance']) < $minWithdraw){
                                                echo '<a href="index.php?page=withdraw&withdr=fp" class="btn btn-primary btn-lg active disabled" role="button" aria-pressed="true">Withdraw</a>';
                                            } else {
                                                echo '<a href="index.php?page=withdraw&withdr=fp" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Withdraw</a>';
                                            }
                                        }
                                        echo "<br /><br />";
                                        echo "Minimum withdraw: ".$minWithdraw." {$faucetCurrencies[$websiteCurrency][1]}";

                    ?>
            </div>
        </div>
    </div>
</div>
<?php
$userId = $user['id'];
$query = "SELECT id, amount, txid, timestamp FROM withdraw_history WHERE userid = $userId ORDER BY timestamp DESC LIMIT 10";

$result = $mysqli->query($query);

if ($result) {
    echo '<h2>Last 10 Payments</h2>';
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Amount</th>';
    echo '<th>Date</th>';
	echo '<th>Transaction ID</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $amount = $row['amount'];
		$txID = $row['txid'];
        $timestamp = date("d-m-Y H:i:s", $row['timestamp']);
        echo '<tr>';
        echo "<td>{$id}</td>";
        echo "<td>{$amount} ZER</td>";
        echo "<td>{$timestamp}</td>";
		echo "<td><a href=\"https://zerochain.info/tx/".$txID."\" target=\"_blank\"><font color=\"#369cf6\">".$txID."</font></a></td>";
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
    
    // Említettük a kifizetéseket, most bezárjuk a kapcsolatot
    $mysqli->close();
} else {
    echo "No payments found.";
}



include("footer.php");
?>