<?php require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php'; ?>
<div class="header">
    <div class="SmallHeader shLeft">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
    <div class="SmallHeader shRight">
       <div id="CheckUpdate">
       <?php
          include $_SERVER['DOCUMENT_ROOT'].'/includes/checkupdates.php';
       ?>
       </div><br />
    </div>
    <h1>WPSD - Digital Voice Dashboard Advanced</h1>
    <p>
	<div class="navbar">
	    <a class="menuconfig" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
	    <a class="menubackup" href="/admin/config_backup.php"><?php echo __( 'Backup/Restore' );?></a>
	    <a class="menuupdate" href="/admin/update.php"><?php echo __( 'WPSD Update' );?></a>
	    <a class="menuadmin" href="/admin/"><?php echo __( 'Admin' );?></a>
	    <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
	</div>

    </p>
</div>
