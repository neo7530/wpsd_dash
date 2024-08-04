<?php


if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();

    //unset($_SESSION['DvModemFWVersion']);  // unset the modem FW version in the event the user up/downgraded, so it shows the correct ver.
    
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

if (isset($_SESSION['CSSConfigs']['Background']['TableRowBgEvenColor'])) {
    $tableRowEvenBg = $_SESSION['CSSConfigs']['Background']['TableRowBgEvenColor'];
    $backgroundModeCellActiveColor = $_SESSION['CSSConfigs']['Background']['ModeCellActiveColor'];
    $backgroundModeCellPausedColor = $_SESSION['CSSConfigs']['Background']['ModeCellPausedColor'];
    $backgroundModeCellInactiveColor = $_SESSION['CSSConfigs']['Background']['ModeCellInactiveColor'];
} else {
    $tableRowEvenBg = "inherit";
}

?>

<div class="divTable">
  <div class="divTableBody">
    <div class="divTableRow center">
      <div class="divTableHeadCell noMob" style="width:250px;"><?php _e( 'Radio Status' ); ?></div>
      <?php if ((isDVmegaCast() == 1) && (($_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasths") || ($_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasthd"))) { // DVMega Cast logic... ?>
      <div class="divTableHeadCell noMob">DVMega Cast Hotspot Freq.</div>
      <?php } else if ((isDVmegaCast() == 1) && ($_SESSION['ModemConfigs']['Modem']['Hardware'] != "dvmpicasths") &&  ($_SESSION['ModemConfigs']['Modem']['Hardware'] != "dvmpicasthd")) { // DVMega Cast logic... ?>
      <div class="divTableHeadCell noMob">DVMega Cast Mode</div>
      <?php } else { // end DVMega Cast logic. ?>
      <?php if(getConfigItem("General", "Duplex", $_SESSION['MMDVMHostConfigs']) == "1") { ?>
      <div class="divTableHeadCell noMob">TX Freq.</div>
      <div class="divTableHeadCell noMob">RX Freq.</div>
      <?php } else { ?>
      <div class="divTableHeadCell noMob">TX/RX Freq.</div>
      <?php } } ?>

      <?php if ((isDVmegaCast() == 1) && $_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasths" || $_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasthd") { // DVMega Cast logic... ?>
      <div class="divTableHeadCell noMob">DVMega Cast Mode</div>
      <?php } // end DVMega Cast logic ?>
      <?php if (isDVmegaCast() == 0) { // DVMega Cast logic... ?>
      <div class="divTableHeadCell noMob">Radio Mode</div>

      <div class="divTableHeadCell noMob">Modem Port</div>

      <div class="divTableHeadCell noMob">Modem Speed</div>
      <?php } // end DVMega Cast logic...?>

      <?php if (isDVmegaCast() == 0 && strpos($_SESSION['ModemConfigs']['Modem']['Hardware'], 'dvmpi') === false) {  // DVMega Cast & modem logic... ?> 
      <div class="divTableHeadCell noMob">TCXO Freq.</div>
      <?php } // end DVMega Cast logic ?>

      <?php if ($_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicast") { // DVMega Cast logic... ?>
      <div class="divTableHeadCell noMob">DVMega Cast Mainboard Firmware</div>
      <?php } else { // end DVMega Cast logic ?>
      <div class="divTableHeadCell noMob">Modem Firmware</div>
      <?php } ?>

    </div>
    <div class="divTableRow center">
        <?php
        // TRX Status code
        if (isset($lastHeard[0])) {
            $isTXing = false;
            // Go through the whole LH array, backward, looking for transmission.
            for (end($lastHeard); (($currentKey = key($lastHeard)) !== null); prev($lastHeard)) {                                                                         
                    $listElem = current($lastHeard);
                    if ($listElem[2] && ($listElem[6] == null) && ($listElem[5] !== 'RF')) {                                                                              
                        $isTXing = true;
                        // Get rid of 'Slot x' for DMR, as it is meaningless, when 2 slots are txing at the same time.
                        $txMode = preg_split('#\s+#', $listElem[1])[0];
                        echo "<div class=\"divTableCell middle cell_content\" style=\"background:#d11141; color:#ffffff; font-weight:bold;padding:2px;\">TX: $txMode</div>\n";
                        break;
                    }
            }
            if ($isTXing == false) {
                    $listElem = $lastHeard[0];
                if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'idle') {
		    if (isProcessRunning("MMDVMHost")) {
                    	echo "<div class=\"divTableCell middle cell_content\" style=\"font-weight:bold;padding:2px;\">IDLE</div>\n";
		    }
		    else { 
                        echo "<div class='error-state-cell divTableCell middle cell_content' style=\"font-weight:bold;padding:2px;\">OFFLINE</div>\n";
		    }
                }
                else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === NULL) {
                    if (isProcessRunning("MMDVMHost")) {
			echo "<div class=\"divTableCell middle cell_content\" style=\"font-weight:bold;padding:2px;\">IDLE</div>\n";
                    }
                    else {
                        echo "<div class='error-state-cell divTableCell middle cell_content' style=\"font-weight:bold;color:#ffffff;padding:2px;\">OFFLINE</div>\n";
                    }
                }
                else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'D-Star') {
                    echo "<div class=\"divTableCell middle active-mode-cell\" style=\"font-weight:bold;padding:2px;\">RX: D-Star</div>\n";
                }
                else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'D-Star') {
                    echo "<div class=\"divTableCell middle cell_content\" style=\"background:#ffc425;color:#000000;font-weight:bold;padding:2px;\">Standby: D-Star</div>\n";
                }
                else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'DMR') {
                    echo "<div class=\"divTableCell middle cell_content active-mode-cell\" style=\"font-weight:bold;padding:2px;\">RX: DMR</div>\n";
                }
                else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'DMR') {
                    echo "<div class=\"divTableCell middle cell_content\" style=\"background:#ffc425;color:#000000;font-weight:bold;padding:2px;\">Standby: DMR</div>\n";
                }
                else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'YSF') {
                    echo "<div class=\"divTableCell middle cell_content active-mode-cell\" style=\"font-weight:bold;padding:2px;\">RX: YSF</div>\n";
                }
                else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'YSF') {
                    echo "<div class=\"divTableCell middle cell_content\" style=\"background:#ffc425;color:#000000;font-weight:bold;padding:2px;\">Standby: YSF</div>\n";
                }
                else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'P25') {
                    echo "<div class=\"divTableCell middle cell_content active-mode-cell\" style=\"font-weight:bold;padding:2px;\">RX: P25</div>\n";
                }
                else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'P25') {
                    echo "<div class=\"divTableCell middle cell_content\" style=\"background:#ffc425;color:#000000;font-weight:bold;padding:2px;\">Standby: P25</div>\n"; 
                }
                else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'M17') {
                    echo "<div class=\"divTableCell middle cell_content active-mode-cell\" style=\"padding:2px;font-weight:bold;\">RX M17</div>\n";
                }
                else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'M17') {
                    echo "<div class=\"divTableCell middle cell_content\" style=\"background:#ffc425;color:#000000;padding:2px;font-weight:bold;\">Standby: M17</div>\n";
                }
                else if ($listElem[2] && $listElem[6] == null && getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'NXDN') {
                    echo "<div class=\"divTableCell middle cell_content active-mode-cell\" style=\"font-weight:bold;padding:2px;\">RX: NXDN</div>\n";
                }   
                else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'NXDN') {
                    echo "<div class=\"divTableCell middle cell_content\" style=\"background:#ffc425;color:#000000;font-weight:bold;padding:2px;\">Standby: NXDN</div>\n"; 
                }   
                else if (getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs']) === 'POCSAG') {
                    echo "<div class=\"divTableCell middle cell_content\" style=\"color:#fff; background:#d11141; font-weight:bold;padding:2px;\">TX: POCSAG</div>\n";
                }   
                else {
                    echo "<div class=\"divTableCell middle cell_content\">".getActualMode($lastHeard, $_SESSION['MMDVMHostConfigs'])."</div>\n";
                }   
            }   
        }   
        else {
            echo "<div class=\"divTableCell middle cell_content\" style=\"font-weight:bold;padding:2px;\">IDLE</div>\n";
        }
        ?>
      <?php if ($_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicast") { // DVMega Cast logic... ?>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>">Base Station/IP Radio Mode</div>
      <?php } else { // end DVMega Cast logic ?>
      <?php if(getConfigItem("General", "Duplex", $_SESSION['MMDVMHostConfigs']) == "1") { ?>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;"><?php echo getMHZ(getConfigItem("Info", "TXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></div>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;"><?php echo getMHZ(getConfigItem("Info", "RXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></div>
      <?php } else { ?>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;"><?php echo getMHZ(getConfigItem("Info", "RXFrequency", $_SESSION['MMDVMHostConfigs'])); ?></div>
      <?php }  ?>
      <?php if ($_SESSION['ModemConfigs']['Modem']['Hardware'] != "dvmpicast" && $_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasths" || $_SESSION['ModemConfigs']['Modem']['Hardware'] == "dvmpicasthd") { // DVMega Cast logic... ?>
 		<div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;">Hotspot Mode: Simplex</div>
      <?php } else { // end DVmega Cast logic ?>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;"><?php if(getConfigItem("General", "Duplex", $_SESSION['MMDVMHostConfigs']) == "1") { echo "Duplex"; } else { echo "Simplex"; } ?></div>
      <?php } ?>
      <?php if ($_SESSION['ModemConfigs']['Modem']['Hardware'] != "dvmpicast" && $_SESSION['ModemConfigs']['Modem']['Hardware'] != "dvmpicasths" && $_SESSION['ModemConfigs']['Modem']['Hardware'] != "dvmpicasthd") { // DVMega Cast logic... ?>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;"><?php echo getConfigItem("Modem", "UARTPort", $_SESSION['MMDVMHostConfigs']); ?></div>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;"><?php echo number_format(getConfigItem("Modem", "UARTSpeed", $_SESSION['MMDVMHostConfigs'])); ?> bps</div>
      <?php if (isDVmegaCast() == 0 && strpos($_SESSION['ModemConfigs']['Modem']['Hardware'], 'dvmpi') === false) { // DVmega CAST and modem logic... ?>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;"><?php echo $_SESSION['DvModemTCXOFreq']; ?></div>
      <?php } } } // end DVmega Cast logic ?>
      <div class="divTableCell cell_content middle noMob" style="background: <?php echo $tableRowEvenBg; ?>;"><?php echo $_SESSION['DvModemFWVersion']; ?></div>
    </div>
  </div>
</div>

