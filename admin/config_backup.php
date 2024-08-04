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

// Load the language support
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/admin/config_backup.php") {
    // Sanity Check Passed.
    header('Cache-Control: no-cache');
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html lang="en">
	<head>
	    <meta name="language" content="English" />
	    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	    <meta http-equiv="pragma" content="no-cache" />
	    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
	    <meta http-equiv="Expires" content="0" />
	    <title>WPSD <?php echo __( 'Digital Voice' ) . " ".__( 'Dashboard' )." - ".__( 'Backup/Restore' );?></title>
	    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
	</head>
	<body>
	    <div class="container">
		<div class="header">
		    <div class="SmallHeader shLeft">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
                    <div class="SmallHeader shRight">
                      <div id="CheckUpdate">
                      <?php
                          include $_SERVER['DOCUMENT_ROOT'].'/includes/checkupdates.php';
                      ?>
                      </div><br />
                    </div>
		    <h1>WPSD <?php echo __( 'Digital Voice' ) . " - ".__( 'Backup/Restore' );?></h1>
		    <p>
			<div class="navbar">
			    <a class="menuconfig" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
			    <a class="menuupdate" href="/admin/update.php"><?php echo __( 'WPSD Update' );?></a>
			    <a class="menupower" href="/admin/power.php"><?php echo __( 'Power' );?></a>
			    <a class="menuadmin" href="/admin/"><?php echo __( 'Admin' );?></a>
			    <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
			</div>
		    </p>
		</div>
		<div class="contentwide">
		<h2 class="ConfSec center"><?php echo __( 'Backup/Restore' );?></h2>
		    <?php if (!empty($_POST)) {
			echo '<table width="100%">'."\n";
			
			if ( escapeshellcmd($_POST["action"]) == "download" ) {
			    $backupDir = "/tmp/config_backup";
			    $backupZip = "/tmp/config_backup.zip";
			    $hostNameInfo = exec('cat /etc/hostname');
			    
			    exec("sudo rm -rf $backupZip > /dev/null");
			    exec("sudo rm -rf $backupDir > /dev/null");
			    exec("sudo mkdir $backupDir > /dev/null");
			    if (exec('cat /etc/dhcpcd.conf | grep "static ip_address" | grep -v "#"')) {
				exec("sudo cp /etc/dhcpcd.conf $backupDir > /dev/null");
			    }
			    exec("sudo cp /etc/wpa_supplicant/wpa_supplicant.conf $backupDir > /dev/null");
			    exec("sudo cp /etc/NetworkManager/system.connections/*.nmconnection $backupDir > /dev/null");
			    exec("sudo cp /etc/wpsd-upnp-rules $backupDir > /dev/null");
                	    exec("sudo cp /etc/hostapd/hostapd.conf $backupDir > /dev/null");
			    exec("sudo cp /etc/pistar-css.ini $backupDir > /dev/null");
			    exec("sudo cp /etc/pistar-release $backupDir > /dev/null");
			    exec("sudo cp /etc/aprsgateway $backupDir > /dev/null");
			    exec("sudo cp /etc/ircddbgateway $backupDir > /dev/null");
			    exec("sudo cp /etc/mmdvmhost $backupDir > /dev/null");
			    exec("sudo cp /etc/dapnetgateway $backupDir > /dev/null");
                	    exec("sudo cp /etc/pistar-css.ini $backupDir > /dev/null");
			    exec("sudo cp /etc/p25gateway $backupDir > /dev/null");
			    exec("sudo cp /etc/ysfgateway $backupDir > /dev/null");
			    exec("sudo cp /etc/dmr2nxdn $backupDir > /dev/null");
			    exec("sudo cp /etc/dmr2ysf $backupDir > /dev/null");
			    exec("sudo cp /etc/nxdn2dmr $backupDir > /dev/null");
			    exec("sudo cp /etc/ysf2dmr $backupDir > /dev/null");
                	    exec("sudo cp /etc/dgidgateway $backupDir > /dev/null");
                	    exec("sudo cp /etc/nxdngateway $backupDir > /dev/null");
                	    exec("sudo cp /etc/m17gateway $backupDir > /dev/null");
			    exec("sudo cp /etc/ysf2nxdn $backupDir > /dev/null");
			    exec("sudo cp /etc/ysf2p25 $backupDir > /dev/null");
			    exec("sudo cp /etc/dmrgateway $backupDir > /dev/null");
			    exec("sudo cp /etc/starnetserver $backupDir > /dev/null");
			    exec("sudo cp /etc/timeserver $backupDir > /dev/null");
			    exec("sudo cp /etc/dstar-radio.* $backupDir > /dev/null");
			    exec("sudo cp /etc/pistar-remote $backupDir > /dev/null");
			    exec("sudo cp /etc/hosts $backupDir > /dev/null");
			    exec("sudo cp /etc/hostname $backupDir > /dev/null");
			    exec("sudo cp /etc/bmapi.key $backupDir > /dev/null");
			    exec("sudo cp /etc/dapnetapi.key $backupDir > /dev/null");
			    exec("sudo cp /etc/default/gpsd $backupDir > /dev/null");
			    exec("sudo cp /etc/*_paused $backupDir > /dev/null");
			    exec("sudo cp /etc/.bm_tgs.json.saved $backupDir > /dev/null");
			    exec("sudo cp /etc/timeserver.disable $backupDir > /dev/null");
			    exec("sudo cp /usr/local/etc/RSSI.dat $backupDir > /dev/null");
			    exec("sudo cp /var/www/dashboard/config/ircddblocal.php $backupDir > /dev/null");
			    exec("sudo cp /var/www/dashboard/config/config.php $backupDir > /dev/null");
			    exec("sudo cp /var/www/dashboard/config/language.php $backupDir > /dev/null");
			    exec("sudo find /root/ -maxdepth 1 -name '*Hosts.txt' -exec cp {} $backupDir \; > /dev/null");
			    // Begin DV-Mega Cast logic to save user cast settings
			    if (isDVmegaCast() == 1) {
				exec("sudo mkdir -p $backupDir/cast-settings > /dev/null");
				exec("sudo sh -c 'cp -a \"/usr/local/cast/etc/\"* \"$backupDir/cast-settings/\"' > /dev/null");
			    }
			    exec("sudo cp -a /etc/WPSD_config_mgr $backupDir > /dev/null");
			    chdir($backupDir);
			    exec("sudo zip -r $backupZip * > /dev/null");

			    if (file_exists($backupZip)) {
				$utc_time = gmdate('Y-m-d H:i:s');
				$utc_tz =  new DateTimeZone('UTC');
				$local_tz = new DateTimeZone(date_default_timezone_get ());
				$dt = new DateTime($utc_time, $utc_tz);
				$dt->setTimeZone($local_tz);
                		$local_time = $dt->format('Y-M-d');
				header('Content-Type: application/zip');
				if ($hostNameInfo != "pi-star") {
				    header('Content-Disposition: attachment; filename="'.basename("WPSD_Config_".$hostNameInfo."_".$local_time.".zip").'"');
				}
				else {
				    header('Content-Disposition: attachment; filename="'.basename("WPSD_Config_$local_time.zip").'"');
				}
				header('Content-Length: ' . filesize($backupZip));
				ob_clean();
				flush();
				readfile($backupZip);
				exit();
			    }
			    
			};
			if ( escapeshellcmd($_POST["action"]) == "restore" ) {
			    echo "<tr><th colspan=\"2\">Configuration Restore</th></tr>\n";
			    
			    $target_dir = "/tmp/config_restore/";
			    exec("sudo rm -rf $target_dir > /dev/null");
			    exec("mkdir $target_dir > /dev/null");
			    if($_FILES["fileToUpload"]["name"]) {
				$filename = $_FILES["fileToUpload"]["name"];
	  			$source = $_FILES["fileToUpload"]["tmp_name"];
				$type = $_FILES["fileToUpload"]["type"];
				
				$name = explode(".", $filename);
				$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
				foreach($accepted_types as $mime_type) {
				    if($mime_type == $type) {
					$okay = true;
					break;
				    }
				}
			    }
			    $continue = false;
			    
			    if (isset($name))
			    {
				$continue = strtolower($name[1]) == 'zip' ? true : false;
			    }
			    
			    if(!$continue) {
				$output .= "The file you are trying to upload is not a .zip file. Please try again.\n";
			    }
			    
			    if (isset($filename))
			    {
				$target_path = $target_dir.$filename;
			    }
			    
			    if(isset($target_path) && move_uploaded_file($source, $target_path)) {
				$zip = new ZipArchive();
				$x = $zip->open($target_path);
				if ($x === true) {
			            $zip->extractTo($target_dir); // change this to the correct site path
			            $zip->close();
			            unlink($target_path);
				}
				
				// Stop the DV Services
			    	exec('sudo wpsd-services fullstop > /dev/null');
	
				// Overwrite the configs
				exec("sudo rm -rf /etc/dstar-radio.* /etc/bmapi.key /etc/dapnetapi.key /etc/timeserver.disable /etc/WPSD_config_mgr > /dev/null");
				exec("sudo mv -f /tmp/config_restore/tmp/config_backup/* /tmp/config_restore/ > /dev/null");
				exec("sudo rm -rf /tmp/config_restore/tmp > /dev/null");
                                exec("sudo cp -a /tmp/config_restore/WPSD_config_mgr /etc/ > /dev/null");
				// Begin DV-Mega Cast logic to save user cast settings
				if (isDVmegaCast() == 1) {
				    exec("sudo mkdir -p /usr/local/cast/etc  > /dev/null");
				    exec("sudo sh -c 'cp -a /tmp/config_restore/cast-settings/* /usr/local/cast/etc/' > /dev/null");
				    exec('sudo chmod 775 /usr/local/cast/etc ; sudo chown -R www-data:pi-star /usr/local/cast/etc ; sudo chmod 664 /usr/local/cast/etc/*');	
				    exec('sudo /usr/local/cast/sbin/RSET.sh  > /dev/null 2>&1 &');
				}
                                exec("sudo mv -f /tmp/config_restore/gpsd /etc/default/ > /dev/null");
				exec("sudo mv -f /tmp/config_restore/RSSI.dat /usr/local/etc/ > /dev/null");
				exec("sudo mv -f /tmp/config_restore/ircddblocal.php /var/www/dashboard/config/ > /dev/null");
				exec("sudo mv -f /tmp/config_restore/config.php /var/www/dashboard/config/ > /dev/null");
				exec("sudo mv -f /tmp/config_restore/language.php /var/www/dashboard/config/ > /dev/null");
				exec('sudo find /tmp/config_restore/ -maxdepth 1 -name "*Hosts.txt" -exec mv -fv {} /root \; > /dev/null');
				exec("sudo mv -f /tmp/config_restore/wpa_supplicant.conf /etc/wpa_supplicant/ > /dev/null");
				exec("sudo mv -f /tmp/config_restore/*.nmconnection /etc/NetworkManager/system.connections/ > /dev/null");
				exec("sudo mv -f /tmp/config_restore/wpsd-upnp-rules /etc/ > /dev/null");
                		exec("sudo mv -f /tmp/config_restore/hostapd.conf /etc/hostapd/ > /dev/null");
				exec("sudo mv -f /tmp/config_restore/*_paused /etc/ > /dev/null");
				exec("sudo cp -a /tmp/config_restore/.bm_tgs.json.saved /etc/ > /dev/null");
				exec("sudo mv -f /tmp/config_restore/* /etc/ > /dev/null");
				
				//Restore the Timezone Config
				$timeZone = exec("grep -o -P \"date_default_timezone_set\\('\\K[^']+\" /var/www/dashboard/config/config.php");
				$timeZone = preg_replace( "/\r|\n/", "", $timeZone);                    //Remove the linebreaks
				exec('sudo timedatectl set-timezone '.$timeZone.' > /dev/null');
				
				//Restore ircDDGBateway Link Manager Password
				$ircRemotePassword = exec('grep remotePassword /etc/ircddbgateway | awk -F\'=\' \'{print $2}\'');
				exec('sudo sed -i "/password=/c\\password='.$ircRemotePassword.'" /root/.Remote\ Control');
				
				exec('sudo /usr/local/sbin/nextion-driver-helper > /dev/null');  // Run the Nextion driver helper based on selected MMDVMHost display type

				// Reset the GPIO Pins on Pi4, Pi5 etc. only
				exec('sudo /usr/local/sbin/wpsd-modemreset boot > /dev/null');

				// Start the services
			    	exec('sudo wpsd-services start > /dev/null &');
	
				// Complete
				$output .= "<h3 style='text-align:center'>Configuration Restoration Complete.</h3>\n";
			    }
			    else {
				$output .= "There was a problem with the upload. Please try again.<br />";
				$output .= "\n".'<button onclick="goBack()">Go Back</button><br />'."\n";
				$output .= '<script>'."\n";
				$output .= 'function goBack() {'."\n";
				$output .= '    window.history.back();'."\n";
				$output .= '}'."\n";
				$output .= '</script>'."\n";
			    }
			    echo "<tr><td>$output</td></tr>\n";
			};
			
			echo "</table>\n";
		    } else { ?>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
			    <table width="100%">
				<tr>
				    <td align="center" valign="top" width="50%"><h3>Download Configuration</h3><br />
					<button style="border: none; background: none; margin: 15px 0px;" name="action" value="download"><img src="/images/download.png" border="0" alt="Download Config" /></button>
				    </td>
				    <td align="center" valign="top"><h3>Restore Configuration</h3><br />
					<button style="border: none; background: none; margin: 10px 0px;" name="action" value="restore"><img src="/images/restore.png" border="0" alt="Restore Config" /></button><br />
    					<input type="file" style="margin: 5px 0px;" name="fileToUpload" id="fileToUpload" />
				    </td>
				</tr>
				<tr>
				    <td colspan="2" align="justify" style="padding: 8px;">
					<br />
					This backup and restore utility will backup your setup / configuration to a zip file, and allow you to restore them later<br />
					either to this WPSD instance or another one.<br />
					<ul>
					    <li>System Passwords / Dashboard passwords are <strong>not</strong> backed up / restored.</li>
					    <li>Wireless Configuration <strong>is</strong> backed up and restored.</li>
					    <li>Profiles are included in backups / restores.</li>
					</ul>
				    </td>
				</tr>
			    </table>
			</form>
		    <?php } ?>
		</div>
		<div class="footer">
			<a href="https://wpsd.radio/">WPSD</a> &copy; <code>W0CHP</code> 2020-<?php echo date("Y"); ?><br />
		</div>
	    </div>
	</body>
    </html>
<?php
}
?>
