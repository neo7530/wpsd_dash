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

// Load the language support
require_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';

// Force the Locale to the stock locale just while we run the update
setlocale(LC_ALL, "LC_CTYPE=en_GB.UTF-8;LC_NUMERIC=C;LC_TIME=C;LC_COLLATE=C;LC_MONETARY=C;LC_MESSAGES=C;LC_PAPER=C;LC_NAME=C;LC_ADDRESS=C;LC_TELEPHONE=C;LC_MEASUREMENT=C;LC_IDENTIFICATION=C");

// Sanity Check that this file has been opened correctly
if ($_SERVER["PHP_SELF"] == "/admin/update.php") {

   if (!isset($_GET['ajax'])) {
    if (!file_exists('/var/log/pi-star')) {
      system('sudo mkdir -p /var/log/pi-star/');
      system('sudo chmod 775 /var/log/pi-star/');
      system('sudo chown root:mmdvm /var/log/pi-star/');
    }
     system('sudo touch /var/log/pi-star/WPSD-update.log > /dev/null 2>&1 &');
     system('sudo echo "" > /var/log/pi-star/WPSD-update.log > /dev/null 2>&1 &');
     system('sudo /usr/local/sbin/wpsd-update > /dev/null 2>&1 &');
  }

  // Sanity Check Passed.
  header('Cache-Control: no-cache');

  if (!isset($_GET['ajax'])) {
    //unset($_SESSION['update_offset']);
    if (file_exists('/var/log/pi-star/WPSD-update.log')) {
      $_SESSION['update_offset'] = filesize('/var/log/pi-star/WPSD-update.log');
    } else {
      $_SESSION['update_offset'] = 0;
    }
  }
  
  if (isset($_GET['ajax'])) {
    //session_start();
    if (!file_exists('/var/log/pi-star/WPSD-update.log')) {
      exit();
    }
    
    $handle = fopen('/var/log/pi-star/WPSD-update.log', 'rb');
    if (isset($_SESSION['update_offset'])) {
      fseek($handle, 0, SEEK_END);
      if ($_SESSION['update_offset'] > ftell($handle)) //log rotated/truncated
        $_SESSION['update_offset'] = 0; //continue at beginning of the new log
      $data = stream_get_contents($handle, -1, $_SESSION['update_offset']);
      $_SESSION['update_offset'] += strlen($data);
      echo nl2br($data);
      }
    else {
      fseek($handle, 0, SEEK_END);
      $_SESSION['update_offset'] = ftell($handle);
      } 
  exit();
  }
  
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
    <title>WPSD <?php echo __( 'Digital Voice' ) . " ".__( 'Dashboard' )." - ".__( 'WPSD Update' );?></title>
<?php include_once $_SERVER['DOCUMENT_ROOT'].'/config/browserdetect.php'; ?>
    <link rel="stylesheet" type="text/css" href="/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript" src="/js/jquery-timing.min.js?version=<?php echo $versionCmd; ?>"></script>
    <script type="text/javascript">
    $(function() {
      $.repeat(1000, function() {
        $.get('/admin/update.php?ajax', function(data) {
          if (data.length < 1) return;
          var objDiv = document.getElementById("tail");
          var isScrolledToBottom = objDiv.scrollHeight - objDiv.clientHeight <= objDiv.scrollTop + 1;
	  $('#tail').append(data);
	  //data = data.replace(/\\n/g, '').replace(/\n/g, ''); // strip linebreaks and literal '\n's
	  //$('#tail').append('<pre>' + data + '</pre>'); // preformat it.
          if (isScrolledToBottom)
            objDiv.scrollTop = objDiv.scrollHeight;
        });
      });
    });
    </script>
  </head>
  <body>
      <div class="container">
	<div class="header">
	   <div class="SmallHeader shLeft">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
	     <div class="SmallHeader shRight noMob">
               <div id="CheckUpdate"><?php echo $version; ?></div><br />
             </div>
             <h1>WPSD <?php echo __( 'Digital Voice' ) . " ".__( 'Dashboard' )." - ".__( 'WPSD Update' );?></h1>
             <div class="navbar">
              <script type= "text/javascript">
               $(document).ready(function() {
                 setInterval(function() {
                   $("#timer").load("/includes/datetime.php");
                   }, 1000);

                 function update() {
                   $.ajax({
                     type: 'GET',
                     cache: false,
                     url: '/includes/datetime.php',
                     timeout: 1000,
                     success: function(data) {
                       $("#timer").html(data); 
                       window.setTimeout(update, 1000);
                     }
                   });
                 }
                 update();
               });
              </script>
              <div class="headerClock"> 
                <span id="timer"></span>
            </div>
	    <a class="menuconfig" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
	    <a class="menubackup" href="/admin/config_backup.php"><?php echo __( 'Backup/Restore' );?></a>
	    <a class="menupower" href="/admin/power.php"><?php echo __( 'Power' );?></a>
	    <a class="menuadmin" href="/admin/"><?php echo __( 'Admin' );?></a>
	    <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
	</div>
  </div>
  <div class="contentwide">
  <table width="100%">
  <tr><td align="left"><div id="tail"><h3 style='color:white;margin-bottom:-5px;margin-top:-1px;'>Starting WPSD Software Update...</h3></div></td></tr>
  </table>
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
