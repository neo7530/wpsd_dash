<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config/version.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta http-equiv="cache-control" content="max-age=0" />
      <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
      <meta http-equiv="expires" content="0" />
      <meta http-equiv="pragma" content="no-cache" />
      <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
      <title><?php echo exec('cat /etc/hostname'); ?> Live Caller Display - W0CHP-PiStar-Dash</title>
      <script type="text/javascript" src="/js/jquery.min.js?version=<?php echo $versionCmd; ?>"></script>
      <script type="text/javascript" src="/js/functions.js?version=<?php echo $versionCmd; ?>"></script>
      <link rel="stylesheet" type="text/css" href="/css/fonts.css?version=<?php echo $versionCmd; ?>" />
      <link rel="stylesheet" type="text/css" href="/css/live-caller.css?version=<?php echo $versionCmd; ?>" />
    </head>
    <body>
      <script type="text/javascript">
        $(function() {
          setInterval(function(){
            $('#liveDetails').load('/mmdvmhost/live_caller_backend.php');
          }, 1500);
        });
      </script>
      <div id="liveDetails">
        <?php include '../mmdvmhost/live_caller_backend.php'; ?>
      </div>
    </body>
</html>
