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

// Set some Variable
$repeaterid = "";
$slot1tg = "";
$slot2tg = "";
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
	} else {
	    $dmrID = getConfigItem("General", "Id", $_SESSION['MMDVMHostConfigs']);
	}
}

if (empty($dmrID) == false) {
    // Use TGIF API to get information about current TGs
    $jsonContext = stream_context_create(array('http'=>array('timeout' => 2, 'header' => 'User-Agent: Pi-Star Dashboard for '.$dmrID) )); // Add Timout and User Agent to include DMRID
    $json_data = file_get_contents("http://tgif.network:5040/api/sessions", false, $jsonContext);
    $json = json_decode($json_data, false);

    // Work out what session number we are using
    foreach($json as $key => $jsons) {
        foreach($jsons as $key => $value) {
            if ($json->sessions[$key]->repeater_id == $dmrID) { $session_nr = $key; }
            }
         }
    }
    // Pull the information from JSON
    if (isset($session_nr)) {
        $repeaterid = $json->sessions[$session_nr]->repeater_id;
        if ($json->sessions[$session_nr]->tg0 == "4000") { $slot1tg = "None"; } else { $slot1tg = "TG".$json->sessions[$session_nr]->tg0; }
        if ($json->sessions[$session_nr]->tg0 != "4000") {
            $slot1TGname = exec("grep -w ".$json->sessions[$session_nr]->tg0." /usr/local/etc/tgif-talkgroups.txt | cut -d\":\" -f1 | tr -cd \"'[:alnum:]\/ -\""); 
        } else {
	        $slot1TGname = "";
        }
        if ($json->sessions[$session_nr]->tg  == "4000") { $slot2tg = "None"; } else { $slot2tg = "TG".$json->sessions[$session_nr]->tg; }
        if ($json->sessions[$session_nr]->tg != "4000") {
            $slot2TGname = exec("grep -w ".$json->sessions[$session_nr]->tg." /usr/local/etc/tgif-talkgroups.txt | cut -d\":\" -f1 | tr -cd \"'[:alnum:]\/ -\""); 
        } else {
            $slot2TGname = "";
        }
        echo '<b>Active TGIF Connections</b>
        <table>
          <tr>
            <th align="left" style="padding-left: 8px;"><a class=tooltip href="#">Connected to Master:<span><b>Connected Master</b></span></a></th>
            <th align="left" style="padding-left: 8px;"><a class=tooltip href="#">Slot 1 Talkgroup<span><b>TG linked to Slot 1</b></span></a></th>
            <th align="left" style="padding-left: 8px;"><a class=tooltip href="#">Slot 2 Talkgroup<span><b>TG linked to Slot 2</b></span></a></th>
          </tr>'."\n";
	      echo '<tr>'."\n";
          echo '  <td align="left" style="padding: 8px;white-space:normal; word-wrap:break; width:200px;">tgif.network<br /><small>(<a href="http://tgif.network/selfcare.html" target="_new">Your HotSpot/Repeater ID: '.$repeaterid.'</a>)</small></td>';
	      echo '  <td align="left" style="padding: 8px;">'.$slot1tg.'<span style="float:right;">'."$slot1TGname".'</span></td>';
	      echo '  <td align="left" style="padding: 8px;">'.$slot2tg.'<span style="float:right;">'."$slot2TGname".'</span></td>';
	      echo '</tr>'."\n";
          //echo '<tr>'."\n";
          //echo '  <td colspan="3"><b><a href="https://w0chp.radio/tgif-talkgroups/" target="_blank">List of All TGIF Talkgroups (sortable/searchable/downloadable)...</a></b></td>'."\n";
          //echo '</tr>'."\n";
	      echo '</table>'."\n";
    }
}
?>
