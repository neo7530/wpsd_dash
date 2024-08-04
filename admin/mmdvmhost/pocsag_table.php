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
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

// Most of the work here contributed by geeks4hire (Ben Horan)
// Skyper decode by Andy Taylor (MW0MWZ)

include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code

// Get origin of the page loading
$origin = (isset($_GET['origin']) ? $_GET['origin'] : (isset($myOrigin) ? $myOrigin : "unknown"));

// Function to reverse the ROT1 used for Skyper
function un_rot($message) {
    $output = "";
    $messageTextArray = str_split($message);
    
    // ROT -1
    foreach($messageTextArray as $asciiChar) {
	$asciiAsInt = ord($asciiChar);
	$convretedAsciiAsInt = $asciiAsInt -1;
	$convertedAsciiChar = chr($convretedAsciiAsInt);
	$output .= $convertedAsciiChar;
    }
    
    // Return the clear text
    return $output;
}

// Function to handle Skyper Messages
function skyper($message, $pocsagric) {
    $output = "";
    $messageTextArray = str_split($message);
    
    if ($pocsagric == "0002504") {                                      // Skyper OTA TimeSync Messages
	$output = "[Skyper OTA Time] ".$message;
	return $output;
    }
    
    if ($pocsagric == "0004512") {                                      // Skyper Rubric Index
	if (isset($messageTextArray[0])) {                                // This is hard coded to 1 for rubric index
	    unset($messageTextArray[0]);
	}
	if (isset($messageTextArray[1])) {                                // Rubric Number
	    $skyperRubric = ord($messageTextArray[1]) - 31;
	    unset($messageTextArray[1]);
	}
	if (isset($messageTextArray[2])) {                                // Message number, hard coded to 10 for Rubric Index
	    unset($messageTextArray[2]);
	}
	
	if (count($messageTextArray) >= 1) {                              // Check to see if there is a message to decode
	    $output = "[Skyper Index Rubric:$skyperRubric] ".un_rot(implode($messageTextArray));
	}
	else {
	    $output = "[Skyper Index Rubric:$skyperRubric] No Name";
	}
	return $output;
    }
    
    if ($pocsagric == "0004520") {                                      // Skyper Message
	if (isset($messageTextArray[0])) {                                // Rubric Number
	    $skyperRubric = ord($messageTextArray[0]) - 31;
	    unset($messageTextArray[0]);
	}
	if (isset($messageTextArray[1])) {                                // Message number
	    $skyperMsgNr = ord($messageTextArray[1]) - 32;
	    unset($messageTextArray[1]);
	}
	
	if (count($messageTextArray) >= 1) {                              // Check to see if there is a message to decode
	    $output = "[Skyper Rubric:$skyperRubric Msg:$skyperMsgNr] ".un_rot(implode($messageTextArray));
	}
	else {
	    $output = "[Skyper Rubric:$skyperRubric] No Message";
	}
	return $output;
    }
}

//
// Fill table entries with DAPNETGW messages, stops to <MY_RIC> marker if tillMYRIC is true
//
function listDAPNETGWMessages($logLinesDAPNETGateway, $tillMYRIC) {
    foreach($logLinesDAPNETGateway as $dapnetMessageLine) {
	
	if ($tillMYRIC) {
	    // After this, only messages for my RIC are stored
	    if (strcmp($dapnetMessageLine, '<MY_RIC>') == 0) {
		break;
	    }
	}
	
	$dapnetMessageArr = explode(" ", $dapnetMessageLine);
	$dapnetMessageTxtArr = explode('"', $dapnetMessageLine);
	$utc_time = $dapnetMessageArr["0"]." ".substr($dapnetMessageArr["1"],0,-4);
	$utc_tz =  new DateTimeZone('UTC');
	$local_tz = new DateTimeZone(date_default_timezone_get ());
	$dt = new DateTime($utc_time, $utc_tz);
	$dt->setTimeZone($local_tz);
    if (constant("TIME_FORMAT") == "24") {
        $local_time = $dt->format('H:i:s M j');
    } else {
        $local_time = $dt->format('h:i:s A M j');
    }
	$pocsag_timeslot = $dapnetMessageArr["6"];
	$pocsag_ric = str_replace(',', '', $dapnetMessageArr["8"]);
	// Fix incorrectly truncated strings containing double quotes
	unset($dapnetMessageTxtArr[0]);
	if (count($dapnetMessageTxtArr) > 2) {
            unset($dapnetMessageTxtArr[count($dapnetMessageTxtArr)]);
            $pocsag_msg = implode('"', $dapnetMessageTxtArr);
	}
	else {
            $pocsag_msg = $dapnetMessageTxtArr[1];
	}
	
	// Decode Skyper Messages
	if ( ($pocsag_ric == "0004520") || ($pocsag_ric == "0004512") || ($pocsag_ric == "0002504") ) {
            $pocsag_msg = skyper($pocsag_msg, $pocsag_ric);
	}
	
	// Formatting long messages without spaces
	if (strpos($pocsag_msg, ' ') == 0 && strlen($pocsag_msg) >= 45) {
            $pocsag_msg = wordwrap($pocsag_msg, 45, ' ', true);
	}
	// page dah log text sanity
	if (isset($local_time)) { $local_time = htmlspecialchars($local_time, ENT_QUOTES, 'UTF-8'); }
	if (isset($pocsag_timeslot)) { $pocsag_timeslot = htmlspecialchars($pocsag_timeslot, ENT_QUOTES, 'UTF-8'); }
	if (isset($pocsag_ric)) { $pocsag_ric = htmlspecialchars($pocsag_ric, ENT_QUOTES, 'UTF-8'); }
	if (isset($pocsag_msg)) { $pocsag_msg = htmlspecialchars($pocsag_msg, ENT_QUOTES, 'UTF-8'); }

	echo "<tr>\n";
	echo "  <td style=\"width: 140px; vertical-align: top; text-align: left;\">".$local_time."</td>\n";
	echo "  <td style=\"width: 70px; vertical-align: top; text-align: center;\">Slot ".$pocsag_timeslot."</td>\n";
	echo "  <td style=\"width: 90px; vertical-align: top; text-align: center;\">".$pocsag_ric."</td>\n";
	echo "  <td style=\"width: max-content; vertical-align: top; text-align: left; word-wrap: break-word; white-space: normal !important;\">".$pocsag_msg."</td>\n";
	echo "</tr>\n";
    }
}

