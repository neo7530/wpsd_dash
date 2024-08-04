<?php

include('wifi/phpincs.php');

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="pragma" content="no-cache" />
<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
<meta http-equiv="Expires" content="0" />';
include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php';
echo '<link rel="stylesheet" type="text/css" href="/admin/wifi/styles.php?version='.$versionCmd.'" />
<script type="text/Javascript" src="/admin/wifi/functions.js?version='.$versionCmd.'"></script>
<script type="text/javascript" src="/js/jquery.min.js?version='.$versionCmd.'"></script>
<script type="text/javascript" src="/js/functions.js?version='.$versionCmd.'"></script>
<script type="text/javascript">
  $.ajaxSetup({ cache: false });
</script>
<title>WPSD Digital Voice Dashboard - WiFi Connection Manager</title>
<style>
        .form-fields {
            display: none;
        }
        body {
            padding:0;
            margin: 0;
            border: none;
        }
        th, td {
            text-align: left;
        }
        .conn_name, label {
            font-weight: bold;
        }
</style>
</head>
<body>'."\n";

//Declare a pile of variables
$strIPAddress = NULL;
$strNetMask = NULL;
$strRxPackets = NULL;
$strRxBytes = NULL;
$strTxPackets = NULL;
$strTxBytes = NULL;
$strSSID = NULL;
$strBSSID = NULL;
$strBitrate = NULL;
$strTxPower = NULL;
$strLinkQuality = NULL;
$strSignalLevel = NULL;
$strWifiFreq = NULL;
$strWifiChan = NULL;

exec('ifconfig wlan0',$return);
exec('iwconfig wlan0',$return);
exec('iw dev wlan0 link',$return);
$strWlan0 = implode(" ",$return);
$strWlan0 = preg_replace('/\s\s+/', ' ', $strWlan0);
if (strpos($strWlan0,'HWaddr') !== false) {
        preg_match('/HWaddr ([0-9a-f:]+)/i',$strWlan0,$result);
        $strHWAddress = $result[1];
}
if (strpos($strWlan0,'ether') !== false) {
        preg_match('/ether ([0-9a-f:]+)/i',$strWlan0,$result);
        $strHWAddress = $result[1];
}
if(strpos($strWlan0, "UP") !== false && strpos($strWlan0, "RUNNING") !== false) {
    $strStatus = '<span style="color:#6f0;background:black;">Interface is active</span>';
    //Cant get these unless we are connected :)
    if (strpos($strWlan0,'inet addr:') !== false) {
        preg_match('/inet addr:([0-9.]+)/i',$strWlan0,$result);
        $strIPAddress = $result[1];
    } else {
        preg_match('/inet ([0-9.]+)/i',$strWlan0,$result);
        $strIPAddress = $result[1];
   }
   if (strpos($strWlan0,'Mask:') !== false) {
        preg_match('/Mask:([0-9.]+)/i',$strWlan0,$result);
        $strNetMask = $result[1];
   } else {
        preg_match('/netmask ([0-9.]+)/i',$strWlan0,$result);
        $strNetMask = $result[1];
   }
   preg_match('/RX packets.(\d+)/',$strWlan0,$result);
   $strRxPackets = $result[1];
   preg_match('/TX packets.(\d+)/',$strWlan0,$result);
   $strTxPackets = $result[1];
   if (strpos($strWlan0,'RX bytes') !== false) {
        preg_match('/RX [B|b]ytes:(\d+ \(\d+.\d+ [K|M|G]iB\))/i',$strWlan0,$result);
        $strRxBytes = $result[1];
   } else {
        preg_match('/RX packets \d+ bytes (\d+ \(\d+.\d+ [K|M|G]iB\))/i',$strWlan0,$result);
        $strRxBytes = $result[1];
   }
   if (strpos($strWlan0,'TX bytes') !== false) {
        preg_match('/TX [B|b]ytes:(\d+ \(\d+.\d+ [K|M|G]iB\))/i',$strWlan0,$result);
        $strTxBytes = $result[1];
   } else {
        preg_match('/TX packets \d+ bytes (\d+ \(\d+.\d+ [K|M|G]iB\))/i',$strWlan0,$result);
        $strTxBytes = $result[1];
   }
   if (preg_match('/Access Point: ([0-9a-f:]+)/i',$strWlan0,$result)) { 
       $strBSSID = $result[1];
   }
   if (preg_match('/Connected to\ ([0-9a-f:]+)/i',$strWlan0,$result)) { 
       $strBSSID = $result[1];
   }
   if (preg_match('/Bit Rate([=:0-9\.]+ Mb\/s)/i',$strWlan0,$result)) {
       $strBitrate = str_replace(':', '', str_replace('=', '', $result[1]));
   }
   if (preg_match('/tx bitrate:\ ([0-9\.]+ Mbit\/s)/i',$strWlan0,$result)) {
       $strBitrate = str_replace(':', '', str_replace('=', '', $result[1]));
   }
   if (preg_match('/Tx-Power=([0-9]+ dBm)/i',$strWlan0,$result)) {
       $strTxPower = $result[1];
   }
   if (preg_match('/ESSID:\"([a-zA-Z0-9-_.\s]+)\"/i',$strWlan0,$result)) {
       $strSSID = str_replace('"','',$result[1]);
   }
   if (preg_match('/SSID:\ ([a-zA-Z0-9-_.\s]+)/i',$strWlan0,$result)) {
       $strSSID = str_replace(' freq','',$result[1]);
   }
   if (preg_match('/Link Quality=([0-9]+\/[0-9]+)/i',$strWlan0,$result)) {
        $strLinkQuality = $result[1];
        if (strpos($strLinkQuality, "/")) {
            $arrLinkQuality = explode("/", $strLinkQuality);
            $strLinkQuality = number_format(($arrLinkQuality[0] / $arrLinkQuality[1]) * 100)." &#37;";
        }
   }
   if (preg_match('/Signal Level=(-[0-9]+ dBm)/i',$strWlan0,$result)) {
       $strSignalLevel = $result[1];
   }
   if (preg_match('/Signal Level=([0-9]+\/[0-9]+)/i',$strWlan0,$result)) {
        $strSignalLevel = $result[1];
   }
   if (preg_match('/Frequency:([0-9.]+ GHz)/i',$strWlan0,$result)) {
        $strWifiFreq = $result[1];
        $strWifiChan = str_replace(" GHz", "", $strWifiFreq);
        $strWifiChan = str_replace(".", "", $strWifiChan);
        $strWifiChan = ConvertToChannel(str_replace(".", "", $strWifiChan));
   }
}
else {
    $strStatus = '<span style="color:#EE4B2B;background:black;">Interface is inactive</span>';
}

