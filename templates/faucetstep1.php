<?php
include("header.php");

?>
<div class="text-center"><h1>Faucet -> STEP 1</h1></div>

<div class="container">
<div class="text-center">
<!-- Advertise here  -->
</div><br>

<div class="row">
    <div class="col-md-4 text-center">
<!-- Advertise here  -->
    </div>
    <div class="col-md-8 text-center">
        <?php




	
	$claimStatus = $mysqli->query("SELECT value FROM settings WHERE name = 'claim_enabled' LIMIT 1")->fetch_assoc()['value'];

	if($claimStatus == "yes"){

	$timer = $mysqli->query("SELECT * FROM settings WHERE id = '5' LIMIT 1")->fetch_assoc()['value'];


	$nextClaim = $user['last_claim'] + ($timer);

	if(time() >= $nextClaim){

	if($_GET['c'] != "1"){
		echo "
		
                    <form method='post' action='index.php?page=faucetstep2'>
                      <input type='hidden' name='verifykey' value='" . $user['claim_cryptokey'] . "'/>
                      <input type='hidden' name='token' value='" . $_SESSION['token'] . "'/>
                      <button type='submit' class='btn btn-success btn-lg'><span class='glyphicon glyphicon-menu-right' aria-hidden='true'></span> Next</button>
                    </form>
                  <br>
		";
	} 

	} 

	} else {
		echo alert("warning", "Faucet is disabled.");
	}


?>
</div>
</div>


<div class="text-center">
<!-- Advertise here  -->
</div>
</div>
<script type="text/javascript">
$(document).ready(function () {
	$(".refresh_link").click(function () {
		location.reload();
	});
});
</script>

<?php


include("footer.php");
?>
