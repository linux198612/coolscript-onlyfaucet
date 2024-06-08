<?php
include("includes/config.php");

 error_reporting(E_ALL);
 ini_set('display_errors', 1);
// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
session_start();

$messages = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $mysqli->real_escape_string($_POST['username']);
        $password = $_POST['password'];

        // Ellenőrizd a felhasználónév és jelszó párost
        $checkUserSQL = "SELECT * FROM admin_users WHERE username = '$username' LIMIT 1";
        $result = $mysqli->query($checkUserSQL);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $storedPasswordHash = $row['password_hash'];

            if (password_verify($password, $storedPasswordHash)) {
                // Sikeres belépés
                $_SESSION['admin_username'] = $username;
                // Ha sikeres a bejelentkezés, akkor a lap újratöltése az admin.php-ra
                header("Location: admin.php?page=dashboard");
                exit;
            } else {
                $login_error = "Incorrect username or password.";
            }
        } else {
            $login_error = "Incorrect username or password.";
        }
    }
}

 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Add your custom CSS styles here -->

    <!-- JavaScript könyvtárak betöltése -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<?php
   
if (!isset($_SESSION['admin_username'])) {   

?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Admin Login</div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="username">Admin username:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <?php if(isset($login_error)) { ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $login_error; ?>
                                </div>
                            <?php } ?>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php

} else {
	

    // Az oldal kiválasztása az URL alapján
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<div class="container">
    <!-- Bootstrap 4 navigációs menü -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Admin Panel</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
                    <a class="nav-link" href="admin.php?page=dashboard">Dashboard</a>
                </li>
			    <li class="nav-item <?php echo ($page == 'passch') ? 'active' : ''; ?>">
                    <a class="nav-link" href="admin.php?page=passch">Admin Password</a>
                </li>
                <li class="nav-item <?php echo ($page == 'basesettings') ? 'active' : ''; ?>">
                    <a class="nav-link" href="admin.php?page=basesettings">Base settings</a>
                </li>
				<li class="nav-item <?php echo ($page == 'basesettings') ? 'active' : ''; ?>">
                    <a class="nav-link" href="admin.php?page=faucet">Faucet</a>
                </li>
			
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Users
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="admin.php?page=duplicate_check">Duplicate Check</a>
					<a class="dropdown-item" href="admin.php?page=whitelist">White IP List</a>
                    <a class="dropdown-item" href="admin.php?page=user_list">User List</a>
                </div>
				</li>
				<li class="nav-item <?php echo ($page == 'logout') ? '' : ''; ?>">
                    <a class="nav-link" href="admin.php?page=logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <?php
    // Az oldal tartalmának megjelenítése a kiválasztott oldalnak megfelelően
    switch ($page) {
        case 'dashboard':
            // Dashboard oldal tartalma
            echo "<h1>Dashboard</h1>";
            // Ide jön a Dashboard oldal tartalma
            break;
		case 'basesettings':
            // Users oldal tartalma
            echo "<h1>Base Settings</h1>";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $faucet_name = $mysqli->real_escape_string($_POST['faucet_name']);
  $referral = $mysqli->real_escape_string($_POST['referral_percent']);
  $hcaptcha_pub_key = $mysqli->real_escape_string($_POST['hcaptcha_pub_key']);
  $hcaptcha_sec_key = $mysqli->real_escape_string($_POST['hcaptcha_sec_key']);
  $zerochain_api = $mysqli->real_escape_string($_POST['zerochain_api']);
  $zerochain_privatekey = $mysqli->real_escape_string($_POST['zerochain_privatekey']);
  // Frissítsd a settings táblát a beállításokkal
  $updatefaucet_name = "UPDATE settings SET value = '$faucet_name' WHERE name = 'faucet_name'";
  $updatereferral_percent = "UPDATE settings SET value = '$referral' WHERE name = 'referral_percent'";
  $updatehcaptcha_pub_key = "UPDATE settings SET value = '$hcaptcha_pub_key' WHERE name = 'hcaptcha_pub_key'";
  $updatehcaptcha_sec_key = "UPDATE settings SET value = '$hcaptcha_sec_key' WHERE name = 'hcaptcha_sec_key'";
  $updatezerochain_api = "UPDATE settings SET value = '$zerochain_api' WHERE name = 'zerochain_api'";
  $updatezerochain_privatekey = "UPDATE settings SET value = '$zerochain_privatekey' WHERE name = 'zerochain_privatekey'";
  
  if (
      $mysqli->query($updatefaucet_name) === TRUE &&
      $mysqli->query($updatereferral_percent) === TRUE &&
      $mysqli->query($updatehcaptcha_pub_key) === TRUE &&
      $mysqli->query($updatehcaptcha_sec_key) === TRUE &&
      $mysqli->query($updatezerochain_api) === TRUE &&
      $mysqli->query($updatezerochain_privatekey) === TRUE
  ) {
      // Sikeres frissítés után visszairányítás a dashboard.php oldalra
      echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
  } else {
    //  echo "Error updating settings: " . $mysqli->error;
  }

}
$getfaucet_name = $mysqli->query("SELECT value FROM settings WHERE name = 'faucet_name' LIMIT 1")->fetch_assoc()['value'];
$getreferral_percent = $mysqli->query("SELECT value FROM settings WHERE name = 'referral_percent' LIMIT 1")->fetch_assoc()['value'];
$gethcaptcha_pub_key = $mysqli->query("SELECT value FROM settings WHERE name = 'hcaptcha_pub_key' LIMIT 1")->fetch_assoc()['value'];
$gethcaptcha_sec_key = $mysqli->query("SELECT value FROM settings WHERE name = 'hcaptcha_sec_key' LIMIT 1")->fetch_assoc()['value'];
$getzerochain_api = $mysqli->query("SELECT value FROM settings WHERE name = 'zerochain_api' LIMIT 1")->fetch_assoc()['value'];
$getzerochain_privatekey = $mysqli->query("SELECT value FROM settings WHERE name = 'zerochain_privatekey' LIMIT 1")->fetch_assoc()['value'];
?>


<div class="container mt-5">
    <form method="post" action="?page=basesettings">
        <div class="form-group">
            <label for="faucet_name">Site name:</label>
            <input type="text" class="form-control" id="faucet_name" name="faucet_name" value="<?php echo $getfaucet_name; ?>">
        </div>
        <div class="form-group">
            <label for="referral_percent">Referral reward (%) :</label>
            <input type="text" class="form-control" id="referral_percent" name="referral_percent" value="<?php echo $getreferral_percent; ?>">
        </div>
        <div class="form-group">
            <label for="hcaptcha_pub_key">Hcaptcha public key :</label>
            <input type="text" class="form-control" id="hcaptcha_pub_key" name="hcaptcha_pub_key" value="<?php echo $gethcaptcha_pub_key; ?>">
        </div>
        <div class="form-group">
            <label for="hcaptcha_sec_key">Hcaptcha private key :</label>
            <input type="text" class="form-control" id="hcaptcha_sec_key" name="hcaptcha_sec_key" value="<?php echo $gethcaptcha_sec_key; ?>">
        </div>
        <div class="form-group">
            <label for="zerochain_api">Zerochain API :</label>
            <input type="text" class="form-control" id="zerochain_api" name="zerochain_api" value="<?php echo $getzerochain_api; ?>">
        </div>
        <div class="form-group">
            <label for="zerochain_privatekey">Zerochain private key :</label>
            <input type="text" class="form-control" id="zerochain_privatekey" name="zerochain_privatekey" value="<?php echo $getzerochain_privatekey; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
<?php
            break;
	
case 'whitelist':
    // Hozzáadás a whitelisthez
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_ip_address'])) {
        $addIpAddress = $mysqli->real_escape_string($_POST['add_ip_address']);
        
        // Lekérdezés az IP-cím ellenőrzésére a whitelistben
        $checkWhiteListSQL = "SELECT * FROM white_list WHERE ip_address = '$addIpAddress' LIMIT 1";
        $whiteListResult = $mysqli->query($checkWhiteListSQL);

        if ($whiteListResult->num_rows == 0) {
            $insertWhiteListSQL = "INSERT INTO white_list (ip_address) VALUES ('$addIpAddress')";
            if ($mysqli->query($insertWhiteListSQL) === TRUE) {
                $whiteListMessages[] = "IP address $addIpAddress successfully added to the whitelist.";
            } else {
                $whiteListMessages[] = "Error adding IP address $addIpAddress to the whitelist: " . $mysqli->error;
            }
        } else {
            $whiteListMessages[] = "IP address $addIpAddress is already in the whitelist.";
        }
    }

    // Eltávolítás a whitelistből
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_ip_address'])) {
        $removeIpAddress = $mysqli->real_escape_string($_POST['remove_ip_address']);
        
        $deleteWhiteListSQL = "DELETE FROM white_list WHERE ip_address = '$removeIpAddress' LIMIT 1";
        if ($mysqli->query($deleteWhiteListSQL) === TRUE) {
            $whiteListMessages[] = "IP address $removeIpAddress successfully removed from the whitelist.";
        } else {
            $whiteListMessages[] = "Error removing IP address $removeIpAddress from the whitelist: " . $mysqli->error;
        }
    }
    ?>

    <div class="container mt-5">
        <h1>Manage White List</h1>
        <?php
        if (isset($whiteListMessages)) {
            foreach ($whiteListMessages as $whiteListMessage) {
                echo '<div class="alert alert-success">' . $whiteListMessage . '</div>';
            }
        }

        // Lekérdezés az összes whitelist IP-cím lekérdezéséhez
        $whiteListSQL = "SELECT * FROM white_list";
        $whiteListResult = $mysqli->query($whiteListSQL);

        if ($whiteListResult->num_rows > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>ID</th><th>IP Address</th><th>Action</th></tr></thead><tbody>';
            while($row = $whiteListResult->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['ip_address'] . '</td>';
                echo '<td>';
                echo '<form method="post" action="">';
                echo '<input type="hidden" name="remove_ip_address" value="' . $row['ip_address'] . '">';
                echo '<button type="submit" class="btn btn-danger">Remove</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-info">No IP addresses in the whitelist.</div>';
        }
        ?>

        <h2>Add IP Address to White List</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="add_ip_address">IP Address:</label>
                <input type="text" class="form-control" id="add_ip_address" name="add_ip_address" required>
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
<?php
    break;	
	
        case 'passch':
            // Users oldal tartalma
            echo "<h1>Admin password change</h1>";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Ellenőrizze a jelenlegi jelszót az adatbázisban
    $username = $_SESSION['admin_username'];

    $sql = "SELECT password_hash FROM admin_users WHERE username = '$username' LIMIT 1";
    $result = $mysqli->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $storedPasswordHash = $row['password_hash'];

        // Ellenőrizze a jelenlegi jelszót
        if (password_verify($currentPassword, $storedPasswordHash)) {
            // A jelenlegi jelszó helyes, cserélje le az új jelszóra
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql = "UPDATE admin_users SET password_hash = '$newPasswordHash' WHERE username = '$username'";
            $mysqli->query($updateSql);

            $messages = "<div class='alert alert-success' role='alert'>Password successfully changed!</div>";
        } else {
            echo "Current password is incorrect!";
        }
    } else {
        echo "An error occurred while verifying the password.";
    }
}
?>
    <?php echo $messages; ?>
    <form method="post" action="?page=passch">
        <div class="form-group">
            <label for="current_password">Current password:</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">New password:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Change</button>
    </form>
