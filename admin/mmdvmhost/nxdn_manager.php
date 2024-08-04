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

// Check if NXDN is Enabled
$testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
$testDMR2NXDN = $_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'];
$testYSF2NXDN = $_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'];
if ( $testMMDVModeNXDN == 1 || $testDMR2NXDN == 1 || $testYSF2NXDN == 1 ) {
    // Check that the remote is enabled
    if (isset($_SESSION['NXDNGatewayConfigs']['Remote Commands']['Enable']) && (isset($_SESSION['NXDNGatewayConfigs']['Remote Commands']['Port'])) && ($_SESSION['NXDNGatewayConfigs']['Remote Commands']['Enable'] == 1)) {
	$remotePort = $_SESSION['NXDNGatewayConfigs']['Remote Commands']['Port'];
	if (!empty($_POST) && isset($_POST["nxdnMgrSubmit"])) {
	    // Handle Posted Data
	    if (preg_match('/[^A-Za-z0-9]/',$_POST['nxdnLinkHost'])) {
		unset ($_POST['nxdnLinkHost']);
	    }
	    if ($_POST["Link"] == "LINK") {
		if ($_POST['nxdnLinkHost'] == "none") {
		$remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup 9999";

		}
		else {
		    $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup ".$_POST['nxdnLinkHost'];
		}
	    }
	    else if ($_POST["Link"] == "UNLINK") {
	        $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup 9999";
	    }
	    else {
		echo "<div style='text-align:left;font-weight:bold;'>NXDN Link Manager</div>\n";
		echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td><p>";
		echo "Something wrong with your input, (Neither Link nor Unlink Sent) - please try again";
		echo "<br />Page reloading...</p></td></tr>\n</table>\n";
		unset($_POST);
		echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
	    }
	    if (empty($_POST['nxdnLinkHost'])) {
		echo "<div style='text-align:left;font-weight:bold;'>NXDN Link Manager</div>\n";
		echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td><p>";
		echo "Something wrong with your input, (No target specified) -  please try again";
		echo "<br />Page reloading...</p></td></tr>\n</table>\n";
		unset($_POST);
		echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
	    }
	    if (isset($remoteCommand)) {
		echo "<div style='text-align:left;font-weight:bold;'>NXDN Link Manager</div>\n";
		echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td><p>";
		echo exec($remoteCommand);
		echo "<br />Page reloading...</p></td></tr>\n</table>\n";
		echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
	    }
	}
	else {
	    // Output HTML
?>
    		<div style="text-align:left;font-weight:bold;">NXDN Link Manager</div>
		<form action="//<?php echo htmlentities($_SERVER['HTTP_HOST']).htmlentities($_SERVER['PHP_SELF']); ?>?func=nxdn_man" method="post">
		    <table>
			<tr>
			    <th width="150"><a class="tooltip" href="#">Select Reflector<span><b>Select Reflector</b></span></a></th>
			    <th width="150"><a class="tooltip" href="#">Current Link<span><b>Current Link</b></span></a></th>
			    <th width="150"><a class="tooltip" href="#">Link / Un-Link<span><b>Link / Un-Link</b></span></a></th>
			    <th width="150"><a class="tooltip" href="#">Action<span><b>Action</b></span></a></th>
			</tr>
			<tr>
			    <td>
				<select name="nxdnLinkHost" class="nxdnLinkHost">
				    <?php
				    $nxdnHosts = fopen("/usr/local/etc/NXDNHosts.txt", "r");
				    if (isset($_SESSION['NXDNGatewayConfigs']['Network']['Startup'])) {
					$testNXDNHost = $_SESSION['NXDNGatewayConfigs']['Network']['Startup'];
				    } elseif
					 (isset($_SESSION['NXDNGatewayConfigs']['Network']['Static'])) {
					    $testNXDNHost = $_SESSION['NXDNGatewayConfigs']['Network']['Static'];
				    }
				    else {
					$testNXDNHost = "";
				    }
				    if ($testNXDNHost == "") {
					echo "      <option value=\"none\" selected=\"selected\">None</option>\n";
				    }
				    else {
					echo "      <option value=\"none\">None</option>\n";
				    }
				    if ($testNXDNHost == "10") {
					echo "      <option value=\"10\" selected=\"selected\">10 - Parrot</option>\n";
				    }
				    else {
					echo "      <option value=\"10\">10 - Parrot</option>\n";
				    }
				    while (!feof($nxdnHosts)) {
					$nxdnHostsLine = fgets($nxdnHosts);
					$nxdnHost = preg_split('/\s+/', $nxdnHostsLine);
					if ((strpos($nxdnHost[0], '#') === FALSE ) && ($nxdnHost[0] != '')) {
					    if ($testNXDNHost == $nxdnHost[0]) {
						echo "      <option value=\"$nxdnHost[0]\" selected=\"selected\">$nxdnHost[0] - $nxdnHost[1]</option>\n";
					    }
					    else {
						echo "      <option value=\"$nxdnHost[0]\">$nxdnHost[0] - $nxdnHost[1]</option>\n";
					    }
					}
				    }
				    fclose($nxdnHosts);
				    if (file_exists('/usr/local/etc/NXDNHostsLocal.txt')) {
					$nxdnHosts2 = fopen("/usr/local/etc/NXDNHostsLocal.txt", "r");
					while (!feof($nxdnHosts2)) {
					    $nxdnHostsLine2 = fgets($nxdnHosts2);
					    $nxdnHost2 = preg_split('/\s+/', $nxdnHostsLine2);
					    if ((strpos($nxdnHost2[0], '#') === FALSE ) && ($nxdnHost2[0] != '')) {
						if ($testNXDNHost == $nxdnHost2[0]) {
						    echo "      <option value=\"$nxdnHost2[0]\" selected=\"selected\">$nxdnHost2[0] - $nxdnHost2[1]</option>\n";
						}
						else {
						    echo "      <option value=\"$nxdnHost2[0]\">$nxdnHost2[0] - $nxdnHost2[1]</option>\n";
						}
					    }
					}
					fclose($nxdnHosts2);
				    }

                                    $target = getActualLink($logLinesNXDNGateway, "NXDN");
                                    $target = str_replace("TG", "", $target);
				    if (strpos($target, "Not") === false) {	
				    	$target_lookup = exec("grep -w \"$target\" /usr/local/etc/TGList_NXDN.txt | awk -F';' '{print $2}'");
				    	if (!empty($target_lookup)) {
					    $target = "TG $target: $target_lookup";
				    	} else {
					    $target = $target;
				    	}
				    } else {
					    $target = "Not Linked";
				    }
				    ?>
				</select>
			    </td>
			    <script>
			          $(document).ready(function(){
			            setInterval(function(){
			                $(".CheckLink").load(window.location.href + " .CheckLink" );
			                },3000);
			            });
			    </script>
			    <td><strong class="CheckLink"><?php echo $target; ?></strong></td>
			    <td>
                            <input type="radio" id="link" name="Link" value="LINK" /> <label for="link"/>Link</label>
                            <input type="radio" id="unlink" name="Link" value="UNLINK" checked="checked"  /> <label for="unlink"/>Un-Link</label>
			    </td>
			    <td>
				<input type="submit" name="nxdnMgrSubmit" value="Request Change" />
			    </td>
			</tr>
                    	<tr>
                      	  <td colspan="4" style="white-space:normal;padding: 3px;"><a href="https://w0chp.radio/nxdn-reflectors/" target="_blank">List of NXDN Reflectors (searchable/downloadable)</a></td>
                    	</tr>
		    </table>
		</form>
	<?php
	}
    }
}
?>
