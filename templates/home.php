<?php
$faucetName = $mysqli->query("SELECT * FROM settings WHERE id = '1'")->fetch_assoc()['value'];

$Address = '';
$alertForm = '';

if(isset($_POST['address'])){
    if(filter_var($_POST['address'], FILTER_VALIDATE_EMAIL)) {
        $alertForm = alert("danger", "Email addresses are not allowed.");
    } else {
        if(!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
            unset($_SESSION['token']);
            $_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));
            exit;
        }
        unset($_SESSION['token']);
        $_SESSION['token'] = md5(md5(uniqid().uniqid().mt_rand()));

        if($_POST['address']){
            $Address = $mysqli->real_escape_string(trim($_POST['address']));
            $addressCheck = (strlen($Address) >= 25 && strlen($Address) <= 80);
            if(!$addressCheck){
                $alertForm = alert("danger", "The Zero address doesn't look valid.");
            } else {
                // Check if the address starts with 't1'
                if(substr($Address, 0, 2) !== 't1') {
                    $alertForm = alert("danger", "Error. Only zerocoin addresses are allowed. Don't have a zerocoin address? <a href='https://zerochain.info/' target='_blank'><font color='blue' size='3'> Create New Zero Wallet </font></a> .");
                } else {
                    if($_COOKIE['refer']){
                        if(is_numeric($_COOKIE['refer'])){
                            $referID2 = $mysqli->real_escape_string($_COOKIE['refer']);
                            $AddressCheck = $mysqli->query("SELECT COUNT(id) FROM users WHERE id = '$referID2'")->fetch_row()[0];
                            if($AddressCheck == 1){
                                $referID = $referID2;
                            } else {
                                $referID = 0;
                            }
                        } else {
                            $referID = 0;
                        }
                    } else {
                        $referID = 0;
                    }

                    $AddressCheck = $mysqli->query("SELECT COUNT(id) FROM users WHERE LOWER(address) = '".strtolower($Address)."' LIMIT 1")->fetch_row()[0];
                    $timestamp = $mysqli->real_escape_string(time());
                    $ip = $mysqli->real_escape_string($realIpAddressUser);

                    if($AddressCheck == 1){
                        $userID = $mysqli->query("SELECT id FROM users WHERE LOWER(address) = '".strtolower($Address)."' LIMIT 1")->fetch_assoc()['id'];
                        $_SESSION['address'] = $userID;
                        $mysqli->query("UPDATE users Set last_activity = '$timestamp', ip_address = '$ip' WHERE id = '$userID'");
                        header("Location: index.php?page=dashboard");
                    } else {
                        $mysqli->query("INSERT INTO users (address, ip_address, balance, joined, last_activity, referred_by, last_claim) VALUES ('$Address', '$ip', '0', '$timestamp', '$timestamp', '$referID', '0')");
                        $_SESSION['address'] = $mysqli->insert_id;
                    }
                    header("Location: index.php?page=dashboard");
                    exit;
                }
            }
        } else {
            $alertForm = alert("danger", "The Zero address field can't be blank.");
        }
    }
}

