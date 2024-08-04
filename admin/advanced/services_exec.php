<?php
if (isset($_COOKIE['PHPSESSID']))
{
    session_id($_COOKIE['PHPSESSID']); 
}
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION) || (count($_SESSION, COUNT_RECURSIVE) < 10)) {
    session_id('wpsdsession');
    session_start();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$cmdoutput = array();
switch ($action) {
    case "stop":
	$cmdresult = exec('sudo /usr/local/sbin/wpsd-services stop', $cmdoutput, $retvalue);
	break;
    case "fullstop":
	$cmdresult = exec('sudo /usr/local/sbin/wpsd-services fullstop', $cmdoutput, $retvalue);
	break;
    case "restart":
	$cmdresult = exec('sudo /usr/local/sbin/wpsd-services restart', $cmdoutput, $retvalue);
	break;
    case "status":
	$cmdresult = exec('sudo /usr/local/sbin/wpsd-services status', $cmdoutput, $retvalue);
	break;
    case "killmmdvmhost":
	$cmdresult = exec('sudo /usr/bin/killall -q -9 MMDVMHost', $cmdoutput, $retvalue);
	break;
    case "updatehostsfiles":
	$cmdresult = exec('sudo -- /bin/bash -c "env FORCE=1 /usr/local/sbin/wpsd-hostfile-update; /usr/local/sbin/wpsd-services restart;"', $cmdoutput, $retvalue);
	break;
    default:
	$cmdoutput = array('error !');
}
echo "<br />";
foreach ($cmdoutput as $l) {
    echo $l."<br />";
    echo "<br />";
}
if ($retvalue == 0) {
    echo "<p style='font-size:larger;'>** Success **</p>";
}
else {
    echo "<p tyle='font-size:larger; >!! Failure !!</p>";
}
echo "<br />";
?>
