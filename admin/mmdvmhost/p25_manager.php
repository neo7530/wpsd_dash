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

// Check if P25 is Enabled
$testMMDVModeP25 = getConfigItem("P25 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
$testYSF2P25 = $_SESSION['YSF2P25Configs']['Enabled']['Enabled'];
if ( $testMMDVModeP25 == 1 || $testYSF2P25 == 1 ) {
    // Check that the remote is enabled
    if (isset($_SESSION['P25GatewayConfigs']['Remote Commands']['Enable']) && (isset($_SESSION['P25GatewayConfigs']['Remote Commands']['Port'])) && ($_SESSION['P25GatewayConfigs']['Remote Commands']['Enable'] == 1)) {
	$remotePort = $_SESSION['P25GatewayConfigs']['Remote Commands']['Port'];
	if (!empty($_POST) && isset($_POST["p25MgrSubmit"])) {
	    // Handle Posted Data
	    if (preg_match('/[^A-Za-z0-9]/',$_POST['p25LinkHost'])) {
		unset($_POST['p25LinkHost']);
	    }
	    if ($_POST["Link"] == "LINK") {
		if ($_POST['p25LinkHost'] == "none") {
		    $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup 9999";
		}
		else {
		    $remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup ".$_POST['p25LinkHost'];
		}
	    } else if ($_POST["Link"] == "UNLINK") {
		$remoteCommand = "cd /var/log/pi-star && sudo /usr/local/bin/RemoteCommand ".$remotePort." TalkGroup 9999";
	    }
	    else {
		echo "<div style='text-align:left;font-weight:bold;'>P25 Link Manager</div>\n";
		echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td><p>";
		echo "Something wrong with your input, (Neither Link nor Unlink Sent) - please try again";
		echo "<br />Page reloading...</p></td></tr>\n</table>\n";
		unset($_POST);
		echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
	    }
	    if (empty($_POST['p25LinkHost'])) {
		echo "<div style='text-align:left;font-weight:bold;'>P25 Link Manager</div>\n";
		echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td><p>";
		echo "Something wrong with your input, (No target specified) -  please try again";
		echo "<br />Page reloading...</p></td></tr>\n</table>\n";
		unset($_POST);
		echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
	    }
	    if (isset($remoteCommand)) {
		echo "<div style='text-align:left;font-weight:bold;'>P25 Link Manager</div>\n";
		echo "<table>\n<tr><th>Command Output</th></tr>\n<tr><td><p>";
		echo exec($remoteCommand);
		echo "<br />Page reloading...</p></td></tr>\n</table>\n";
		echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
	    }
	}
	else {
	    // Output HTML
	    ?>
    	    <div style="text-align:left;font-weight:bold;">P25 Link Manager</div>
	    <form action="//<?php echo htmlentities($_SERVER['HTTP_HOST']).htmlentities($_SERVER['PHP_SELF']); ?>?func=p25_man" method="post">
		<table>
		    <tr>
			<th width="150"><a class="tooltip" href="#">Select Reflector<span><b>Select Reflector</b></span></a></th>
			<th width="150"><a class="tooltip" href="#">Current Link<span><b>Current Link</b></span></a></th>
			<th width="150"><a class="tooltip" href="#">Link / Unlink<span><b>Link or unlink</b></span></a></th>
			<th width="150"><a class="tooltip" href="#">Action<span><b>Action</b></span></a></th>
		    </tr>
		    <tr>
			<td>
			    <select name="p25LinkHost" class="p25LinkHost">
				<?php
				if (isset($_SESSION['P25GatewayConfigs']['Network']['Startup'])) {
				    $testP25Host = $_SESSION['P25GatewayConfigs']['Network']['Startup'];
                                } elseif
                                    (isset($_SESSION['P25GatewayConfigs']['Network']['Static'])) {
                                        $testP25Host = $_SESSION['P25GatewayConfigs']['Network']['Static'];
                                }
				else {
				    $testP25Host = "none";
				}
				if ($testP25Host == "") {
				    echo "      <option value=\"none\" selected=\"selected\">None</option>\n";
				}
				else {
				    echo "      <option value=\"none\">None</option>\n";
				}
				if ($testP25Host == "10") {
				    echo "      <option value=\"10\" selected=\"selected\">10 - Parrot</option>\n";
				}
				else {
				    echo "      <option value=\"10\">10 - Parrot</option>\n";
				}
				$p25Hosts = fopen("/usr/local/etc/P25Hosts.txt", "r");
				while (!feof($p25Hosts)) {
              			    $p25HostsLine = fgets($p25Hosts);
				    $p25Host = preg_split('/\s+/', $p25HostsLine);
				    if ((strpos($p25Host[0], '#') === FALSE ) && ($p25Host[0] != '')) {
                			if ($testP25Host == $p25Host[0]) {
					    echo "      <option value=\"$p25Host[0]\" selected=\"selected\">$p25Host[0] - $p25Host[1]</option>\n";
					}
					else {
					    echo "      <option value=\"$p25Host[0]\">$p25Host[0] - $p25Host[1]</option>\n";
					}
				    }
				}
				fclose($p25Hosts);
				if (file_exists('/usr/local/etc/P25HostsLocal.txt')) {
              			    $p25Hosts2 = fopen("/usr/local/etc/P25HostsLocal.txt", "r");
				    while (!feof($p25Hosts2)) {
                			$p25HostsLine2 = fgets($p25Hosts2);
					$p25Host2 = preg_split('/\s+/', $p25HostsLine2);
					if ((strpos($p25Host2[0], '#') === FALSE ) && ($p25Host2[0] != '')) {
                        		    if ($testP25Host == $p25Host2[0]) {
						echo "      <option value=\"$p25Host2[0]\" selected=\"selected\">$p25Host2[0] - $p25Host2[1]</option>\n";
					    }
					    else {
						echo "      <option value=\"$p25Host2[0]\">$p25Host2[0] - $p25Host2[1]</option>\n";
					    }
					}
				    }
				    fclose($p25Hosts2);
				}

				$target = getActualLink($logLinesP25Gateway, "P25");
				$target = str_replace("TG", "", $target);
				if (strpos($target, "Not") === false) {	
				    $target_lookup = exec("grep -w \"$target\" /usr/local/etc/TGList_P25.txt | awk -F';' '{print $2}'");
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
			    <input type="submit" name="p25MgrSubmit" value="Request Change" />
			</td>
		    </tr>
                    <tr>
                      <td colspan="4" style="white-space:normal;padding: 3px;">
                        <a href="https://w0chp.radio/p25-reflectors/" target="_blank">List of P25 Reflectors (searchable/downloadable)</a>
                      </td>
                    </tr>
		</table>
	    </form>
	<?php
	}
    }
}
?>
