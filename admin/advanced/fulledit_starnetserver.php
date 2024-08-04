<?php


if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();

    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

$editorname = 'StarNet Server';
$configfile = '/etc/starnetserver';
$tempfile = '/tmp/c3Rhcm5ldHNlcnZlcg.tmp';
$servicenames = array('mmdvmhost.service', 'starnetserver.service');

require_once('fulledit_template.php');

?>
