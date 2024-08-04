<?php

if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();
}
// Load the language support
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
// Load the Pi-Star Release file
$pistarReleaseConfig = '/etc/pistar-release';
$configPistarRelease = array();
$configPistarRelease = parse_ini_file($pistarReleaseConfig, true);
// Load the Version Info
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
// load config
require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';
// load t00lz
require_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';

if (file_exists('/etc/default/shellinabox')) {
  $getPortCommand = "grep -m 1 'SHELLINABOX_PORT=' /etc/default/shellinabox | awk -F '=' '/SHELLINABOX_PORT=/ {print $2}'";
  $shellPort = exec($getPortCommand);
}

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/admin/advanced/ssh_access.php") {
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
    <title>WPSD <?php echo __( 'Digital Voice' ) . " ".__( 'Dashboard' )." - SSH";?></title>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/jquery-timing.min.js?version=<?php echo $versionCmd; ?>"></script>
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
    <h1>WPSD <?php echo __( 'Digital Voice' ) . " ".__( 'Dashboard for' )." ".$_SESSION['MYCALL']; ?> - SSH Access</h1>
        <p>
        <div class="navbar">
          <a class="menuconfig" href="/admin/configure.php">Configuration
          </a>
          <a class="menubackup" href="/admin/config_backup.php">Backup/Restore
          </a>
          <a class="menuupdate" href="/admin/update.php">WPSD Update
          </a>
          <a class="menuadmin" href="/admin/">Admin
          </a>
          <a class="menudashboard" href="/">Dashboard
          </a>
        </div>
	</p>
  </div>
  <div class="contentwide">
    <?php if (isset($shellPort)) {
      echo "<iframe src=\"http://".$_SERVER['HTTP_HOST'].":".$shellPort."\" style=\"border:1px solid #999; background:#000; color:#00ff00; padding:5px;margin:5px;\" name=\"WPSD_SSH\" scrolling=\"no\" frameborder=\"0\" marginheight=\"0px\" marginwidth=\"0px\" height=\"700px\" width=\"1152px\"></iframe>";
    }
    else {
      echo "SSH Feature not yet installed";
    } ?>
  <?php if (isset($shellPort)) { echo "<p><a href=\"//".$_SERVER['HTTP_HOST'].":".$shellPort."\" style=\"text-decoration:underline;color:inherit;\">Click here for full-screen SSH client</a></p>\n"; } ?>
  <div class="footer">
  <a href="https://wpsd.radio/">WPSD</a> &copy; <code>W0CHP</code> 2020-<?php echo date("Y"); ?>
  <br />
  </div>
  </body>
  </html>

<?php
}
?>
