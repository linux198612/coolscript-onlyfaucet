<?php
include("header.php");

$currentLevel = $user['level'];
$currentXP = $user['xp'];
$bonusPercentage = $currentLevel * 0.1;
?>

<div class="container">
    <div class="row text-center">
        <div class="col-sm-4">
        <img src="https://via.placeholder.com/300x250/404040/FFFFFF?text=300x250" alt="Banner Placeholder">
		<br>
		<?php
		                        if ($currentLevel >= $maxLevel) {
                            echo "<p>Max Level Reached: " . $maxLevel . "</p>";
                        } else {
                            // Kiszámítjuk a következő szintig hátralévő XP-t és az aktuális szint XP-szükségletét
                            $xpNeededForNextLevel = ($currentLevel + 1) * $xpThreshold;
                            $remainingXP = max(0, $xpNeededForNextLevel - $currentXP);
    
                            // Kiszámítjuk a szint progressz bár százalékát
                            $percentComplete = floor(($remainingXP / $xpThreshold) * 100);
    
                            // Szint progressz bár
                            echo "<h4>Level: " . $currentLevel . "</h4>";
                            echo '<div class="progress" style="height: 20px; position: relative; background-color: #f3f3f3;">';
                            echo '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' . $remainingXP . '" aria-valuemin="0" aria-valuemax="' . $xpThreshold . '" style="width: ' . (100 - $percentComplete) . '%; background-color: #5bc0de;">';
							echo '<span style="position: absolute; width: 100%; text-align: center; color: black; font-weight: bold;">' . (100 - $percentComplete) . '%</span>';
                            echo '</div>';
                            echo '</div>';
							}

							echo "Level bonus: " . $bonusPercentage . "%";
		?>		
        </div>
        <div class="col-sm-8 d-flex justify-content-center align-items-center">
            <?php
            $timer = $mysqli->query("SELECT * FROM settings WHERE id = '5' LIMIT 1")->fetch_assoc()['value'];

            $nextClaim = $user['last_claim'] + ($timer);

            if (time() >= $nextClaim) {
                echo "<a class='btn btn-dark btn-lg text-center custom-btn' href='index.php?page=faucetstep1' role='button'>Start claim</a>";
            } else {
                $timeLeft = floor(($nextClaim - time()));
                echo "<a class='btn btn-dark btn-lg disabled custom-btn' href='index.php?page=dashboard' role='button' aria-disabled='true'><div id='countdown' style='font-size: 24px; margin-top: 10px;'>You can claim again in <span id='timeLeft'>$timeLeft</span> seconds.</div></a><br>";
            }
            ?>
        </div>
    </div>
</div>

<script>
    function startCountdown(seconds) {
        var countdownElement = document.getElementById('timeLeft');
        var countdownInterval = setInterval(function() {
            if (seconds <= 0) {
                clearInterval(countdownInterval);
                location.reload(); // Reload the page when the countdown is over
            } else {
                seconds--;
                countdownElement.textContent = seconds;
            }
        }, 1000);
    }

    // Start the countdown with the initial time left
    var initialTimeLeft = <?php echo $timeLeft; ?>;
    if (initialTimeLeft > 0) {
        startCountdown(initialTimeLeft);
    } else {
        location.reload(); // Reload immediately if the time left is 0 or less
    }
</script>

<?php
include("footer.php");
?>