<?php
            break;
		case 'faucet':
            // Users oldal tartalma
            echo "<h1>Faucet settings</h1>";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $minReward = $mysqli->real_escape_string($_POST['min-reward']);
    $maxReward = $mysqli->real_escape_string($_POST['max-reward']);
    $faucetTimer = $mysqli->real_escape_string($_POST['timer']);
    $minWithdrawalGateway = $mysqli->real_escape_string($_POST['min-withdrawal-gateway']);

    // Frissítsd a settings táblát a beállításokkal
    $updateMinRewardQuery = "UPDATE settings SET value = '$minReward' WHERE id = 6";
    $updateMaxRewardQuery = "UPDATE settings SET value = '$maxReward' WHERE id = 7";
    $updateTimerQuery = "UPDATE settings SET value = '$faucetTimer' WHERE id = 5";
    $updateMinWithdrawalGatewayQuery = "UPDATE settings SET value = '$minWithdrawalGateway' WHERE id = 23";

    if (
        $mysqli->query($updateMinRewardQuery) === TRUE &&
        $mysqli->query($updateMaxRewardQuery) === TRUE &&
        $mysqli->query($updateTimerQuery) === TRUE &&
        $mysqli->query($updateMinWithdrawalGatewayQuery) === TRUE
    ) {
        // Sikeres frissítés után visszairányítás a dashboard.php oldalra
        echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
        
    } else {
        echo "Hiba a beállítások frissítése közben: " . $mysqli->error;
    }
}

