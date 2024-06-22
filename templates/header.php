<?php
$faucetName = $mysqli->query("SELECT * FROM settings WHERE id = '1'")->fetch_assoc()['value'];


// Get user's IP address
$user_ip = $_SERVER['REMOTE_ADDR'];

// Check if the IP address exists in the white_list table
$whitelist_check_sql = "SELECT COUNT(*) as count FROM white_list WHERE ip_address = ?";
$stmt = $mysqli->prepare($whitelist_check_sql);
$stmt->bind_param('s', $user_ip);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$whitelist_count = $row['count'];

// Get the current timestamp
$current_time = time();
$twenty_four_hours_ago = $current_time - 86400; // 24 hours ago in seconds

// Check if the IP address exists in the users table with activity in the last 24 hours
$ip_check_sql = "SELECT COUNT(*) as count FROM users WHERE ip_address = ? AND last_activity > ?";
$stmt = $mysqli->prepare($ip_check_sql);
$stmt->bind_param('si', $user_ip, $twenty_four_hours_ago);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$user_count = $row['count'];

// If the IP is found in users table more than once and not in white_list table, block the user
if ($user_count > 1 && $whitelist_count == 0) {
    header("Location: index.php?page=blocked");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
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

        .custom-btn {
            width: 500px;
            height: 150px;
            background-color: #004d40; /* Sötétzöld háttérszín */
            color: white; /* Fehér szöveg */
            text-align: center;
            line-height: 150px; /* Függőlegesen középre igazítja a szöveget */
            font-size: 24px; /* Nagyobb betűméret */
            border-radius: 5px; /* Kerekített sarkok */
            text-decoration: none; /* Nincs aláhúzás */
        }
        .custom-btn.disabled {
            background-color: #004d40; /* Sötétebb zöld háttérszín a letiltott gombhoz */
            cursor: not-allowed; /* Mutatja, hogy nem kattintható */
        }

        .custom-button {

        width: 600px;
        height: 200px;
        background-color: #004d40;
        border: none;
        color: white;
        text-align: center;
        text-decoration: none;
        font-size: 24px;
        font-weight: bold;
        line-height: 200px; /* Középre igazítás */
        cursor: pointer;
    }

    .navbar-balance {
            color: white;
            margin-right: auto;
            padding: 0 20px;
        }
    .card-header {
        background-color: #004d40;
        color: white;
    }

    .card-body {
        background-color: #b0d7da;
    }
    
    .container {
        margin-top: 10px;
    }
    </style>
</head>
<body>
    <div class="jumbotron">
        <h1><?php echo $faucetName; ?></h1>
        <p style="color:white;"></p>
        <img src="images/zerlogo.png" class="logo logo-1" alt="ZeroCoin Logo">
        <img src="images/zerlogo.png" class="logo logo-2" alt="ZeroCoin Logo">
        <img src="images/zerlogo.png" class="logo logo-3" alt="ZeroCoin Logo">
        <img src="images/zerlogo.png" class="logo logo-4" alt="ZeroCoin Logo">
        <img src="images/zerlogo.png" class="logo logo-5" alt="ZeroCoin Logo">
    </div>

    <nav class="navbar navbar-expand-lg"> <!-- Add the "navbar-expand-lg" class -->
    <span class="navbar-balance">Balance: <?php echo htmlspecialchars($user['balance']); ?> ZER</span>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto"> <!-- Center the menu items -->
                <li class="nav-item"><a class="nav-link" href="index.php?page=dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=withdraw">Withdraw</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=referral">Referral</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=logout">Logout</a></li>
            </ul>
        </div>
    </nav>