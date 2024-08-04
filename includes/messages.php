<?php

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';         // Version Lib
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code

$UUID = $_SESSION['PiStarRelease']['Pi-Star']['UUID'];
$CALL = $_SESSION['PiStarRelease']['Pi-Star']['Callsign'];

$headers = stream_context_create(Array("http" => Array("method"  => "GET",
                                                       "timeout" => 10,
                                                       "header"  => "User-agent: WPSD-Messages - $CALL $UUID",
                                                       'request_fulluri' => True )));
// buster EOL!!!! YAY!!!!!!! \o/
if ($osName === "buster") {
    $local_msg = '/var/www/dashboard/includes/.wpsd-legacy-msg.html';
    if(file_exists($local_msg)) {
	$result = @file_get_contents($local_msg);
    } else {
	$result = @file_get_contents('https://wpsd-swd.w0chp.net/WPSD-SWD/WPSD_Messages/raw/branch/master/no-mo-busta-yo.html', false, $headers);
    }
    echo $result;
}
?>