//
if ((strcmp($origin, "admin") == 0) && isset($_SESSION['DAPNETAPIKeyConfigs'])) {
    $myRIC = getConfigItem("DAPNETAPI", "MY_RIC", $_SESSION['DAPNETAPIKeyConfigs']);
    
    // Display personal messages only if RIC has been defined, and some personal messages are available
    if ($myRIC && (array_search('<MY_RIC>', $logLinesDAPNETGateway) != FALSE)) {
?>
    <div style="margin-top: 20px;">
	<!-- Personal messages-->
	<div>
	    <b class="larger">Personal Messages</b>
	    <div class="table-container">
		<table class="table poc-lh-table">
		    <thead>
			<tr>
			    <th style="width: 140px;" ><a class="tooltip" href="#"><?php echo __( 'Time' );?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
			    <th style="width: max-content;" ><a class="tooltip" href="#"><?php echo __( 'Message' );?><span><b>Message contents</b></span></a></th>
			</tr>
		    </thead>
		    <tbody>
			<?php
			$found = false;
			
			foreach ($logLinesDAPNETGateway as $dapnetMessageLine) {
			    // After this, only messages for my RIC are stored
			    if (!$found && strcmp($dapnetMessageLine, '<MY_RIC>') == 0) {
				$found = true;
				continue;
			    }
			    
			    if ($found) {
				$dapnetMessageArr = explode(" ", $dapnetMessageLine);
				$utc_time = $dapnetMessageArr["0"]." ".substr($dapnetMessageArr["1"],0,-4);
				$utc_tz = new DateTimeZone('UTC');
				$local_tz = new DateTimeZone(date_default_timezone_get ());
				$dt = new DateTime($utc_time, $utc_tz);
				$dt->setTimeZone($local_tz);
				$local_time = $dt->format('H:i:s M jS');
				
				$pos = strpos($dapnetMessageLine, '"');
				$len = strlen($dapnetMessageLine);
				$pocsag_msg = substr($dapnetMessageLine, ($pos - $len) + 1, ($len - $pos) - 2);
				
				// Formatting long messages without spaces
				if (strpos($pocsag_msg, ' ') == 0 && strlen($pocsag_msg) >= 70) {
				    $pocsag_msg = wordwrap($pocsag_msg, 70, ' ', true);
				}
			?>
		                <tr>
				    <td style="width: 140px; vertical-align: top; text-align: left;"><?php echo $local_time; ?></td>
				    <td style="width: max-content; vertical-align: top; text-align: left; word-wrap: break-word; white-space: normal !important;"><?php echo $pocsag_msg; ?></td>
				</tr>
                        <?php
                            } // $found
                        } // foreach
			?>
		    </tbody>
		</table>
	    </div>
	</div>

<?php
    } // $myRIC
} // admin
?>

<div id='pocsag-sec' style="margin-top: -10px;">
        <div class="larger" style="vertical-align: bottom; font-weight: bold; padding-top:14px;text-align:left;"><?php echo __( 'DAPNET Activity' );?></div>
	<!-- Activity -->
	<div class="table-container">
	    <table class="table poc-lh-table">
		<thead>
		    <tr>
			<th style="width: 140px;" ><a class="tooltip" href="#"><?php echo __( 'Time' );?> (<?php echo date('T')?>)<span><b>Time in <?php echo date('T')?> time zone</b></span></a></th>
			<th style="width: 70px;" ><a class="tooltip" href="#"><?php echo __( 'Time Slot' );?><span><b>Message Mode</b></span></a></th>
			<th style="width: 90px;" ><a class="tooltip" href="#"><?php echo __( 'Target' );?><span><b>RIC / CapCode of the receiving Pager</b></span></a></th>
			<th style="width: max-content;" ><a class="tooltip" href="#"><?php echo __( 'Message' );?><span><b>Message contents</b></span></a></th>
		    </tr>
		</thead>
		<tbody>
		    <?php listDAPNETGWMessages($logLinesDAPNETGateway, ((strcmp($origin, "admin") == 0) ? true : false)); ?>
		</tbody>
	    </table>
	</div>
  </div>
</div>
</div>
<br />
