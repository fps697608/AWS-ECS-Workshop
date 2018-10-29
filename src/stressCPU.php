<?php
	$isRunning = exec("pgrep stress");
	
	if($isRunning){
		echo "CPU is burning right now.";
	} else {
		echo("Start burning CPU for 5 minutes...<br>");
		passthru("stress -c 1 -t 300");
	}
?>
