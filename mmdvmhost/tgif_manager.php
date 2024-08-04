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

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code

function httpStatusText($code = 0) {
    // List of HTTP status codes.
    $statuslist = array(
	'100' => 'Continue',
	'101' => 'Switching Protocols',
	'200' => 'OK',
	'201' => 'Created',
	'202' => 'Accepted',
	'203' => 'Non-Authoritative Information',
	'204' => 'No Content',
	'205' => 'Reset Content',
	'206' => 'Partial Content',
	'300' => 'Multiple Choices',
	'302' => 'Found',
	'303' => 'See Other',
	'304' => 'Not Modified',
	'305' => 'Use Proxy',
	'400' => 'Bad Request',
	'401' => 'Unauthorized',
	'402' => 'Payment Required',
	'403' => 'Forbidden',
	'404' => 'Not Found',
	'405' => 'Method Not Allowed',
	'406' => 'Not Acceptable',
	'407' => 'Proxy Authentication Required',
	'408' => 'Request Timeout',
	'409' => 'Conflict',
	'410' => 'Gone',
	'411' => 'Length Required',
	'412' => 'Precondition Failed',
	'413' => 'Request Entity Too Large',
	'414' => 'Request-URI Too Long',
	'415' => 'Unsupported Media Type',
	'416' => 'Requested Range Not Satisfiable',
	'417' => 'Expectation Failed',
	'500' => 'Internal Server Error',
	'501' => 'Not Implemented',
	'502' => 'Bad Gateway',
	'503' => 'Service Unavailable',
	'504' => 'Gateway Timeout',
	'505' => 'HTTP Version Not Supported'
    );
    // Caste the status code to a string.
    $code = preg_replace("/[^0-9]/", "", $code);
    $code = (string)$code;
    // Determine if it exists in the array.
    if(array_key_exists($code, $statuslist) ) {
	// Return the status text
	return $statuslist[$code];
    }
    else {
	// If it doesn't exists, degrade by returning the code.
	return $code;
    }
}

// Set some Variable
$dmrID = "";

// Check if DMR is Enabled
$testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);

if ( $testMMDVModeDMR == 1 ) {
    // Get the current DMR Master from the config
    $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
    if ( $dmrMasterHost == '127.0.0.1' ) {
	// DMRGateway, need to check each config
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Id'];
	    }
	}
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 2']['Id'];
	    }
	}
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 3']['Id'];
	    }
	}
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 4']['Id'];
	    }
	}
	if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Address'])) {
	    if (($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Enabled'])) {
		$dmrID = $_SESSION['DMRGatewayConfigs']['DMR Network 5']['Id'];
	    }
	}
    }
    else if ( $dmrMasterHost == 'tgif.network' ) {
	// MMDVMHost Connected directly to TGIF, get the ID form here
	if (getConfigItem("DMR", "Id", $_SESSION['MMDVMHostConfigs'])) {
	    $dmrID = getConfigItem("DMR", "Id", $_SESSION['MMDVMHostConfigs']);
	}
	else {
	    $dmrID = getConfigItem("General", "Id", $_SESSION['MMDVMHostConfigs']);
	}
    }
}