?>

<div class="network" id="networkbox">

<?php
function executeCommand($command) {
    $output = shell_exec($command);
    echo "<pre>$output</pre>";
}

function getWiFiBand($channel) {
    $wifiBands = array(
        '2.4 GHz' => range(1, 14),
        '5 GHz'   => range(36, 165)
    );

    foreach ($wifiBands as $band => $channels) {
        if (in_array($channel, $channels)) {
            return $band;
        }
    }

    return 'Unknown'; // Return 'Unknown' if the channel doesn't match any known WiFi band
}

function signalStrengthBars($signalStrength) {
    $bars = "";
    $maxStrength = 100;
    $numBars = 5;
    $strengthPerBar = $maxStrength / $numBars;
    $filledBars = round($signalStrength / $strengthPerBar);

    // Set default color
    $color = "#00D517"; // Green

    // Change color based on signal strength
    if ($signalStrength <= 60) {
        $color = "#E55C00"; // Orange
    }
    if ($signalStrength <= 30) {
        $color = "#B20000"; // Red
    }

    for ($i = 0; $i < $filledBars; $i++) {
	$bars .= "<span style='color:$color;'>&#x2588;</span>"; // Unicode FULL BLOCK character
    }
    for ($i = $filledBars; $i < $numBars; $i++) {
	$bars .= "<span style='color:#666666;'>&#x2588;</span>"; // Unicode FULL BLOCK character
    }
    return $bars;
}

function getAvailableRegulatoryDomains() {
    $crdaFile = '/usr/local/etc/regulatory.txt';
    $domains = [];

    if (file_exists($crdaFile)) {
        $fileContent = file_get_contents($crdaFile);
        preg_match_all('/country ([A-Z]+)/', $fileContent, $matches);
        $domains = isset($matches[1]) ? array_unique($matches[1]) : [];
    }

    return $domains;
}
$availableDomains = getAvailableRegulatoryDomains();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regulatory_domain'])) {
    $currentGlobalDomain = $_POST['regulatory_domain'];
} else {
    $fileContent = file_get_contents('/boot/firmware/cmdline.txt');
    if (preg_match('/\bcfg80211\.ieee80211_regdom=([^ ]+)/', $fileContent, $matches)) {
        $currentGlobalDomain = $matches[1];
    }
}