// Lekérdezés a min_reward értékének lekérésére
$getMinReward = $mysqli->query("SELECT value FROM settings WHERE name = 'min_reward' LIMIT 1")->fetch_assoc()['value'];

// Lekérdezés a max_reward értékének lekérésére
$getMaxReward = $mysqli->query("SELECT value FROM settings WHERE name = 'max_reward' LIMIT 1")->fetch_assoc()['value'];

// Lekérdezés a max_reward értékének lekérésére
$getTimer = $mysqli->query("SELECT value FROM settings WHERE name = 'timer' LIMIT 1")->fetch_assoc()['value'];

// Lekérdezés a min_withdrawal_gateway értékének lekérésére
$getMinWithdrawalGateway = $mysqli->query("SELECT value FROM settings WHERE name = 'min_withdrawal_gateway' LIMIT 1")->fetch_assoc()['value'];
?>
<div class="container mt-5">
     <form method="post" action="?page=faucet">
        <div class="form-group">
            <label for="min-reward">Min Reward: (Zatoshi)</label>
            <input type="text" class="form-control" id="min-reward" name="min-reward" value="<?php echo $getMinReward; ?>">
        </div>
        <div class="form-group">
            <label for="max-reward">Max Reward: (Zatoshi)</label>
            <input type="text" class="form-control" id="max-reward" name="max-reward" value="<?php echo $getMaxReward; ?>">
        </div>
        <div class="form-group">
            <label for="timer">Faucet Timer(seconds):</label>
            <input type="text" class="form-control" id="timer" name="timer" value="<?php echo $getTimer; ?>">
        </div>
        <div class="form-group">
            <label for="min-withdrawal-gateway">Min Withdrawal (Zatoshi):</label>
            <input type="text" class="form-control" id="min-withdrawal-gateway" name="min-withdrawal-gateway" value="<?php echo $getMinWithdrawalGateway; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
    
