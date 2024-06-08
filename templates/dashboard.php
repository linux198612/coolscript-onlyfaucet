<?php
include("header.php");
?>

<div class="container">
    <div class="row text-center">
        <div class="col-sm-4">
        <img src="https://via.placeholder.com/300x250/404040/FFFFFF?text=300x250" alt="Banner Placeholder">
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
