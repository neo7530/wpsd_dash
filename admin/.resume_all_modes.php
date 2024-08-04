<?php
//
//  Config page handler to unpause all paused moded in a single POST
//
if (isset($_POST['unpause_modes'])) {
    $paused_modes = explode(',', $_POST['paused_modes']);
    foreach ($paused_modes as $mode) {
        $command = "sudo /usr/local/sbin/wpsd-mode-manager $mode Enable";
        exec($command);
    }
    if(isset($_GET['imm'])) {
	 header("Location: /admin/index.php?func=mode_man&all_resumed");
    } else {
	header("Location: /admin/configure.php");
    }
    exit();
}
?>