<?php
            break;
case 'duplicate_check':
    // Banned address hozzáadása

    ?>

    <div class="container mt-5">
        <h1>Check Duplicate Registrations</h1>
        <?php
        if (isset($banMessages)) {
            foreach ($banMessages as $banMessage) {
                echo '<div class="alert alert-success">' . $banMessage . '</div>';
            }
        }

        $currentTime = time();
        $time24HoursAgo = $currentTime - 86400;

        $duplicateSQL = "SELECT ip_address, COUNT(*) as count FROM users WHERE last_activity >= $time24HoursAgo GROUP BY ip_address HAVING count > 1";
        $duplicateResult = $mysqli->query($duplicateSQL);

        if ($duplicateResult->num_rows > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>IP Address</th><th>Count</th><th>Users</th></tr></thead><tbody>';
            while($row = $duplicateResult->fetch_assoc()) {
                $ipAddress = $row['ip_address'];
                $userSQL = "SELECT id, address FROM users WHERE ip_address = '$ipAddress'";
                $userResult = $mysqli->query($userSQL);
                $users = '';
                $allBanned = true;
                while ($userRow = $userResult->fetch_assoc()) {
                    $userAddress = $userRow['address'];
                    $checkBanSQL = "SELECT * FROM banned_address WHERE address = '$userAddress' LIMIT 1";
                    $banResult = $mysqli->query($checkBanSQL);

                    if ($banResult->num_rows > 0) {
                        $users .= 'ID: ' . $userRow['id'] . ', Address: ' . $userAddress . ' <span style="color: red;">BANNED</span><br>';
                    } else {
                        $users .= 'ID: ' . $userRow['id'] . ', Address: ' . $userAddress . '<br>';
                        $allBanned = false;
                    }
                }
                if (!$allBanned) {
                    echo '<tr>';
                    echo '<td>' . $row['ip_address'] . '</td>';
                    echo '<td>' . $row['count'] . '</td>';
                    echo '<td>' . $users . '</td>';
                    echo '</tr>';
                }
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-info">No duplicate registrations found.</div>';
        }
        ?>
    </div>
<?php
    break;
            
case 'user_list':
    // Pagination settings
    $limit = 25; // Number of entries per page
    $page = isset($_GET['pages']) ? (int)$_GET['pages'] : 1;
    $page = max($page, 1); // Ensure page is at least 1
    $start = ($page - 1) * $limit;

    // Retrieve users with pagination, ordered by ID descending
    $userSQL = "SELECT id, address, ip_address, joined, last_activity
                FROM users
                ORDER BY id DESC
                LIMIT $start, $limit";

    // Execute the query and check for errors
    if ($userResult = $mysqli->query($userSQL)) {
        // Count total users for pagination
        $countSQL = "SELECT COUNT(*) as total FROM users";
        $countResult = $mysqli->query($countSQL);
        $total = $countResult->fetch_assoc()['total'];
        $pages = ceil($total / $limit);

        ?>

        <div class="container mt-5">
            <h1>User List</h1>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Address</th>
                        <th>IP Address</th>
                        <th>Joined</th>
                        <th>Last Activity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($userRow = $userResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $userRow['id']; ?></td>
                            <td><?php echo $userRow['address']; ?></td>
                            <td><?php echo $userRow['ip_address']; ?></td>
                            <td><?php echo date('Y-m-d H:i:s', $userRow['joined']); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', $userRow['last_activity']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                            <a class="page-link" href="?page=user_list<?php if ($i > 1) echo "&pages=$i"; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

    <?php
    } else {
        // Log SQL error and display a user-friendly message
        echo "Error: " . $mysqli->error;
    }
    break;

        case 'logout':
				 // Munkamenet törlése a felhasználó kijelentkeztetésekor
				session_unset();
				session_destroy();

				// Átirányítás a bejelentkezési oldalra vagy más elérhető oldalra
				// header("Location: admin.php");
            break;
        default:
            // Ha az URL nem egyezik meg semmelyik oldal nevével
            echo "<h1>404 - Page not found</h1>";
            break;
    }
	

	
}
 ?>
</div>

</body>
</html>


