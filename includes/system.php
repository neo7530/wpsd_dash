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
include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';	      // Translation Code
require_once($_SERVER['DOCUMENT_ROOT'].'/config/ircddblocal.php');

?>
<h3 style="text-align:left;font-weight:bold;margin:5px 0 2px 0;"><?php echo __( 'Service &amp; Process Status' );?></h3>
<div class="status-grid">
  <?php if (getFWstate()=='0' ) { ?>
  <div class='grid-item paused-mode-cell' title="Disabled">Firewall</div>
  <?php } else { ?>
  <div class="grid-item <?php getServiceStatusClass(getFWstate()); ?>">Firewall</div>
  <?php } ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('MMDVMHost')); ?>">MMDVMHost</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('DMRGateway')); ?>">DMRGateway</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('ircddbgatewayd')); ?>">ircDDBGateway (D-Star)</div>  
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('YSFGateway')); ?>">YSFGateway</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('YSF2DMR')); ?>">YSF2DMR</div>
  <?php if (getPSRState()=='0' ) { ?>
  <div class='grid-item disabled-mode-cell'>RF Remote Control</div>
  <?php } else { ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('/usr/local/sbin/pistar-remote',true)); ?>">RF Remote Control</div> 
  <?php } ?>

  <?php if (getCronState()=='0' ) { ?>
  <div class='grid-item paused-mode-cell' title="Disabled">Cron</div>
  <?php } else { ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('cron')); ?>">Cron</div>
  <?php } ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('NXDNGateway')); ?>">NXDNGateway</div> 
  <?php if (isDVmegaCast() == 0) { ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('M17Gateway')); ?>">M17Gateway</div> 
  <?php } ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('NXDNParrot')); ?>">NXDNParrot</div> 
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('usr/sbin/vnstatd',true)); ?>">Network Metrics (vnstat)</div> 
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('APRSGateway')); ?>">APRSGateway</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('/lib/systemd/systemd-timesyncd',true)); ?>">Time Sync Service</div> 
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('YSFParrot')); ?>">YSFParrot</div>

  <div class="grid-item <?php getServiceStatusClass(autoAPenabled()); ?>">Auto AP</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('P25Gateway')); ?>">P25Gateway</div> 
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('P25Parrot')); ?>">P25Parrot</div> 
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('DAPNETGateway')); ?>">DAPNETGateway</div> 
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('timeserverd')); ?>">TimeServer (D-Star)</div>
  <?php if (getPSWstate()=='0' ) { ?>
  <div class='grid-item paused-mode-cell' title="Disabled">WPSD Services Watchdog</div>
  <?php } else { ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('/usr/local/sbin/pistar-watchdog',true)); ?>">WPSD Services Watchdog</div> 
  <?php } ?>

  <div class="grid-item <?php getServiceStatusClass(UPnPenabled()); ?>">UPnP</div>  
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('gpsd'));  ?>">GPSd</div>  
  <?php if (isDVmegaCast() == 0) { ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('NextionDriver'));  ?>">NextionDriver</div>  
  <?php } else { ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('castudp'));  ?>">DVMega Cast UDP Service</div>  
  <?php } ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('starnetserverd')); ?>">Starnet Server (D-Star)</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('DGIdGateway')); ?>">DGIdGateway</div> 
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('DMR2YSF')); ?>">DMR2YSF</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('YSF2P25')); ?>">YSF2P25</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('YSF2NXDN')); ?>">YSF2NXDN</div>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('DMR2NXDN')); ?>">DMR2NXDN</div>
  <?php if (isDVmegaCast() == 1) { ?>
  <div class="grid-item <?php getServiceStatusClass(isProcessRunning('castserial'));  ?>">DVMega Cast Serial Service</div>  
  <?php } ?>
</div>

<br />
