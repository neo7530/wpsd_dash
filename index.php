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

$MYCALL = strtoupper($callsign);
$_SESSION['MYCALL'] = $MYCALL;

// Clear session data (page {re}load);
unset($_SESSION['BMAPIKey']);
unset($_SESSION['DAPNETAPIKeyConfigs']);
unset($_SESSION['PiStarRelease']);
unset($_SESSION['MMDVMHostConfigs']);
unset($_SESSION['ircDDBConfigs']);
unset($_SESSION['timeServerConfigs']);
unset($_SESSION['DStarRepeaterConfigs']);
unset($_SESSION['DMRGatewayConfigs']);
unset($_SESSION['YSFGatewayConfigs']);
unset($_SESSION['DGIdGatewayConfigs']);
unset($_SESSION['DAPNETGatewayConfigs']);
unset($_SESSION['YSF2DMRConfigs']);
unset($_SESSION['YSF2NXDNConfigs']);
unset($_SESSION['YSF2P25Configs']);
unset($_SESSION['DMR2YSFConfigs']);
unset($_SESSION['DMR2NXDNConfigs']);
unset($_SESSION['APRSGatewayConfigs']);
unset($_SESSION['NXDNGatewayConfigs']);
unset($_SESSION['P25GatewayConfigs']);
unset($_SESSION['CSSConfigs']);
unset($_SESSION['DvModemFWVersion']);
unset($_SESSION['DvModemTCXOFreq']);
unset($_SESSION['M17GatewayConfigs']);
unset($_SESSION['ModemConfigs']);

checkSessionValidity();

if (isset($_SESSION['CSSConfigs']['Text'])) {
    $textSections = $_SESSION['CSSConfigs']['Text']['TextSectionColor'];
}
if(empty($_GET['func'])) {
    $_GET['func'] = "main";
}
if(empty($_POST['func'])) {
    $_POST['func'] = "main";
}

// Vendor-specific funcs
// ZUMradio image init:
$iniFile = '/etc/dstar-radio.mmdvmhost';
$section = 'ZUM';
$key = 'NewInstall';
$expectedValue = '1';
$iniData = parse_ini_file($iniFile, true);
$isNewZumInstall = isset($iniData[$section][$key]) && $iniData[$section][$key] === $expectedValue;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
    <head>
	<meta name="language" content="English" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
	<link rel="shortcut icon" href="/images/favicon.ico?version=<?php echo $versionCmd; ?>" type="image/x-icon" />
	<title><?php echo "$MYCALL"." - " . __( 'Digital Voice' ) . " ".__( 'Dashboard' );?></title>
	<link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css?version=<?php echo $versionCmd; ?>" />
	<?php include_once "config/browserdetect.php"; ?>
	<script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
	<script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>
	<script type="text/javascript">
	 $.ajaxSetup({ cache: false });
	</script>
        <link href="/js/select2/css/select2.min.css?version=<?php echo $versionCmd; ?>" rel="stylesheet" />
        <script src="/js/select2/js/select2.full.min.js?version=<?php echo $versionCmd; ?>"></script>
        <script src="/js/select2/js/select2-searchInputPlaceholder.js?version=<?php echo $versionCmd; ?>"></script>
        <script type="text/javascript">
          $(document).ready(function() {
            $('.ysfLinkHost').select2({searchInputPlaceholder: 'Search...'});
            $('.p25LinkHost').select2({searchInputPlaceholder: 'Search...'});
            $('.nxdnLinkHost').select2({searchInputPlaceholder: 'Search...'});
            $(".RefName").select2({
              tags: true,
              width: '125px',
              dropdownAutoWidth : false,
              createTag: function (params) {
                return {
                  id: params.term,
                  text: params.term,
                  newOption: true
                }
              },
              templateResult: function (data) {
                var $result = $("<span></span>");

                $result.text(data.text);

                if (data.newOption) {
                  $result.append(" <em>(Search existing, or enter and save custom reflector value.)</em>");
                }

                return $result;
              }
            });
            $('.dmrMasterHost3').select2();
            $('.dmrMasterHost3Startup').select2({searchInputPlaceholder: 'Search...', width: '125px'});
            $('.ModSel').select2();
            $('.M17Ref').select2({searchInputPlaceholder: 'Search...', width: '125px'});
          });
          $(document).ready(function() {
            $('.menuhwinfo').click(function() {
              $(".hw_toggle").slideToggle(function() {
                localStorage.setItem('hwinfo_visible', $(this).is(":visible"));
              })
            });
            $('.hw_toggle').toggle(localStorage.getItem('hwinfo_visible') === 'true');
          });
    function clear_activity() {
      if ( 'true' === localStorage.getItem('filter_activity') ) {
        max = localStorage.getItem( 'filter_activity_max') || 1;
        jQuery('.filter-activity-max').attr('value',max);
        jQuery('.activity-duration').each( function(i,el) {
          duration = parseFloat( jQuery(this).text() );
          if ( duration < max ) {
            jQuery(this).closest('tr').hide();
          } else {
            jQuery(this).closest('tr').addClass('good-activity');
          }
        });
        

        jQuery('.good-activity').each( function( i,el ) {
          if (i % 2 === 0) {
          /* we are even */
          jQuery(el).addClass('even');
        } else {
          jQuery(el).addClass('odd');
        }
        });
      }
    };
    function setFilterActivity(obj) {
      localStorage.setItem('filter_activity', obj.checked);
      $.ajax({
        type: "POST",
        url: '/mmdvmhost/filteractivity_ajax.php',
        data:{
          action: obj.checked
        },
      });
    }
    function setFilterActivityMax(obj) {
      max = obj.value || 1;
      localStorage.setItem('filter_activity_max', obj.value);
    }

    function reloadUpdateCheck(){
      $("#CheckUpdate").load("/includes/checkupdates.php",function(){
        setTimeout(reloadUpdateCheck,10000) });
    }
    setTimeout(reloadUpdateCheck,10000);

    function reloadMessageCheck(){
      $("#CheckMessage").load("/includes/messages.php",function(){
        setTimeout(reloadMessageCheck,300000) });
    }
    setTimeout(reloadMessageCheck,300000);

    function reloadDateTime(){
      $("#DateTime").load("/includes/datetime.php",function(){
        setTimeout(reloadDateTime,1000) });
    }
    setTimeout(reloadDateTime,1000);

    </script>
