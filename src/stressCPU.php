<?php
	$isRunning = exec("pgrep stress");
		if($isRunning) {
			echo "CPU is burning right now.";
		} else {
			echo "Start burning CPU for 5 minutes...";
			$return = exec("nohup nice -n 10 stress -c 1 -t 360 > /dev/null &");
		}
?>