$bonusValue = $mysqli->query("SELECT value FROM settings WHERE name = 'bonuslevelvalue' LIMIT 1")->fetch_assoc()['value'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $faucetName; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e0f7fa;
            font-family: Arial, sans-serif;
            color: #004d40;
            margin: 0;
            padding: 0;
        }
        .jumbotron {
            background: linear-gradient(135deg, #00796b, #004d40);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
            border-radius: 0; /* Remove border radius */
        }
        .jumbotron h1 {
            font-size: 48px;
            margin: 0;
        }
        .jumbotron .logo {
            position: absolute;
            width: 100px;
            height: 100px;
            opacity: 0.1;
        }
        .logo-1 { top: 10%; left: 10%; }
        .logo-2 { top: 30%; left: 50%; }
        .logo-3 { top: 50%; left: 80%; }
        .logo-4 { top: 70%; left: 20%; }
        .logo-5 { top: 90%; left: 65%; }
        
        .navbar {
            background-color: #004d40;
            padding: 15px;
            text-align: center;
            margin-bottom: 0; /* Remove space between navbar and content */
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
            display: inline-block;
        }
        .banner {
            background-color: #26a69a;
            padding: 40px 20px;
            text-align: center;
            margin: 0 0;
        }
        .login-form {
            background-color: #004d40;
            color: white;
            padding: 30px;
            margin: 20px auto;
            width: 50%;
            border-radius: 10px;
            text-align: center;
        }
        .login-form input[type="text"], .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }
        .login-form button {
            background-color: #26a69a;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .statistics, .transactions, .footer {
            background-color: #b2dfdb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            text-align: center;
        }
        .footer {
            background-color: #004d40;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #004d40;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
		
        .promo-section {
            background-color: #004d40;
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 10px;
        }
        .promo-section h2 {
            margin-bottom: 20px;
        }
        .promo-section .card {
            margin-bottom: 20px;
        }
        .promo-section .btn {
            background-color: #26a69a;
            border: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="jumbotron">
        <h1><?php echo $faucetName; ?></h1>
        <img src="images/zerlogo.png" class="logo logo-1" alt="ZeroCoin Logo">
        <img src="images/zerlogo.png" class="logo logo-2" alt="ZeroCoin Logo">
        <img src="images/zerlogo.png" class="logo logo-3" alt="ZeroCoin Logo">
        <img src="images/zerlogo.png" class="logo logo-4" alt="ZeroCoin Logo">
        <img src="images/zerlogo.png" class="logo logo-5" alt="ZeroCoin Logo">
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="<?php echo $Website_Url;?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-toggle="modal" data-target="#loginModal">Login</a></li>
            </ul>
        </div>
    </nav>

    <div class="banner">
        <h2>Claim your free cryptocurrency today!</h2>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div><?php echo $alertForm ?></div>
                    <form method="post" action="">
                        <input class="form-control" type="text" placeholder="Enter your Zerocoin Address" name="address" value="<?php echo htmlspecialchars($Address); ?>" autofocus>
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
                        <button type="submit" class="btn btn-primary mt-3">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container promo-section">
        <h2>Why Use Our Site?</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h4 class="card-title">Maximize Your Earnings</h4>
                        <p class="card-text">The more you collect, the more you earn. With each level you climb, your faucet rewards increase. Start now and watch your rewards grow!</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h4 class="card-title">Level Up for Bigger Rewards</h4>
                        <p class="card-text">Each level unlocks a higher bonus. At level 0, you start with no bonus. Gain an additional <?php echo $bonusValue; ?>% bonus with each level up. Keep leveling up!</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h4 class="card-title">Join Our Community</h4>
                        <p class="card-text">Be a part of our growing community. Share tips, earn referrals, and enjoy the benefits of being a valued member. Earn more by inviting friends!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
    <h3 class="text-center">Statistics</h3>
    <div class="row text-center">
        <?php
        // Dummy data for demonstration
        $userCount = $mysqli->query("SELECT COUNT(id) FROM users")->fetch_row()[0];
        $totalClaims = $mysqli->query("SELECT value FROM settings WHERE name = 'total_claims' LIMIT 1")->fetch_assoc()['value'];
        $totalWithdrawn = $mysqli->query("SELECT SUM(amount) FROM withdraw_history")->fetch_row()[0];
		
        echo '<div class="col-md-4">';
        echo "<p><strong>Total registered users:<br></strong> $userCount</p>";
        echo '</div>';
        
        echo '<div class="col-md-4">';
        echo "<p><strong>Total faucet claims:<br></strong> $totalClaims</p>";
        echo '</div>';

        echo '<div class="col-md-4">';
        echo "<p><strong>Total withdrawals:<br></strong> $totalWithdrawn ZER</p>";
        echo '</div>';
        ?>
    </div>
</div>

    <div class="transactions">
        <h3>Latest 10 Transactions</h3>
        <?php
        // Dummy data for demonstration
        $result = $mysqli->query("SELECT * FROM withdraw_history ORDER BY id DESC LIMIT 10");
        if($result->num_rows == 0) {
            echo alert("danger", "There are no transactions yet.");
        } else {
            echo '<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Address</th>
                            <th>Amount</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>';
                    while ($row = $result->fetch_assoc()) {
                        $userAddress = $row['address'];
                        $visiblePart = substr($userAddress, 0, -10);
                        $hiddenPart = str_repeat('*', 10);
                        $maskedAddress = $visiblePart . $hiddenPart;
                        $timeAgo = findTimeAgo($row['timestamp']);
                        $row['amount'] .= ' ZER';
                    echo '<tr>
                        <td>' . $row['id'] . '</td>
                        <td>' . $maskedAddress . '</td>
                        <td>' . $row['amount'] . '</td>
                        <td>' . $timeAgo . '</td>
                    </tr>';
            }
            echo '  </tbody>
                  </table>';
        }
        ?>
    </div>

    <div class="footer">
    <p>&copy; <?php echo date('Y'); ?> <a href="./"><?php echo $faucetName; ?></a>. All Rights Reserved. Version: <?php echo $version; ?><br> Powered by <a href="https://coolscript.hu">CoolScript</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
</body>
</html>
