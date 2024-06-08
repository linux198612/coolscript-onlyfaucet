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
            width: 75%;
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

    <nav class="navbar navbar-expand-lg"> <!-- Add the "navbar-expand-lg" class -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto"> <!-- Center the menu items -->
                <li class="nav-item"><a class="nav-link" href="<?php echo $Website_Url;?>">Home</a></li>
            </ul>
        </div>
    </nav>

    <div class="banner">
        <h2>Claim your free cryptocurrency today!</h2>
    </div>

    <div class="login-form">
        <h3>Login</h3>
        <div><?php echo $alertForm ?></div>
        <form method="post" action="">
                    <input class="form-control" type="text" placeholder="Enter your Zerocoin Address" name="address" value="<?php echo htmlspecialchars($Address); ?>" autofocus>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>"/>
            <button type="submit">Login</button>
        </form>
    </div>




    <div class="container">
    <h3 class="text-center">Statistics</h3>
    <div class="row text-center">
        <?php
        // Dummy data for demonstration
        $userCount = $mysqli->query("SELECT COUNT(id) FROM users")->fetch_row()[0];
        $totalClaims = $mysqli->query("SELECT value FROM settings WHERE name = 'total_claims' LIMIT 1")->fetch_assoc()['value'];
        $totalWithdrawn = $mysqli->query("SELECT value FROM settings WHERE name = 'total_withdraw' LIMIT 1")->fetch_assoc()['value'];

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
</body>
</html>
