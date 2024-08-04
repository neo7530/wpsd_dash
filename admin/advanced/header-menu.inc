<?php require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php'; ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php'; ?>
<div class="header">
    <div class="SmallHeader shLeft">Hostname: <?php echo exec('cat /etc/hostname'); ?></div>
    <div class="SmallHeader shRight">
       <div id="CheckUpdate">
       <?php
          include $_SERVER['DOCUMENT_ROOT'].'/includes/checkupdates.php';
       ?>
       </div><br />
    </div>
    <?php if ($_SERVER['REQUEST_URI'] === '/admin/appearance.php') { ?>
    <h1>WPSD - Digital Voice Dashboard Appearance Settings</h1>
    <?php } else { ?>
    <h1>WPSD - Digital Voice Dashboard Advanced Area</h1>
    <?php } ?>
    <p>
	<div class="navbar">
	    <a class="menuconfig" href="/admin/configure.php"><?php echo __( 'Configuration' );?></a>
	    <a class="menubackup" href="/admin/config_backup.php"><?php echo __( 'Backup/Restore' );?></a>
	    <a class="menuupdate" href="/admin/update.php"><?php echo __( 'WPSD Update' );?></a>
	    <a class="menuadmin" href="/admin/"><?php echo __( 'Admin' );?></a>
	    <a class="menudashboard" href="/"><?php echo __( 'Dashboard' );?></a>
	</div>
    <?php if (!($_SERVER['REQUEST_URI'] === '/admin/appearance.php')) { ?>
	<div class="lnavbar">
            <ul class="mainnav">
		<li class="has-subs"><a href="#">Quick Editors</a>
		    <ul class="dropdown">
		    	<li class="subs"><a href="edit_mmdvmhost.php">MMDVMHost</a></li>
			<!-- D-Star Sub -->
			<li class="subs has-subs"><a href="#">D-Star</a>
			    <ul class="dropdown">
				<li class="subs"><a href="edit_ircddbgateway.php">ircDDB Gateway</a></li>
				<li class="subs"><a href="edit_timeserver.php">TimeServer</a></li>
				<li class="subs"><a href="edit_starnetserver.php">StarNet Server</a></li>
			    </ul>
			</li>
			<!-- DMR Sub -->
			<li class="subs has-subs"><a href="#">DMR</a>
			    <ul class="dropdown">
				<li class="subs"><a href="edit_dmrgateway.php">DMR Gateway</a></li>
				<li class="subs"><a href="edit_dmr2ysf.php">DMR to YSF</a></li>
				<li class="subs"><a href="edit_dmr2nxdn.php">DMR to NXDN</a></li>
			    </ul>
			</li>
			<!-- SystemFusion Sub -->
			<li class="subs has-subs"><a href="#">SystemFusion</a>
			    <ul class="dropdown">
				<li class="subs"><a href="edit_ysfgateway.php">YSF Gateway</a></li>
				<li class="subs"><a href="edit_dgidgateway.php">DGId Gateway</a></li>
				<li class="subs"><a href="edit_ysf2dmr.php">YSF to DMR</a></li>
				<li class="subs"><a href="edit_ysf2p25.php">YSF to P25</a></li>
				<li class="subs"><a href="edit_ysf2nxdn.php">YSF to NXDN</a></li>
			    </ul>
			</li>
            <!-- others -->
			<li class="subs"><a href="edit_p25gateway.php">P25 Gateway</a></li>
			<li class="subs"><a href="edit_nxdngateway.php">NXDN Gateway</a></li>
			<li class="subs"><a href="edit_m17gateway.php">M17 Gateway</a></li>
			<li class="subs"><a href="edit_aprsgateway.php">APRS Gateway</a></li>
			<!-- DAPNet Sub -->
			<li class="subs has-subs"><a href="#">DAPNet</a>
			    <ul class="dropdown">
				<li class="subs"><a href="edit_dapnetgateway.php">DAPNet Gateway</a></li>
				<li class="subs"><a href="edit_dapnetapi.php">DAPNet API</a></li>
			    </ul>
			</li>
		    </ul>
		</li>

		<li class="has-subs"><a href="#">Full Editors</a>
		    <ul class="dropdown">
			<li class="subs"><a href="fulledit_mmdvmhost.php">MMDVMHost</a></li>
			<li class="subs"><a href="fulledit_pistar-remote.php">RF Remote Commands</a></li>
			<!-- D-Star Sub -->
			<li class="subs has-subs"><a href="#">D-Star</a>
			    <ul class="dropdown">
				<li class="subs"><a href="fulledit_ircddbgateway.php">ircDDB Gateway</a></li>
				<li class="subs"><a href="fulledit_timeserver.php">TimeServer</a></li>
				<li class="subs"><a href="fulledit_starnetserver.php">StarNet Server</a></li>
			    </ul>
			</li>
			<!-- DMR Sub -->
			<li class="subs has-subs"><a href="#">DMR</a>
			    <ul class="dropdown">
				<li class="subs"><a href="fulledit_dmrgateway.php">DMR Gateway</a></li>
				<li class="subs"><a href="fulledit_dmr2ysf.php">DMR to YSF</a></li>
				<li class="subs"><a href="fulledit_dmr2nxdn.php">DMR to NXDN</a></li>
				<li class="subs"><a href="fulledit_bmapikey.php">Brandmeister API Key</a></li>
			    </ul>
			</li>
			<!-- YSF Sub -->
			<li class="subs has-subs"><a href="#">SystemFusion</a>
			    <ul class="dropdown">
				<li class="subs"><a href="fulledit_ysfgateway.php">YSF Gateway</a></li>
				<li class="subs"><a href="fulledit_dgidgateway.php">DGId Gateway</a></li>
				<li class="subs"><a href="fulledit_ysf2dmr.php">YSF to DMR</a></li>
				<li class="subs"><a href="fulledit_ysf2p25.php">YSF to P25</a></li>
				<li class="subs"><a href="fulledit_ysf2nxdn.php">YSF to NXDN</a></li>
			    </ul>
			</li>
            <!-- others -->
			<li class="subs"><a href="fulledit_p25gateway.php">P25 Gateway</a></li>
			<li class="subs"><a href="fulledit_nxdngateway.php">NXDN Gateway</a></li>
			<li class="subs"><a href="fulledit_m17gateway.php">M17 Gateway</a></li>
			<li class="subs"><a href="fulledit_aprsgateway.php">APRS Gateway</a></li>
			<!-- DAPNet Sub -->
			<li class="subs has-subs"><a href="#">DAPNet</a>
			    <ul class="dropdown">
				<li class="subs"><a href="fulledit_dapnetgateway.php">DAPNet Gateway</a></li>
				<li class="subs"><a href="fulledit_dapnetapi.php">DAPNet API</a></li>
			    </ul>
			</li>
			<li class="subs"><a href="fulledit_wpaconfig.php">WiFi</a></li>
			<li class="subs"><a href="fulledit_cron.php">System Cron</a></li>
			<li class="subs"><a href="fulledit_rssidat.php">RSSI Data</a></li>
		    </ul>
		</li>
		
		<li class="has-subs"><a href="#">Hosts File Editors</a>
		    <ul class="dropdown">
			<li class="subs"><a href="fulledit_hosts_DCS_Hosts.php">DCS Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_DExtra_Hosts.php">DExtra Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_DPlus_Hosts.php">DPlus Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_DMR_Hosts.php">DMR Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_P25_Hosts.php">P25 Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_NXDN_Hosts.php">NXDN Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_M17_Hosts.php">M17 Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_XLX_Hosts.php">XLX Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_YSF_Hosts.php">YSF Hosts</a></li>
			<li class="subs"><a href="fulledit_hosts_FCS_Hosts.php">FCS Hosts</a></li>
		    </ul>
		</li>
			
		<li class="has-subs"><a href="#">Tools</a>
		    <ul class="dropdown">
			<!-- Services Sub -->
			<li class="subs has-subs"><a href="#">WPSD Services</a>
			    <ul class="dropdown">
				<li class="subs"><a href="services.php?action=status">Services Status</a></li>
				<li class="subs"><a href="services.php?action=fullstop">Stop Services</a></li>
				<li class="subs"><a href="services.php?action=restart">(Re-)Start Services</a></li>
			    </ul>
			</li>
			<li class="subs"><a href="services.php?action=updatehostsfiles">Update Hosts Files</a></li>
			<li class="subs"><a href="ssh_access.php">SSH Access</a></li>
			<li class="subs"><a href="jitter_test.php">DMR Jitter Test</a></li>
			<li class="subs"><a href="calibration.php">MMDVMHost Calibration</a></li>
			<li class="subs"><a href="filemanager.php">File Manager</a></li>
			<?php if(isDVmegaCast() == 1) { // Begin DVmega Cast logic... ?>	
			<li class="subs"><a href="/admin/cast/cast-firmware/">DVMega Cast Upgrade Utility</a></li>
			<?php } else { // end DVmega cast logic ?>
			<li class="subs"><a href="modem_fw_upgrade.php">Modem Firmware Upgrade</a></li>
			<?php } ?>
			<li class="subs"><a href="/config/changelog.php">ChangeLog</a></li>
		    </ul>
		</li>
	    </ul>
	</div>
    <?php } ?>
    </p>
</div>
