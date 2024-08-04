<?php
session_set_cookie_params(0, "/");
session_name("WPSD_Session");
session_id('wpsdsession');
session_start();

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
unset($_SESSION['PiStarRelease']);
checkSessionValidity();

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
	<title>WPSD - Digital Voice Dashboard - Appearance Settings</title>
	<script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
	<script type="text/javascript" src="/css/farbtastic/farbtastic.min.js?version=<?php echo $versionCmd; ?>"></script>
	<link rel="stylesheet" type="text/css" href="/css/farbtastic/farbtastic.css" />
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
	<style type="text/css" media="screen">
	 .colorwell {
	     border: 2px solid #fff;
	     width: 6em;
	     text-align: center;
	     cursor: pointer;
	 }
	 body .colorwell-selected {
	     border: 2px solid #000;
	     font-weight: bold;
	 }
	</style>
	<script type="text/javascript">
	 function cssDownload()
	 {
	     window.location.href = "/admin/advanced/css_download.php";
	 }
	 
	 function cssUpload()
	 {
	     document.getElementById('fileid').addEventListener('change', submitForm);
	     document.getElementById('fileid').click();
	 }
	 
         function submitForm() {
	     document.getElementById('cssUpload').submit();
         }
	 
	 function cssReset()
	 {
	     if (confirm('WARNING: This will reset the appearance settings back to factory defaults.\n\nAre you SURE you want to do this?\n\nPress OK to restore the factory appearance configuration.\nPress Cancel to go back.')) {
		 document.getElementById("cssReset").submit();
	     } else {
		 return false;
	     }
	 }

	 $(document).ready(function() {
	     var f = $.farbtastic('#colorpicker');
	     var p = $('#colorpicker').css('opacity', 1).hide();
	     var selected;
	     $('.colorwell')
	         .each(function () { f.linkTo(this); $(this).css('opacity', 1); })
	         .focus(function() {
		     if (selected) {
			 $(selected).removeClass('colorwell-selected');
		     }
		     p.show();
		     f.linkTo(this);
		     $(selected = this).addClass('colorwell-selected');
		 })
		 .blur(function() {
		     if (selected) {
			 $(selected).removeClass('colorwell-selected');
			 seleted = null;
			 p.hide();
			 f.linkTo(function(){});
		     }
		 });
	 });
	 
	</script>
    </head>
    <body>
	<div class="container">
	    <?php include $_SERVER['DOCUMENT_ROOT'].'/admin/advanced/header-menu.inc'; ?>
	    <div class="contentwide">
		
		<?php

		if (empty($_POST['CallLookupProvider']) != TRUE) {
		    exec('sudo mount -o remount,rw /');
		    exec('sudo sed -i "/CallLookupProvider = /c\\\CallLookupProvider = '.escapeshellcmd($_POST['CallProvider']).'" /etc/pistar-release');	
		    unset($_POST);
		    echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},0);</script>';
		    die();
		}

		// Check if we are using the new CSS configuration syntax
		if (file_exists('/etc/pistar-css.ini')) {
		    $dataContent = file_get_contents('/etc/pistar-css.ini');
		    
		    if (! preg_match('/NavPanelColor/', $dataContent))
		    {
			// Reset CSS configuration
			exec('sudo mount -o remount,rw /');                             // Make rootfs writable
			exec('sudo rm -rf /etc/pistar-css.ini');                        // Delete the Config
			echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},0);</script>';
			die();
		    }
		}
		if (!file_exists('/etc/pistar-css.ini')) {
		    //The source file does not exist, lets create it....
		    $outFile = fopen("/tmp/bW1kd4jg6b3N0DQo.tmp", "w") or die("Unable to open file!");
		    $headers = stream_context_create(Array("http" => Array("method"  => "GET",
							    		   "timeout" => 10,
							    		    "header"  => "User-agent: WPSD-CSS-Reset - $versionCmd",
							    		    'request_fulluri' => True )));
		    $fileContent = @file_get_contents("https://wpsd-swd.w0chp.net/WPSD-SWD/W0CHP-PiStar-Installer/raw/branch/master/supporting-files/pistar-css-W0CHP.ini", false, $headers);
		    fwrite($outFile, $fileContent);
		    fclose($outFile);
		    
		    // Put the file back where it should be
		    exec('sudo mount -o remount,rw /');                             // Make rootfs writable
		    exec('sudo cp /tmp/bW1kd4jg6b3N0DQo.tmp /etc/pistar-css.ini');  // Move the file back
		    exec('sudo chmod 644 /etc/pistar-css.ini');                     // Set the correct runtime permissions
		    exec('sudo chown root:root /etc/pistar-css.ini');               // Set the owner
		}
		//Do some file wrangling...
		exec('sudo cp /etc/pistar-css.ini /tmp/bW1kd4jg6b3N0DQo.tmp');
		exec('sudo chown www-data:www-data /tmp/bW1kd4jg6b3N0DQo.tmp');
		exec('sudo chmod 664 /tmp/bW1kd4jg6b3N0DQo.tmp');
		
		//ini file to open
		$filepath = '/tmp/bW1kd4jg6b3N0DQo.tmp';
		
		//after the form submit
		if($_POST) {
		    $data = $_POST;
		    // CSS Factory Reset Handler Here
		    if (empty($_POST['cssReset']) != TRUE) {
			echo "<br />\n";
			echo "<table>\n";
			echo "<tr><td>Resetting Appearance Settings...</td><tr>\n";
			echo "</table>\n";
			unset($_POST);
			//Reset the config
			exec('sudo mount -o remount,rw /');                             // Make rootfs writable
			exec('sudo rm -rf /etc/pistar-css.ini');                        // Delete the Config
			echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;},2000);</script>';
			die();
		    }
		    else if (empty($_POST['cssDownload']) != TRUE)
		    {
			// Do nothing, handled in JS function
		    }
		    else if (empty($_POST['cssUpload']) != TRUE)
		    {
			echo "<tr><th colspan=\"2\">CCS Configuration Restore</th></tr>\n";
			
			if (isset($_FILES['cssFile']) && $_FILES['cssFile']['error'] === UPLOAD_ERR_OK)
			{
			    $output = "Uploading your appearance settings data.\n";
			    $target_dir = "/tmp/css_restore/";
			    $okay = false;
			    
			    shell_exec("sudo rm -rf $target_dir 2>&1");
			    shell_exec("mkdir $target_dir 2>&1");
			    
			    if($_FILES["cssFile"]["name"]) {
				$filename = $_FILES["cssFile"]["name"];
	  			$source = $_FILES["cssFile"]["tmp_name"];
				$type = $_FILES["cssFile"]["type"];
				
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
			    
			    if ($okay == false || $continue == false) {
				$output .= "The file you are trying to upload is not a .zip file. Please try again.\n";
				die();
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
				
				$output .= "Your .zip file was uploaded and unpacked.\n";

				// Make the disk Writable
				shell_exec('sudo mount -o remount,rw / 2>&1');
				
				$output .= "Copying appearance setttings...\n";
				$output .= shell_exec("sudo mv -v -f /tmp/css_restore/pistar-css.ini /etc/ 2>&1")."\n";

				// Make the disk Read-Only
				
				// Complete
				$output .= "Appearance Restoration Complete.\n";
				
				echo '<script type="text/javascript">setTimeout(function() { window.location=window.location;}, 4000);</script>';
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
			    echo "<tr><td align=\"left\"><pre>$output</pre></td></tr>\n";
			}
			die();
		    }
		    else
		    {
			//update ini file, call function
			update_ini_file($data, $filepath);
		    }
		}

		//this is the function going to update your ini file
		function update_ini_file($data, $filepath) {
		    $content = "";

    		    // Read the INI file contents
    		    $ini_content = file_get_contents($filepath);
    		    // Set the INI scanner option to treat values as literal strings
    		    $parsed_ini = parse_ini_string($ini_content, true, INI_SCANNER_RAW); 
		    
		    foreach($data as $section=>$values) {
			// UnBreak special cases
			$section = str_replace("_", " ", $section);
			$content .= "[".$section."]\n";
			//append the values
			foreach($values as $key=>$value) {
			    if ($value == '') {
				$content .= $key."=none\n";
			    }
			    else {
				$content .= $key."=".$value."\n";
			    }
			}
			$content .= "\n";
		    }
		    
		    //write it into file
		    if (!$handle = fopen($filepath, 'w')) {
			return false;
		    }
		    
		    $success = fwrite($handle, $content);
		    fclose($handle);
		    
		    // Updates complete - copy the working file back to the proper location
		    exec('sudo mount -o remount,rw /');                             // Make rootfs writable
		    exec('sudo cp /tmp/bW1kd4jg6b3N0DQo.tmp /etc/pistar-css.ini');  // Move the file back
		    exec('sudo chmod 644 /etc/pistar-css.ini');                     // Set the correct runtime permissions
		    exec('sudo chown root:root /etc/pistar-css.ini');               // Set the owner
		    
		    return $success;
		}
		
		//parse the ini file using default parse_ini_file() PHP function
		$parsed_ini = parse_ini_file($filepath, true);
?>

	<h2 class="ConfSec">Callsign Link Provider</h2>	

	<table>
	  <tr>
	    <td>
	      <form method="post" action="" class="left">
		<input type="radio" name="CallProvider" value="RadioID" id="RadioID" <?php if ($_SESSION['PiStarRelease']['Pi-Star']['CallLookupProvider'] == "RadioID") {  echo 'checked="checked"'; } ?> />
		<label for="RadioID">RadioID</label>
		&nbsp;
		<input type="radio" name="CallProvider" value="QRZ" id="QRZ" <?php if ($_SESSION['PiStarRelease']['Pi-Star']['CallLookupProvider'] == "QRZ") {  echo 'checked="checked"'; } ?> />
		<label for="QRZ">QRZ</label>
		&nbsp;
		<input name="CallLookupProvider" type="submit" value="Apply Change" />
	      </form>
	    </td>
	  </tr>
	</table>

	<br />
	
	<h2 class="ConfSec">Appearance and Extra Look/Feel Settings</h2>	

<?php
		echo '<form action="" method="post" name="edit-css">'."\n";

		// Colorpicker
		echo '<div style="position: fixed; pointer-events: none; transform: translateX(230%);" >'."\n";
		echo '<div id="colorpicker" style="float: right; margin: 20px; pointer-events: auto;"></div>'."\n";
		echo '</div>'."\n";
		
		foreach($parsed_ini as $section=>$values) {
		    // keep the section as hidden text so we can update once the form submitted
		    echo "<input type=\"hidden\" value=\"$section\" name=\"$section\" />\n";
		    echo "<table>\n";
		    echo "<tr><th class='larger' colspan=\"3\">$section</th></tr>\n";
		    // print all other values as input fields, so can edit. 
		    // note the name='' attribute it has both section and key
		    foreach($values as $key=>$value) {
			    if (endsWith($key, 'SectionColor')) {
			        echo "<tr><td align=\"right\" style='padding-left:10em;width:150px;'>$key</td><td align=\"left\"><input type=\"text\" class=\"colorwell\" name=\"{$section}[$key]\" value=\"$value\" /></td><td align='left' style='word-wrap: break-word;white-space: normal;'>(the small section heading font color; default is \"#000000\" [black].)</td></tr>\n";
			    } elseif ($key == 'TextColor') {
			        echo "<tr><td align=\"right\" style='padding-left:10em;width:150px;'>$key</td><td align=\"left\"><input type=\"text\" class=\"colorwell\" name=\"{$section}[$key]\" value=\"$value\" /></td><td align='left' style='word-wrap: break-word;white-space: normal;'>(the Main Content font color, used across most of the Dashboard's informational/data text; default is \"#000000\" [black].)</td></tr>\n";
			    } elseif (endsWith($key, 'Color')) { 
			        echo "<tr><td align=\"right\" style='padding-left:10em;width:150px;'>$key</td><td align=\"left\" colspan='2'><input type=\"text\" class=\"colorwell\" name=\"{$section}[$key]\" value=\"$value\" /></td></tr>\n";
			    } elseif (startsWith($key, 'MainFontSize')) {
			        echo "<tr><td align=\"right\" style='padding-left:10em;width:150px;'>$key</td><td align=\"left\"><input type=\"text\" name=\"{$section}[$key]\" value=\"$value\" size='3' maxlength='2' /></td><td align='left' style='word-wrap: break-word;white-space: normal;'>(the Main Content font size, in pixels, used across most of the Dashboard's informational/data text; default is 18 pixels.)</td></tr>\n";
			    } elseif (startsWith($key, 'BodyFontSize')) {
			        echo "<tr><td align=\"right\" style='padding-left:10em;width:150px;'>$key</td><td align=\"left\"><input type=\"text\" name=\"{$section}[$key]\" value=\"$value\" size='3' maxlength='2' /></td><td align='left' style='word-wrap: break-word;white-space: normal;'>(the Body font size, in pixels, used across most of the Dashboard's non-data/non-informational text; default is 17 pixels.)</td></tr>\n";
			    } elseif (startsWith($key, 'HeaderFont')) {
			        echo "<tr><td align=\"right\" style='padding-left:10em;width:150px;'>$key</td><td align=\"left\"><input type=\"text\" name=\"{$section}[$key]\" value=\"$value\" size='3' maxlength='2' /></td><td align='left' style='word-wrap: break-word;white-space: normal;'>(the Header font size, in pixels; default is 34 pixels.)</td></tr>\n";
			    } elseif (endsWith($key, 'HeardRows')) {
			        echo "<tr><td align=\"right\" style='padding-left:15em;width:150px;'>$key</td><td align=\"left\"><input type=\"text\" name=\"{$section}[$key]\" value=\"$value\" size='3' maxlength='3' /></td><td align='left' style='word-wrap: break-word;white-space: normal;'>(The number of rows displayed on the Dashboard; default is 40 rows, and 100 rows is the maximum allowed.*)</td></tr>\n";
		            } else {
			        echo "<tr><td align=\"right\" style='padding-left:15em;width:150px;'>$key</td><td align=\"left\" colspan='2'><input type=\"text\" name=\"{$section}[$key]\" value=\"$value\" /></td></tr>\n";
                }
		    }
		    echo "</table>\n";
                    echo '<br /><input type="submit" value="'.__( 'Apply Changes' ).'" />'."\n";
                    echo "<br />\n";
                    echo "<br />\n";
        }
		echo "</form>\n";
		echo "<p> * Because of the way WPSD parses log files to display last heard data, it is not guaranteed that the number of rows specified will be displayed.</p>\n";
		echo "<hr />\n";
		echo '<form id="cssUpload" action="" method="POST" enctype="multipart/form-data">'."\n";
		echo '  <div><input id="fileid" name="cssFile" type="file" hidden/></div>'."\n";
		echo '  <div><input type="hidden" name="cssUpload" value="1" /></div>'."\n";
		echo '</form>'."\n";
		echo '<form id="cssReset" action="" method="POST">'."\n";
		echo '  <div><input type="hidden" name="cssReset" value="1" /></div>'."\n";
		echo '</form>'."\n";
		echo '<input type="button" onclick="javascript:cssDownload();" value="Download Appearance Settings" />'."\n";
		echo '<input type="button" onclick="javascript:cssUpload();" value="Upload &amp; Apply Appearance Settings" />'."\n";
		echo '<input style="background:crimson;color:white;" type="button" onclick="javascript:cssReset();" value="Reset Appearance Settings" />'."\n";
		echo "<br />\n";
		echo "<br />\n";
		?>
	    </div>
	    
	    <div class="footer">
		<a href="https://wpsd.radio/">WPSD</a> &copy; <code>W0CHP</code> 2020-<?php echo date("Y"); ?><br />
	    </div>
	    
	</div>
    </body>
</html>