function parseNetworkInfo($line) {
    $parts = preg_split('/(?<!\\\):/', $line);

    $ssid = trim($parts[2]);
    $signalStrength = trim($parts[6]);
    $channel = trim($parts[4]);
    $securityType = trim($parts[8]);

    return compact('ssid', 'signalStrength', 'channel', 'securityType');
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            if (isset($_POST['ssid']) && isset($_POST['passphrase'])) {
                $ssid = $_POST['ssid'];
                $passphrase = $_POST['passphrase'];
                executeCommand("sudo nmcli connection add type wifi con-name \"$ssid\" ifname '*' ssid \"$ssid\" wifi-sec.key-mgmt wpa-psk wifi-sec.psk \"$passphrase\" ; sleep 1");
		echo "<p>(Please give the Wifi Information some time to initialize and refresh its status)</p>";
            } else {
                echo "Error: SSID and passphrase are required for adding a connection.";
            }
            break;
        case 'delete':
            if (isset($_POST['connection'])) {
                $connection = $_POST['connection'];
                executeCommand("sudo nmcli connection delete \"$connection\" sleep 1");
            } else {
                echo "Error: Connection name is required for deletion.";
            }
            break;
        case 'scan':
            $scanOutput = shell_exec('sudo nmcli -e yes -c no -g common device wifi list --rescan yes');
            $networks = explode("\n", trim($scanOutput));

            if (count($networks) > 1) {
                $header = array_map('trim', preg_split('/\s+/', $networks[0]));
                 unset($networks[0]);

        ?>
        <table>
            <thead>
                <tr>
                    <th>SSID</th>
                    <th>Signal Strength</th>
                    <th>Band</th>
                    <th>Channel</th>
                    <th>Security Type</th>
                    <th>Passphrase</th>
                    <th>Add Connection</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($networks as $network) : ?>
                    <?php $networkInfo = parseNetworkInfo($network); ?>
                    <?php if (!empty($networkInfo['ssid'])) : ?>
                        <tr>
                            <td><?php echo $networkInfo['ssid']; ?></td>
                            <td><?php echo signalStrengthBars($networkInfo['signalStrength']). "&nbsp" .$networkInfo['signalStrength']; ?>%</td>
                            <td><?php echo getWiFiBand($networkInfo['channel']); ?></td>
                            <td><?php echo $networkInfo['channel']; ?></td>
                            <td><?php echo $networkInfo['securityType']; ?></td>
                            <td>
                                <input type="text" name="passphrase[]" placeholder="Enter Passphrase" onkeyup="CheckPSK(this)">
                            </td>
                            <td>
                                <input type="hidden" name="ssid[]" value="<?php echo $networkInfo['ssid']; ?>">
                                <input type="hidden" name="regulatory_domain[]" value="US">
                                <button type="button" onclick="addConnection('<?php echo $networkInfo['ssid']; ?>')">Add This Network</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <script>
            function addConnection(ssid) {
                const index = document.getElementsByName('ssid[]').length;
                const button = event.target; // Get the button element that was clicked
                const passphrase = button.parentElement.previousElementSibling.children[0].value;

                const form = document.createElement('form');
                form.method = 'post';
                form.action = '';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'add';
                form.appendChild(actionInput);

                const ssidInput = document.createElement('input');
                ssidInput.type = 'hidden';
                ssidInput.name = 'ssid';
                ssidInput.value = ssid;
                form.appendChild(ssidInput);

                const passphraseInput = document.createElement('input');
                passphraseInput.type = 'hidden';
                passphraseInput.name = 'passphrase';
                passphraseInput.value = passphrase;
                form.appendChild(passphraseInput);

                document.body.appendChild(form);
                form.submit();
            }
        </script>

        <?php
    } else {
        echo "No WiFi networks found.";
    }
    break;

    }
}

$configuredConnections = shell_exec('sudo nmcli connection show');
$configuredConnections = explode("\n", trim($configuredConnections));

