<?php
	$isRunning = exec("pgrep stress");
	
	if($isRunning){
		echo "CPU is burning right now.";
	} else {
		echo("Start burning CPU for 5 minutes...");
		exec("stress -c 1 -t 300 > uselessFile");
	}
?>