if (empty($dmrID) == false)
{
    // Work out if the data has been posted or not
    if ( !empty($_POST) && isset($_POST["tgifSubmit"]) ) { // Data has been posted for this page
	// Are we a repeater
	if ( getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == "0" ) {
            $targetSlot = "1"; // Force TS2 (1)
	}
	else {
            $targetSlot = preg_replace("/[^0-9]/", "", $_POST["tgifSlot"]);
            $targetSlot--; // real TS = ts - 1
	}
	
	$command = "Link";
	
	// Figure out what has been posted
	if ((isset($_POST["tgifNumber"]) && !empty($_POST["tgifNumber"])) && isset($_POST["tgifSubmit"])) {
	    $targetTG = preg_replace("/[^0-9]/", "", $_POST["tgifNumber"]);
	    if ($targetTG < 1) {
		$targetTG = "4000";
		$command = "Unlink";
	    }
	}
	else {
	    $targetTG = "4000";
	    $command = "Unlink";
	}

	if ($_POST["tgifAction"] == "UNLINK") {
	    $targetTG = "4000";
	    $command = "Unlink";
	}
	// Perform the API request
	$tgifApiUrl = "http://tgif.network:5040/api/sessions/update/".$dmrID."/".$targetSlot."/".$targetTG;

	#$context = stream_context_create($options);
	#$result = file_get_contents($tgifApiUrl, false, $context);

	$context = stream_context_create(array('http'=>array('timeout' => 10, 'header' => 'User-Agent: WPSD Dashboard for '.$dmrID) )); // Add Timout and User Agent to include DMRID
	$result = @file_get_contents($tgifApiUrl, true, $context);

	// Output to the browser
	echo '<div style="text-align:left;font-weight:bold;">TGIF Manager</div>'."\n";
	echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td>";
	//echo "Sending command to TGIF API";
	echo "<p>TGIF API: ".((isset($_POST["tgifNumber"]) && !empty($_POST["tgifNumber"])) ? "Talkgroup ".preg_replace("/[^0-9]/", "", $_POST["tgifNumber"]) : "Current Talkgroup")." ".(($command == "Link") ? "linked on" : "unlinked from")." slot ".($targetSlot + 1)." (command status: ".httpStatusText($result).").";
	echo "<br />Page reloading...</p></td></tr>\n</table>\n";
	echo "<br />\n";
	// Clean up...
	unset($_POST);
	echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},3000);</script>';
    }
    else { // Do this when we are not handling post data
	echo '<div style="text-align:left;font-weight:bold;">TGIF Manager</div>'."\n";
	echo '<form action="'.htmlentities($_SERVER['PHP_SELF']).'?func=tgif_man" method="post">'."\n";
	echo '<table>
    <tr>
      <th><a class=tooltip href="#">Enter Static Talkgroup:<span><b>Enter the Talkgroup number</b></span></a></th>
      <th><a class=tooltip href="#">Timeslot<span><b>Where to link/unlink</b></span></a></th>
      <th><a class=tooltip href="#">Link / Unlink<span><b>Link or unlink</b></span></a></th>
      <th><a class=tooltip href="#">Action<span><b>Take Action</b></span></a></th>
    </tr>
    <tr>
      <td><input type="text" id="tgifNumber" name="tgifNumber" size="10" maxlength="7" oninput="disableOnEmpty(\'tgifNumber\', \'tgifActionLink\'); return false;"/></td>';
    if (getConfigItem("DMR Network", "Slot1", $_SESSION['MMDVMHostConfigs']) == "1") {
        echo '<td><input type="radio" id="ts1" name="tgifSlot" value="1" /><label for="ts1"/>TS1</label> &nbsp;';
    } else {
          echo '<td><input type="radio" id="ts1" name="tgifSlot" value="1" disabled="disabled" access="fasle"/><label for="ts1"/>TS1</label> &nbsp;';
    }
    echo '<input type="radio" id="ts2" name="tgifSlot" value="2" checked="checked" /><label for="ts2"/>TS2</label></td>
      <td><input type="radio" id="tgifActionLink" name="tgifAction" value="LINK"  /><label for="tgifActionLink"/>Link</label> &nbsp;<input type="radio" id="tgifActionUnLink" name="tgifAction" value="UNLINK" checked="checked" /><label for="tgifActionUnLink"/>Un-Link</label></td>
      <td><input type="submit" value="Request Change" name="tgifSubmit" /></td>
    </tr>
    <tr>
      <td colspan="4" style="white-space:normal;padding: 3px;">Your Hotspot/Repeater ID: <a href="https://tgif.network/profile.php?tab=SelfCare" target="_new" title="Click to view your hotspot info on TGIF">'.$dmrID.'</a> &bull; <a href="https://w0chp.radio/tgif-talkgroups/" target="_blank">List of All TGIF Talkgroups (sortable/searchable/downloadable)...</a></td>
    </tr>
    </table></form>'."\n";
    }
}
?>
