<?php
	$isRunning = exec("pgrep stress");
	
	if($isRunning){
		echo "CPU is burning right now.";
	} else {
		echo("Start burning CPU for 5 minutes...");
		exec("bash -c 'stress -c 1 -t 300 > /dev/null &' ");
	}
?>