$wifiFound = false;
foreach ($configuredConnections as $connection) {
    if (strpos($connection, 'wifi') !== false) {
        $wifiFound = true;
        break; // Exit the loop once "wifi" is found in any element
    }
}

    if ($wifiFound) {
    ?>
    <h3>Configured Connections</h3>
    <table id='conns'>
        <thead>
            <tr>
                <th>Connection Name</th>
                <th>Delete Connection</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($configuredConnections as $configuredConnection) : ?>
                <?php if (!empty($configuredConnection) && strpos($configuredConnection, 'wifi') !== false) : ?>
                    <?php
                    $connectionParts = preg_split('/\s+/', $configuredConnection);
                    $connectionName = $connectionParts[0];
                    ?>
                    <tr>
                        <td class='conn_name'><?php echo $connectionName; ?></td>
                        <td>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="connection" value="<?php echo $connectionName; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <?php
echo '
<script>
$(document).ready(function(){
    function refreshDiv() {
        $("#wlan_info").load(location.href + " #wlan_info");
    }
    refreshDiv();
    setInterval(refreshDiv, 1000);
});
</script>
<div class="infobox" id="wlan_info">
<div class="infoheader">Wireless Information and Statistics</div>
<div class="intinfo"><div class="intheader">Interface Information</div>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Interface Name : wlan0<br />
&nbsp;&nbsp;&nbsp;&nbsp;Interface Status : ' . $strStatus . '<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IP Address : ' . $strIPAddress . '<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Subnet Mask : ' . $strNetMask . '<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mac Address : ' . $strHWAddress . '<br />
<br />
<div class="intheader">Interface Statistics</div>
&nbsp;&nbsp;&nbsp;&nbsp;Received Packets : ' . $strRxPackets . '<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Received Bytes : ' . $strRxBytes . '<br />
&nbsp;Transferred Packets : ' . $strTxPackets . '<br />
&nbsp;&nbsp;&nbsp;Transferred Bytes : ' . $strTxBytes . '<br />
<br />
</div>
<div class="wifiinfo">
<div class="intheader">Wireless Information</div>
&nbsp;&nbsp;&nbsp;Connected To : ' . $strSSID . '<br />
&nbsp;AP Mac Address : ' . $strBSSID . '<br />
<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bitrate : ' . $strBitrate . '<br />
&nbsp;&nbsp;&nbsp;Signal Level : ' . $strSignalLevel . '<br />
<br />';
if ($strTxPower) { echo '&nbsp;Transmit Power : ' . $strTxPower .'<br />'."\n"; } else { echo "<br />\n"; }
if ($strLinkQuality) { echo '&nbsp;&nbsp;&nbsp;Link Quality : ' . $strLinkQuality . '<br />'."\n"; } else { echo "<br />\n"; }
if (($strWifiFreq) && ($strWifiChan) && ($strWifiChan != "Invalid Channel")) {
        echo '&nbsp;&nbsp;&nbsp;Channel Info : ' . $strWifiChan . ' (' . $strWifiFreq . ')<br />'."\n";
} else {
        echo "<br />\n";
}
if (isset($currentGlobalDomain)) {
    echo '&nbsp;&nbsp;&nbsp;WiFi Country : '.$currentGlobalDomain."<br />\n";
} else {
    echo "<br />\n";
}
echo '
<br />
<br />
</div>
<br />
</div>';
}
else {
    echo '<h4>(No Connections Configured)</h4>';
}
?>
<h3>Add Connections</h3>
<form method="post">
    <label for="action">Action:</label>
    <select name="action" id="action" onchange="showHideFormFields()">
        <option value="" selected disabled>Choose Action...</option>
        <option value="scan">Scan &amp Add Available Networks (10 secs.)</option>
        <option value="add">Add Connection Manually</option>
    </select>
    <br>
    <br>
    <div class="form-fields" id="add-connection-fields">
        <label for="ssid">SSID:</label>
        <input type="text" name="ssid" placeholder="Enter SSID" onkeyup="CheckSSID(this)">

        <label for="passphrase">Passphrase:</label>
        <input type="text" name="passphrase" placeholder="Enter Passphrase" onkeyup="CheckPSK(this)">
        <button class="submit" type="submit">Submit</button>
    </div>
</form>

<script>
    function showHideFormFields() {
        const action = document.getElementById('action').value;
        const addConnectionFields = document.getElementById('add-connection-fields');

        if (action === 'add') {
            addConnectionFields.style.display = 'block';
        } else {
            addConnectionFields.style.display = 'none';
            jQuery('button.submit').trigger('click');
        }
        iframe = jQuery(window.parent.document).find('iframe' );
        iframe.height( iframe.contents().find("html").height() );
    }
</script>

<form method="post">
    <label for="select-domain">Select Country:</label>
    <select name="regulatory_domain" id="select-domain">
        <?php foreach ($availableDomains as $domain) : ?>
	    <option value="<?= $domain; ?>" <?= (trim($domain) === trim($currentGlobalDomain)) ? 'selected' : ''; ?>>
                <?= $domain; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="action" value="set_domain">Set Country</button>
</form>

<?php
if (isset($_POST['action']) && $_POST['action'] === 'set_domain') {
    if (isset($_POST['regulatory_domain'])) {
        $selectedDomain = $_POST['regulatory_domain'];
        executeCommand("sudo iw reg set $selectedDomain");
        executeCommand("sudo sed -i 's/cfg80211\.ieee80211_regdom=.*/cfg80211.ieee80211_regdom=$selectedDomain/' /boot/firmware/cmdline.txt ; sleep 1");
	echo "<pre>WiFi Country Updated.</pre>";
    } else {
        echo "Error: Please select a regulatory domain.";
    }
}

?>

</div>
</body>
</html>

