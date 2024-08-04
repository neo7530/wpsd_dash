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

include_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code
?>
<br />
<div style="text-align:left;font-weight:bold;"><?php echo __( 'Active Starnet Groups' );?></div>
<table style="table-layout: fixed;" id="starNetGrps">
    <tr>
	<th><a class="tooltip" href="#"><?php echo __( 'Callsign' );?><span><b>Starnet Callsign</b></span></a></th>
	<th><a class="tooltip" href="#"><?php echo __( 'LogOff' );?><span><b>Starnet Logoff Callsign</b></span></a></th>
	<th colspan="3"><a class="tooltip" href="#"><?php echo __( 'Information' );?><span><b>Infotext</b></span></a></th>
	<th><a class="tooltip" href="#"><?php echo __( 'UTOT' );?><span><b>User TimeOut (min)</b>inactivity time after which a user will be disconnected</span></a></th>
	<th><a class="tooltip" href="#"><?php echo __( 'GTOT' );?><span><b>Group TimeOut (min)</b>inactivity time after which the group will be disconnected</span></a></th>
    </tr>
    <?php
    $ci = 0;
    $i = 0;
    $stngrp = array();
    for($i = 1; $i < 6; $i++) {
	$param = "starNetCallsign" . $i;
	exec('echo T:\"'.$_SESSION['ircDDBConfigs'][$param].'\" >> /tmp/trace.txt');
	if(isset($_SESSION['ircDDBConfigs'][$param]) && !empty($_SESSION['ircDDBConfigs'][$param])) {
	    $gname = $_SESSION['ircDDBConfigs'][$param];
	    $stngrp[$gname] = $i;
	    $ci++;
	    if($ci > 1) {
		$ci = 0;
	    }
	    echo '<tr>'."\n";
	    echo '<td align="center">'.str_replace(' ', '&nbsp;', substr($gname, 0, 8)).'</td>'."\n";
	    
	    $param = "starNetLogoff" . $i;
	    if(isset($_SESSION['ircDDBConfigs'][$param])) {
		$output = str_replace(' ', '&nbsp;', substr($_SESSION['ircDDBConfigs'][$param],0,8));
		echo '<td align="center">'.$output.'</td>'."\n";
	    }
	    else {
		echo '<td>&nbsp;</td>'."\n";
	    }
	    
	    $param = "starNetInfo" . $i;
	    if(isset($_SESSION['ircDDBConfigs'][$param])){
		echo '<td colspan="3" align="left">'.$_SESSION['ircDDBConfigs'][$param].'</td>'."\n";
	    }
	    else {
		echo '<td colspan="3">&nbsp;</td>'."\n";
	    }
	    
	    $param = "starNetUserTimeout" . $i;
	    if(isset($_SESSION['ircDDBConfigs'][$param])) {
		echo '<td align="center">'.$_SESSION['ircDDBConfigs'][$param].'</td>'."\n";
	    }
	    else {
		echo '<td>&nbsp;</td>'."\n";
	    }
	    
	    $param = "starNetGroupTimeout" . $i;
	    if(isset($_SESSION['ircDDBConfigs'][$param])){
		echo '<td align="center">'.$_SESSION['ircDDBConfigs'][$param].'</td>'."\n";
	    }
	    else {
		echo '<td>&nbsp;</td>'."\n";
	    }
	    echo '</tr>'."\n";
	}
    }
    ?>
</table><br />

<?php
$groupsx = array();
if ($starLog = fopen($starLogPath,'r')) {
    while($logLine = fgets($starLog)) {
	preg_match_all('/^(.{19}).*(Adding|Removing) (.{8}).*StarNet group (.{8}).*$/',$logLine,$matches);
	$groupz = substr($matches[4][0],0,8);
	$member = substr($matches[3][0],0,8);
	$action = substr($matches[2][0],0,8);
	$date = $matches[1][0];
	$guid = $stngrp[$groupz];
	if ($action == 'Adding') {
	    $groupsx[$guid][$groupz][$member] = $date;
	}
	elseif ($action == 'Removing'){
	    unset($groupsx[$guid][$groupz][$member]);
	}
    }
    fclose($starLog);
}

//Clean the empty arrays from the multidimensional array
$groupsx = array_map('array_filter', $groupsx);

$active = 0;
for ($i = 1;$i < 6; $i++) {
    if (isset($groupsx[$i])) {
	$active = $active + count($groupsx[$i]);
    }
}

if ($active >= 1) {
    
    echo "<b>".__( 'Active Starnet Group Members' )."</b>\n";
    echo "<table style=\"table-layout: fixed;\">\n";
    echo "<tr>\n";
    echo "<th><a class=tooltip href=\"#\">".__( 'Time' )." (".date('T').")<span><b>Time of Login</b></span></a></th>\n";
    echo "<th><a class=tooltip href=\"#\">".__( 'Group' )."<span><b>Starnet Callsign</b></span></a></th>\n";
    echo "<th><a class=tooltip href=\"#\">".__( 'Callsign' )."<span><b>Callsign</b></span></a></th>\n";
    echo "</tr>\n";
    
    $ci = 0;
    $ulist = array();
    $glist = array();
    for($i = 1;$i < 6; $i++) {
	if(isset($groupsx[$i])) {
	    $glist = $groupsx[$i];
	    foreach ($glist as $gcall => $ulist) {
		foreach ($ulist as $ucall => $ulogin) {
		    $ci++;
		    if($ci > 1) {
			$ci = 0;
		    }
		    $ulogin = date("d-M-Y H:i:s", strtotime(substr($ulogin,0,19)));
		    $utc_time = $ulogin;
                    $utc_tz =  new DateTimeZone('UTC');
                    $local_tz = new DateTimeZone(date_default_timezone_get ());
                    $dt = new DateTime($utc_time, $utc_tz);
                    $dt->setTimeZone($local_tz);
                    if (constant("TIME_FORMAT") == "24") {
                        $local_time = date('H:i:s M j');
                    } else {
                        $local_time = date('h:i:s A M j');
                    }
		    $groupz = str_replace(' ', '&nbsp;', substr($gcall,0,8));
		    $ucall = str_replace(' ', '', substr($ucall,0,8));
		    print "<tr>";
		    print "<td align=\"left\">$local_time</td>";
		    print "<td class='mono' align=\"center\">$groupz</td>";
		    print "<td class='mono'  align=\"center\"><a href=\"http://www.qrz.com/db/$ucall\" target=\"_new\" alt=\"Lookup Callsign\">$ucall</a></td>";
		    print "</tr>\n";
		}
	    }
	}
    }
    echo "</table>\n<br />\n";
}

?>
