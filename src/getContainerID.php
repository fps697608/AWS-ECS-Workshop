<?php
    
    $datetime = date ("Y-m-d-H:i:s" , mktime(date('H')+8, date('i'), date('s'), date('m'), date('d'), date('Y')));
    echo "Time: ".$datetime.'<br><br>';
    
	$uuid = exec("if [ -e /tmp/uuid ]; then cat /tmp/uuid; else echo `cat /proc/self/cgroup | head -n 1 | cut -d/ -f3` > /tmp/uuid && cat /tmp/uuid; fi");
	echo "UUID: ".$uuid.'<br><br>';
   
    $port = $_SERVER['SERVER_PORT'];
    echo "From Port: ".$port.'<br><br>';

?>