<script>
  function executeBackgroundTasks() {
    $.ajax({
      url: '/includes/execute-background-tasks.php',
      success: function(data) {
        console.log('Background tasks executed successfully.');
      },
      error: function() {
        console.log('Error executing background tasks.');
      }
    });
  }

  $(document).ready(function() {
    setInterval(function() {
      executeBackgroundTasks();
    }, 300000); // 5 mins
  });
</script>
<?php if ( '/index.php' === $_SERVER["PHP_SELF"] ) : ?>
  <script>
    document.addEventListener('keydown', function(event) {
      if ( event.key === 'S' || event.keyCode === 83 ) {
        window.location.href = '/mmdvmhost/export-lh.php';
      }
    });
  </script>
<?php endif; ?>
    </head>
   <body>
	<div class="container">
	    <div class="header">
               <div class="SmallHeader shLeft noMob"><a style="border-bottom: 1px dotted;" class="tooltip" href="#"><?php echo __( 'Hostname' ).": ";?> <span><strong>System IP Address<br /></strong><?php echo str_replace(',', ',<br />', exec('hostname -I'));?> </span>  <?php echo exec('cat /etc/hostname'); ?></a></div>
		<div class="SmallHeader shRight noMob">
		<div id="CheckUpdate">
		<?php
		    include('includes/checkupdates.php');
		?>
		</div><br />
		</div>

		<h1>WPSD <?php echo __( 'Digital Voice' ) . " ".__( 'Dashboard for' )." <code style='font-weight:550;'>".$_SESSION['MYCALL']."</code>"; ?></h1>
		<div id="CheckMessage">
		<?php
		    include('includes/messages.php');
		?>
		</div>

 		<div class="navbar">
		  <div class="headerClock">
		    <span id="DateTime"></span>
		  </div>
		  <?php if ($_SERVER["PHP_SELF"] == "/admin/index.php") {
		      echo ' <a class="menuconfig" href="/admin/configure.php">'.__( 'Configuration' ).'</a>';
		      echo ' <a class="menuupdate noMob" href="/admin/update.php">'.__( 'WPSD Update' ).'</a>'."\n";
		      echo ' <a class="menuadvanced noMob" href="/admin/advanced/">Advanced</a>'."\n";
		      echo ' <a class="menupower" href="/admin/power.php">'.__( 'Power' ).'</a>'."\n";
		      echo ' <a class="menusysinfo noMob" href="/admin/sysinfo.php">System Details</a>'."\n";
		      echo ' <a class="menulogs noMob" href="/admin/live_log.php">'.__( 'Log Viewer' ).'</a>'."\n";
		      echo ' <a class="menuappearance noMob" href="/admin/appearance.php">Appearance</a>'."\n";
		      echo ' <a class="menudashboard" href="/">'.__( 'Dashboard' ).'</a>'."\n";
		    }
                    if ($_SERVER["PHP_SELF"] !== "/admin/index.php") {
                        echo '<a class="menuadmin" href="/admin/">'.__( 'Admin' ).'</a>'."\n";
		    	echo '<a class="menuhwinfo noMob" href="#">SysInfo</a>';
		        echo '<a class="menusimple noMob" href="/simple/">Simple View</a>';
		    	echo '<a class="menulive" href="/live/">Live Caller</a>';
			// DV-Mega Cast menu item only
			if (isDVmegaCast() == 1) {
		    	    echo '<a class="menucastmemory noMob" href="/admin/cast/memory-list/">Cast Memory</a>';
			}
                        echo '<a class="menuprofile noMob" href="/admin/profile_manager.php">Profiles</a>'."\n";
                    } ?>
	    	</div>
		</div>

	    <?php
	    // Check if config files need updating but supress if new installation
	    if (($_SERVER["PHP_SELF"] == "/admin/index.php") || ($_SERVER["PHP_SELF"] == "/index.php")) {
		$configUpNeeded = $_SESSION['PiStarRelease']['Pi-Star']['ConfUpdReqd'];
                if (!isset($configUpNeeded) || ($configUpNeeded < $configUpdateRequired)) {	
		    $fileList = array_filter(array("/etc/dstar-radio.mmdvmhost", "/etc/dstar-radio.dstarrepeater"), 'file_exists');
		    if ($file = array_shift($fileList)) {
	    ?>
		<div>
		    <div style="background-color: #FFCC00; color: #000;text-align:center; padding:10px 0; margin: 0px 0px 10px 0px; width: 100%;">
				<p>
				<b>IMPORTANT</b><br />
				<br />
				Your configuration needs to be updated.<br />
				Go to the <a href="/admin/configure.php" style="text-decoration:underline;font-weight:bold;">Configuration Page</a> and click on any "Apply Changes" button.<br />
				<br />This message will disappear once this has been completed.<br />
				</p>
		    </div>
		</div>
	    <?php }
	        }
	    }
	    ?>
	    <?php
            // Output some default features
            if ($_SERVER["PHP_SELF"] == "/index.php" || $_SERVER["PHP_SELF"] == "/admin/index.php")
            {
                    echo '<div class="contentwide">'."\n";
                    echo '<script type="text/javascript">'."\n";
                    echo 'function reloadHwInfo(){'."\n";
                    echo '  $("#hwInfo").load("/includes/hw_info.php",function(){ setTimeout(reloadHwInfo, 30000) });'."\n";
                    echo '}'."\n";
                    echo 'setTimeout(reloadHwInfo, 30000);'."\n";
                    echo '</script>'."\n";
                    echo '<script type="text/javascript">'."\n";
                    echo 'function reloadRadioInfo(){'."\n";
                    echo '  $("#radioInfo").load("/mmdvmhost/radioinfo.php",function(){ setTimeout(reloadRadioInfo, 1000) });'."\n";
                    echo '}'."\n";
                    echo 'setTimeout(reloadRadioInfo, 1000);'."\n";
                    echo '</script>'."\n";
                    echo "<div id='hw_info' class='hw_toggle'>\n";
                    echo '<div id="hwInfo">'."\n";
                    include 'includes/hw_info.php';
                    echo '</div>'."\n";
                    echo '</div>'."\n";
                    echo '<div id="radioInfo">'."\n";
                    include 'mmdvmhost/radioinfo.php';
                    echo '</div>'."\n";
                    echo '<br class="noMob" />'."\n";
                    echo '</div>'."\n";
            }

	// New ZUMspot image?
	    if ($isNewZumInstall) {
		echo '<div class="contentwide">'."\n";
		echo "<h1>New ZUMspot Installation...</h1>\n";
		echo "<p>You will be redirected to the configuration page in 10 seconds to setup your ZUMspot...</p>\n";
		echo '<script type="text/javascript">setTimeout(function() { window.location="/admin/configure.php";},10000);</script>'."\n";
            } else if (file_exists('/etc/dstar-radio.dstarrepeater')) { //dstarrepeater migration
		echo '<div class="contentwide">'."\n";
		echo "<h1>NOTE: Migration Required...</h1>\n";
		echo "<p>DSTARrepeater mode is unsupported and has been removed. You will need to re-configure using the (now default) MMDVMHost controller mode.</p>\n";
		echo "<p>You will be redirected to the configuration page in 30 seconds to setup your installation... Or <a href='admin/configure.php'>configure now...</a></p>\n";
		echo '<script type="text/javascript">setTimeout(function() { window.location="/admin/configure.php";},30000);</script>'."\n";

	    } else if (file_exists('/etc/dstar-radio.mmdvmhost')) {
		echo '<div class="nav">'."\n";					// Start the Side Menu
		echo '<script type="text/javascript">'."\n";
		echo 'function reloadRepeaterInfo(){'."\n";
		echo '  $("#repeaterInfo").load("/mmdvmhost/repeaterinfo.php",function(){ setTimeout(reloadRepeaterInfo,5000) });'."\n";
		echo '}'."\n";
		echo 'setTimeout(reloadRepeaterInfo,5000);'."\n";
		echo '</script>'."\n";
		echo '<div id="repeaterInfo">'."\n";
		include 'mmdvmhost/repeaterinfo.php';				// MMDVMDash Repeater Info
		echo '</div>'."\n";
		echo '</div>'."\n";
		
		echo '<div class="content">'."\n";

		// menu/selection set:
    		// BM  / DMRGwcheck: Get the current DMR Master from the config
    		$dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
    		if ( $dmrMasterHost == '127.0.0.1' ) {
        		$dmrMasterHost = $_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'];
        		$bmEnabled = ($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Enabled'] != "0" ? true : false);
    		}
		elseif (preg_match("/brandmeister.network/",$dmrMasterHost))
		{
			$bmEnabled = true;
		}
    		// Make sure the master is a BrandMeister Master
    		if (($dmrMasterFile = fopen("/usr/local/etc/DMR_Hosts.txt", "r")) != FALSE) {
        		while (!feof($dmrMasterFile)) {
            		$dmrMasterLine = fgets($dmrMasterFile);
            		$dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
            		if ((strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
                		if ($dmrMasterHost == $dmrMasterHostF[2]) { $dmrMasterHost = str_replace('_', ' ', $dmrMasterHostF[0]); }
            		}
        	    }
        	    fclose($dmrMasterFile);
    		} // end BM check
			// tgif check:
			if ( $testMMDVModeDMR == 1 ) {
		    	// Get the current DMR Master from the config
		    	$dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
		    	if ( $dmrMasterHost == '127.0.0.1' ) {
				// DMRGateway, need to check each config
				if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'])) {
			    	if (($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 1']['Enabled'])) {
						$tgifEnabled = true;
			    	}
				}
				if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Address'])) {
			    	if (($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 2']['Enabled'])) {
						$tgifEnabled = true;
			    	}
				}
				if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Address'])) {
			    	if (($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 3']['Enabled'])) {
						$tgifEnabled = true;
			    	}
				}
				if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Address'])) {
			    	if (($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 4']['Enabled'])) {
						$tgifEnabled = true;
			    	}
				}
				if (isset($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Address'])) {
			    	if (($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Address'] == "tgif.network") && ($_SESSION['DMRGatewayConfigs']['DMR Network 5']['Enabled'])) {
						$tgifEnabled = true;
			    	}
				}
    		}
			// DMRmaster Connected directly to TGIF
    		else if ( $dmrMasterHost == 'tgif.network' ) {
				$tgifEnabled = true;
			}
        } // end tgif check

		$testMMDVModeDSTARnet = getConfigItem("D-Star", "Enable", $_SESSION['MMDVMHostConfigs']);
		if ( $testMMDVModeDSTARnet == 1 ) {	// If D-Star network is enabled, add these extra features.
		    
		    if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "ds_man" || $_GET["func"] == "ds_man") {	// Admin Only Option (D-Star Mgr)
			echo '<script type="text/javascript">'."\n";
			echo 'function reloadrefLinks(){'."\n";
			echo '  $("#refLinks").load("/mmdvmhost/dstar_reflector_links.php",function(){ setTimeout(reloadrefLinks,5000) });'."\n";
			echo '}'."\n";
			echo 'setTimeout(reloadrefLinks,5000);'."\n";
			echo '</script>'."\n";
			echo '<div id="refLinks">'."\n";
			include 'mmdvmhost/dstar_reflector_links.php';
			echo '</div>'."\n";
			include 'mmdvmhost/dstar_link_manager.php';		// D-Star Link Manager
			echo '<script type="text/javascript">'."\n";
			echo 'function reloadccsConnections(){'."\n";
			echo '  $("#ccsConnects").load("/mmdvmhost/dstar_ccs_connections.php",function(){ setTimeout(reloadccsConnections,15000) });'."\n";
			echo '}'."\n";
			echo 'setTimeout(reloadccsConnections,15000);'."\n";
			echo '</script>'."\n";
			echo '<div id="ccsConnects">'."\n";
			include 'mmdvmhost/dstar_ccs_connections.php';
			echo '</div>'."\n";
		    }
		}
		if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "mode_man" || $_GET["func"] == "mode_man") {	// Admin Only Option (instant mode mgr)	
                    include "admin/instant-mode-manager.php";
		}

		if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "sys_man" || $_GET["func"] == "sys_man") {	// Admin Only Option (system mgr)	
                    include "admin/system-manager.php";
		}

		if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "bm_man" || $_GET["func"] == "bm_man") { 		// Admin Only Option (BM manager )
		    $bmAPIkeyFile = '/etc/bmapi.key';
		    if (!file_exists($bmAPIkeyFile)) { // no API key file at all; warn, instruct  and bail.
		    ?>
			<div>
			  <table align="center"style="margin: 0px 0px 10px 0px; width: 100%;border-collapse:collapse; table-layout:fixed;white-space: normal!important;">
			    <tr>
				<td align="center" valign="top" style="background-color: #ffff90; color: crimson; word-wrap: break-all;padding:20px;">Notice! You do not have a BrandMeister API key defined! Read the announcement on how create one: <a href="https://news.brandmeister.network/introducing-user-api-keys/" target="new" alt="BM API Keys">BM API Key Announcement and Migration Instructions</a>; and then <a href="/admin/advanced/fulledit_bmapikey.php">Enter your API Key</a> to enable this page.</td>
			    </tr>
			  </table>
			</div>
		    <?php
		    } else if (file_exists($bmAPIkeyFile) && fopen($bmAPIkeyFile,'r')) { // yay we have am API key file
			$configBMapi = parse_ini_file($bmAPIkeyFile, true);
			$bmAPIkey = $configBMapi['key']['apikey'];
			// Check the BM API Key
			if ( strlen($bmAPIkey) <= 20 ) { // malformed api key file/contents; warn, instruct and bail!
		     ?>
			<div>
			  <table align="center"style="margin: 0px 0px 10px 0px; width: 100%;border-collapse:collapse; table-layout:fixed;white-space: normal!important;">
			    <tr>
				<td align="center" valign="top" style="background-color: #ffff90; color: crimson; word-wrap: break-all;padding:20px;">Notice! You do not have a BrandMeister API key defined! Read the announcement on how create one: <a href="https://news.brandmeister.network/introducing-user-api-keys/" target="new" alt="BM API Keys">BM API Key Announcement and Migration Instructions</a>; and then <a href="/admin/advanced/fulledit_bmapikey.php">Enter your API Key</a>. to enable this page.</td>
			    </tr>
			  </table>
			</div>
		    <?php
		        } elseif ( strlen($bmAPIkey) <= 200 ) { // Legacy API v1 key; warn, instruct and bail
		    ?>
			   <div>
			     <table align="center"style="margin: 0px 0px 10px 0px; width: 100%;border-collapse:collapse; table-layout:fixed;white-space: normal!important;">
			       <tr>
			         <td align="center" valign="top" style="background-color: #ffff90; color: #906000; word-wrap: break-all;padding:20px;">Notice! You have a legacy Brandmeister API Key, which will not work any longer. Read the announcement on how to migrate: <a href="https://news.brandmeister.network/introducing-user-api-keys/" target="new" alt="BM API Keys">BM API Key Announcement and Migration Instructions</a>; and then <a href="/admin/advanced/fulledit_bmapikey.php">Update your API Key</a> to enable this page.</td>
			       </tr>
			     </table>
			   </div>
		    <?php
		    	} else { // Yay! good API key!
			    echo '<script type="text/javascript">'."\n";
        	    	    echo 'function reloadbmConnections(){'."\n";
        	    	    echo '  $("#bmConnects").load("/mmdvmhost/bm_links.php",function(){ setTimeout(reloadbmConnections,15000) });'."\n";
        	    	    echo '}'."\n";
        	    	    echo 'setTimeout(reloadbmConnections,15000);'."\n";
        	    	    echo '</script>'."\n";
        	    	    echo '<div id="bmConnects">'."\n";
		    	    include 'mmdvmhost/bm_links.php';                   // BM Links
		    	    echo '</div>'."\n";
		    	    include 'mmdvmhost/bm_manager.php';                 // BM DMR Link Manager
		        }
		    }
		}

		// Will re-enable if/when TGIF provides a public API for
		// their new (2022) platform - if they ever do FFS. It's
		// 9/23 now :(
		/*
		if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "tgif_man" || $_GET["func"] == "tgif_man") {	// Admin Only Option (tgif links)
		    echo '<script type="text/javascript">'."\n";
        	    echo 'function reloadtgifConnections(){'."\n";
        	    echo '  $("#tgifConnects").load("/mmdvmhost/tgif_links.php",function(){ setTimeout(reloadtgifConnections,15000) });'."\n";
        	    echo '}'."\n";
        	    echo 'setTimeout(reloadtgifConnections,15000);'."\n";
        	    echo '</script>'."\n";
        	    echo '<div id="tgifConnects">'."\n";
		    include 'mmdvmhost/tgif_links.php';			// TGIF Links
		    echo '</div>'."\n";
		}
		*/

		if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "tgif_man" || $_GET["func"] == "tgif_man") {	// Admin Only Options (tgif mgr)
        	include 'mmdvmhost/tgif_manager.php';			// TGIF DMR Link Manager
		}
		
		$testMMDVModeYSF = getConfigItem("System Fusion", "Enable", $_SESSION['MMDVMHostConfigs']);
		$testDMR2YSF = $_SESSION['DMR2YSFConfigs']['Enabled']['Enabled'];
		if ($testMMDVModeYSF == 1 || $testDMR2YSF == 1) {
		    if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "ysf_man" || $_GET["func"] == "ysf_man") { 	// Admin Only Option
				include 'mmdvmhost/ysf_manager.php';		// YSF Links
		    }
		}
		$testMMDVModeP25net = getConfigItem("P25 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
                $testYSF2P25 = $_SESSION['YSF2P25Configs']['Enabled']['Enabled'];
		if ( $testMMDVModeP25net == 1 || $testYSF2P25 == 1) {				// If P25 network is enabled, add these extra features.
		    if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "p25_man" || $_GET["func"] == "p25_man") { 	// Admin Only Option *p25 mgr)
				include 'mmdvmhost/p25_manager.php';		// P25 Links
		    }
		}
		$testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
		$testDMR2NXDN = $_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'];
		$testYSF2NXDN = $_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'];
		if ( $testMMDVModeNXDN == 1 || $testDMR2NXDN == 1 || $testYSF2NXDN == 1 ) {
		    if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "nxdn_man" || $_GET["func"] == "nxdn_man") { 	// Admin Only Option (nxdn mgr)
				include 'mmdvmhost/nxdn_manager.php';		// NXDN Links
		    }
		}
		$testMMDVModeM17net = getConfigItem("M17 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
		if ( $testMMDVModeM17net == 1 ) {				// If M17 network is enabled, add these extra features.
		    if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "m17_man" || $_GET["func"] == "m17_man") { 	// Admin Only Option
			include 'mmdvmhost/m17_manager.php';		// M17 Links
		    }
		}
                $dmrMasterHost = getConfigItem("DMR Network", "Address", $_SESSION['MMDVMHostConfigs']);
                if ( $dmrMasterHost == '127.0.0.1') {
		    if ($testMMDVModeDMR == 1) {
			if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "dmr_man" || $_GET["func"] == "dmr_man") { 	// Admin Only Option
			    include 'mmdvmhost/dmr_manager.php';		// DMR Manager
			}
		    }
		}
		$testMMDVModePOCSAG = getConfigItem("POCSAG", "Enable", $_SESSION['MMDVMHostConfigs']);
		if ( $testMMDVModePOCSAG == 1 ) {
		    if ($_SERVER["PHP_SELF"] == "/admin/index.php" && $_POST["func"] == "pocsag_man" || $_GET["func"] == "pocsag_man") {  // Admin Only Options (pocsag mgr)
			    echo '<div id="dapnetMsgr">'."\n";
			    include 'mmdvmhost/dapnet_messenger.php';
			    echo '</div>'."\n";
		    }
        	}

		// begin admin selection form
		if ($_SERVER["PHP_SELF"] == "/admin/index.php") {
		    if ($_GET['func'] != 'main') { echo '<br />'; }
                    echo '<h3 class="larger" style="text-align:left;font-weight:bold;margin:-5px 0 2px 0;">Select an Admin Section/Page:</h3><br />'."\n";
		    echo '<form method="get" id="admin_sel" name="admin_sel" action="/admin/" style="padding-bottom:10px;">'."\n";
		    echo '      <div class="mode_flex">'."\n";
		    echo '        <div class="mode_flex row">'."\n";
		    echo '          <div class="mode_flex column">'."\n";
 		    echo '            <button form="admin_sel" type="submit" value="main" name="func"><span>Admin Main Page</span></button>'."\n";
		    echo '          </div><div class="mode_flex column">'."\n";
                    $testMMDVModeDSTARnet = getConfigItem("D-Star", "Enable", $_SESSION['MMDVMHostConfigs']);
                    if ( $testMMDVModeDSTARnet == 1 && !isPaused("D-Star") ) {
                        echo '		<button form="admin_sel" type="submit" value="ds_man" name="func"><span>D-Star Manager</span></button>'."\n";
                    }
                    else {
                        echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="ds_man" name="func"><span>D-Star Manager</span></button>'."\n";
                    } 
		    echo '          </div><div class="mode_flex column">'."\n";
                    $testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);
		    if ($bmEnabled == true && $testMMDVModeDMR ==1) {
		        echo '		<button form="admin_sel" type="submit" value="bm_man" name="func"><span>BrandMeister Manager</span></button>'."\n";
		    }
		    else {
			echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="bm_man" name="func"><span>BrandMeister Manager</span></button>'."\n";
		    }
		    echo '          </div><div class="mode_flex column">'."\n";
                   if ( isset( $tgifEnabled ) && $tgifEnabled == 1 && $testMMDVModeDMR == 1 ) {
		        echo '		<button form="admin_sel" type="submit" value="tgif_man" name="func"><span>TGIF Manager</span></button>'."\n";
		    }
		    else {
			echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="tgif_man" name="func"><span>TGIF Manager</span></button>'."\n";
		    }
		    echo '          </div><div class="mode_flex column">'."\n";
        	    $testMMDVModeYSF = getConfigItem("System Fusion", "Enable", $_SESSION['MMDVMHostConfigs']);
        	    $testDMR2YSF = $_SESSION['DMR2YSFConfigs']['Enabled']['Enabled'];
        	    if ($testMMDVModeYSF == 1 || $testDMR2YSF == 1) {
		        echo '		<button form="admin_sel" type="submit" value="ysf_man" name="func"><span>YSF Manager</span></button>'."\n";
		    }
		    else {
		        echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="ysf_man" name="func"><span>YSF Manager</span></button>'."\n";
		    }
		    echo '          </div><div class="mode_flex column">'."\n";
                    $testMMDVModeDMR = getConfigItem("DMR", "Enable", $_SESSION['MMDVMHostConfigs']);
                    if ($testMMDVModeDMR ==1) {
                        echo '          <button form="admin_sel" type="submit" value="dmr_man" name="func"><span>DMR Network Manager</span></button>'."\n";
                    }
                    else {
                        echo '          <button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="dmr_man" name="func"><span>DMR Network Manager</span></button>'."\n";
                    } 
		    echo '      </div></div>'."\n";
                    echo '        <div class="mode_flex row">'."\n";
		    if (isDVmegaCast() != 1) { // Being DVmega Cast logic 
			echo '          <div class="mode_flex column">'."\n";
			$testMMDVModeP25 = getConfigItem("P25", "Enable", $_SESSION['MMDVMHostConfigs']);
			$testYSF2P25 = $_SESSION['YSF2P25Configs']['Enabled']['Enabled'];
			if ( $testMMDVModeP25 == 1 || $testYSF2P25 == 1) {
			    echo '		<button form="admin_sel" type="submit" value="p25_man" name="func"><span>P25 Manager</span></button>'."\n";
			}
			else {
			    echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="p25_man" name="func"><span>P25 Manager</span></button>'."\n";
			}
			echo '          </div><div class="mode_flex column">'."\n";
			$testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $_SESSION['MMDVMHostConfigs']);
			$testDMR2NXDN = $_SESSION['DMR2NXDNConfigs']['Enabled']['Enabled'];
			$testYSF2NXDN = $_SESSION['YSF2NXDNConfigs']['Enabled']['Enabled'];
			if (($testMMDVModeNXDN == 1 || $testDMR2NXDN == 1 || $testYSF2NXDN == 1) && !isPaused("NXDN")) {
			    echo '		<button form="admin_sel" type="submit" value="nxdn_man" name="func"><span>NXDN Manager</span></button>'."\n";
			}
			else {
			    echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="nxdn_man" name="func"><span>NXDN Manager</span></button>'."\n";
			}
			echo '          </div><div class="mode_flex column">'."\n";
			$testMMDVModeM17 = getConfigItem("M17 Network", "Enable", $_SESSION['MMDVMHostConfigs']);
			if ($testMMDVModeM17 == 1 && !isPaused("M17")) {
			    echo '		<button form="admin_sel" type="submit" value="m17_man" name="func"><span>M17 Manager</span></button>'."\n";
			}
			else {
			    echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="m17_man" name="func"><span>M17 Manager</span></button>'."\n";
			}
			echo '          </div><div class="mode_flex column">'."\n";
			$testMMDVModePOCSAG = getConfigItem("POCSAG", "Enable", $_SESSION['MMDVMHostConfigs']);
			if ($testMMDVModePOCSAG == 1) {
			    echo '		<button form="admin_sel" type="submit" value="pocsag_man" name="func"><span>POCSAG Manager</span></button>'."\n";
			}
			else {
			    echo '		<button form="admin_sel" disabled="disabled" title="Mode is Disabled" type="submit" value="pocsag_man" name="func"><span>POCSAG Manager</span></button>'."\n";
			}
			echo '          </div><div class="mode_flex column">'."\n";
		    } else { 
			echo '          <div class="mode_flex column">'."\n";
		    }  // end DVmega Cast logic
 		    echo '            <button form="admin_sel" type="submit" value="mode_man" name="func"><span>Instant Mode Manager</span></button>'."\n";
		    echo '          </div><div class="mode_flex column">'."\n";
		    echo '		<button form="admin_sel" type="submit" value="sys_man" name="func"><span>System Manager</span></button>'."\n";
		    echo '      </div></div>'."\n".'</div>'."\n";
		    echo '      <div><br /><b>Note:</b> Modes/networks/services not <a href="/admin/configure.php" style="text-decoration:underline;color:inherit;">globally configured/enabled</a>, or that are paused, are not selectable here until they are enabled or <a href="./?func=mode_man" style="text-decoration:underline;color:inherit;">resumed from pause</a>.</div>'."\n";
		    echo ' </form>'."\n";
		    if ($_GET['func'] != "main" && $_GET['func'] != "pocsag_man") {
			echo "</div>\n";
		    }
		}

	if ($_SERVER["PHP_SELF"] == "/index.php" || $_SERVER["PHP_SELF"] == "/admin/index.php") {
		echo '<script type="text/javascript">'."\n";
        	echo 'function setLastCaller(obj) {'."\n";
        	echo '    if (obj.checked) {'."\n";
        	echo "        $.ajax({
				success: function(data) { 
     				    $('#lcmsg').html(data).fadeIn('slow');
				    $('#lcmsg').html(\"<div style='padding:8px;font-style:italic;font-weight:bold;'>For optimal performance, the number of Last Heard rows will be decreased while Caller Details function is enabled.</div>\").fadeIn('slow')
     				    $('#lcmsg').delay(4000).fadeOut('slow');
				},
                	        type: \"POST\",
  	          	        url: '/mmdvmhost/callerdetails_ajax.php',
                	        data:{action:'enable'},
         	             });";
	        echo '    }'."\n";
	        echo '    else {'."\n";
	        echo "        $.ajax({
				success: function(data) { 
     				    $('#lcmsg').html(data).fadeIn('slow');
				    $('#lcmsg').html(\"<div style='padding:8px;font-style:italic;font-weight:bold;'>Caller Details function disabled. Increasing Last Heard table rows to user preference (if set) or default (40).</div>\").fadeIn('slow')
     				    $('#lcmsg').delay(4000).fadeOut('slow');
				},
	                        type: \"POST\",
	                        url: '/mmdvmhost/callerdetails_ajax.php',
	                        data:{action:'disable'},
	                      });";
	        echo '    }'."\n";
	        echo '}'."\n";
    		echo '</script>'."\n";
		echo '<div id="lcmsg" style="background:#d6d6d6;color:black; margin:0 0 10px 0;"></div>'."\n";

		echo '<script>
		  async function fetchData(url, targetElement) {
		    try {
		      const response = await fetch(url);
		      const data = await response.text();
		      $(targetElement).html(data);
		    } catch (error) {
		      console.error(`Error fetching data from ${url}:`, error);
		    }
		  }

		  function reloadDynData() {
		    fetchData("/mmdvmhost/last_heard_table.php", "#lastHeard");
		    fetchData("/mmdvmhost/local_tx_table.php", "#localTxs");
		    fetchData("/mmdvmhost/caller_details_table.php", "#liveCallerDeets");
		  }

		  setInterval(reloadDynData, 1500);
		</script>';
 
		echo '<script>'."\n";
        	echo 'function setLHTGnames(obj) {'."\n";
        	echo '    if (obj.checked) {'."\n";
        	echo "        $.ajax({
                                success: function(data) { 
                                    $('#lcmsg').html(data).fadeIn('slow');
                                    $('#lcmsg').html(\"<div style='padding:8px;font-style:italic;font-weight:bold;'>Talkgroup Names display enabled: Please wait until data populated. For optimal performance, the number of Last Heard rows will be decreased while TG Names function is enabled.</div>\").fadeIn('slow')
                                    $('#lcmsg').delay(4000).fadeOut('slow');
                                },
                	        type: \"POST\",
  	          	        url: '/mmdvmhost/tgnames_ajax.php',
                	        data:{action:'enable'},
         	             });";
	        echo '    }'."\n";
	        echo '    else {'."\n";
	        echo "        $.ajax({
                                success: function(data) { 
                                    $('#lcmsg').html(data).fadeIn('slow');
                                    $('#lcmsg').html(\"<div style='padding:8px;font-style:italic;font-weight:bold;'>Talkgroup Names display disabled: Please wait until data is cleared. Increasing Last Heard table rows to user preference (if set) or default (40).</div>\").fadeIn('slow')
                                    $('#lcmsg').delay(4000).fadeOut('slow');
                                },
	                        type: \"POST\",
	                        url: '/mmdvmhost/tgnames_ajax.php',
	                        data:{action:'disable'},
	                      });";
	        echo '    }'."\n";
	        echo '}'."\n";
    		echo '</script>'."\n";

    }

	if ($_SERVER["PHP_SELF"] != "/admin/index.php") {
            echo '<div id="liveCallerDeets">'."\n";
            include 'mmdvmhost/caller_details_table.php';
            echo '</div>'."\n";

            if (!file_exists('/etc/.CALLERDETAILS')) {
                echo '<div id="lastHeard" style="margin-top:-20px;">'."\n";
            } else {
                echo '<div id="lastHeard">'."\n";
            }
            echo '</div>'."\n";

            echo '<div id="localTxs" style="margin-top: 20px;">'."\n";
            include 'mmdvmhost/local_tx_table.php';
            echo '</div>'."\n";
	}

	// If POCSAG is enabled, show the information panel
        if ( $testMMDVModePOCSAG == 1 ) {
            if (($_SERVER["PHP_SELF"] == "/index.php" || $_POST["func"] == "pocsag_man" || $_GET["func"] == "pocsag_man")) { // display pages in pocsag mgr or main dash page only with no other func requested
	            $myOrigin = ($_SERVER["PHP_SELF"] == "/admin/index.php" ? "admin" : "other");
		    
		    echo '<script type="text/javascript">'."\n";
		    echo 'var pagesto;'."\n";
		    echo 'function setPagesAutorefresh(obj) {'."\n";
	            echo '        pagesto = setTimeout(reloadPages, 10000, "?origin='.$myOrigin.'");'."\n";
		    echo '}'."\n";
		    echo 'function reloadPages(OptStr){'."\n";
		    echo '    $("#Pages").load("/mmdvmhost/pocsag_table.php"+OptStr, function(){ pagesto = setTimeout(reloadPages, 10000, "?origin='.$myOrigin.'") });'."\n";
		    echo '}'."\n";
		    echo 'pagesto = setTimeout(reloadPages, 10000, "?origin='.$myOrigin.'");'."\n";
		    echo '</script>'."\n";
		    echo "\n".'<div id="Pages">'."\n";
		    include 'mmdvmhost/pocsag_table.php';				// POCSAG Messages
		    echo '</div>'."\n";
		}
	    }
        }
	else {
	    echo '<div class="contentwide">'."\n";
	    // Instance not configured...
	    echo "<h1>New Installation...</h1>\n";
	    echo "<p>Your installation needs to be configured.</p>\n";
	    echo "<p>You will be redirected to the configuration page in 15 seconds...</p>\n";
	    echo '<script type="text/javascript">setTimeout(function() { window.location="/admin/configure.php";},15000);</script>'."\n";
	}
	?>
	</div>
	
	<div class="footer">
	   <?php 
		echo 'Get WPSD Help: [ <a href="https://w0chp.radio/wpsd-faqs/" target="_new">FAQs</a> ] &bull; [ <a href="https://wpsd-docs.w0chp.net/" target="_new">User Manual</a> ] &bull; [ <a href="https://www.facebook.com/groups/wpsdproject/" target="_new">Facebook Group</a> ] &bull; [ <a href="https://discord.gg/b8Hv5ygPdF" target="_new">Discord Server</a> ]<br />';
		echo '<a href="https://wpsd.radio/">WPSD</a> by <code>W0CHP</code> &copy; 2020-'.date("Y").' -- WPSD Project <a href="https://w0chp.radio/wpsd/#credits" target="_new">Credits</a>';
	   ?>
	</div>
	
	</div>
<?php
include 'includes/execute-background-tasks.php';
?>
<script>
executeBackgroundTasks();
</script>
    </body>
</html>
