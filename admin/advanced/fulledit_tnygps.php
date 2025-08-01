<?php
$ip = $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname());
if (!isset($_SESSION) || !is_array($_SESSION)) {
    session_id('wpsdsession');
    session_start();
    
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';          // MMDVMDash Config
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/tools.php';        // MMDVMDash Tools
    include_once $_SERVER['DOCUMENT_ROOT'].'/mmdvmhost/functions.php';    // MMDVMDash Functions
    include_once $_SERVER['DOCUMENT_ROOT'].'/config/language.php';        // Translation Code
    checkSessionValidity();
}

$editorname = 'TNYGPS Daemon';
$service = "tnygps"; // Name des systemd-Dienstes
$configfile = '/usr/local/etc/tnygps.conf';
$tempfile = '/tmp/yAw432GHa9.tmp';
$servicenames = array();

require_once('fulledit_template.php');


function service_status($service) {
    $status = shell_exec("systemctl is-active $service");
    $enabled = shell_exec("systemctl is-enabled $service");
    return ['active' => trim($status), 'enabled' => trim($enabled)];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["action"])) {
        switch ($_POST["action"]) {
            case "start": shell_exec("sudo systemctl start $service"); break;
            case "stop": shell_exec("sudo systemctl stop $service"); break;
            case "enable": shell_exec("sudo systemctl enable $service"); break;
            case "disable": shell_exec("sudo systemctl disable $service"); break;
            case "save":
                if (isset($_POST["config"])) {
                    file_put_contents($conf_file, $_POST["config"]);
                }
                break;
        }
    }
}

$status = service_status($service);
$config = file_exists($conf_file) ? htmlspecialchars(file_get_contents($conf_file)) : "";

?>

<!DOCTYPE html>
<html>
<head>
    <title>tnygps Dienstverwaltung</title>
    <style>
	h2,
	p,
	#live-gps,
  	#live-fix,
	#live-speed {
    		color: white;
  	}
        textarea { width: 100%; height: 300px; }
        button { margin: 5px; }
    </style>
</head>
<body>
    <h2>tnygps-Dienststatus</h2>
    <p><strong>Aktiv:</strong> <?= $status['active'] ?> <br>
       <strong>Systemdienst Aktiviert:</strong> <?= $status['enabled'] ?></p>

    <form method="post">
        <button name="action" value="start">Start</button>
        <button name="action" value="stop">Stop</button>
        <button name="action" value="enable">Aktivieren</button>
        <button name="action" value="disable">Deaktivieren</button>
    </form>

    <hr>
    <h2>Live-GPS-Daten</h2>
    <div id="live-gps">Warten auf Daten...</div>
    <div id="live-fix">Fix-Status: unbekannt</div>

    <hr>

<script>
function ladeLiveDaten() {
     fetch(`http://<?php echo $ip ?>:8081/status`)
        .then(res => res.json())
        .then(data => {
                document.getElementById('live-gps').textContent = 'GPS: ' + data.lat.toFixed(6) + ', ' + data.lon.toFixed(6);
                document.getElementById('live-fix').textContent = 'Fix: ' + (data.fix ? 'Ja ✔️' : 'Nein ❌');

        })
        .catch(err => {
            console.error('Fehler beim Laden der Live-Daten:', err);
            document.getElementById('live-gps').textContent = 'GPS: Fehler 😢';
            document.getElementById('live-fix').textContent = 'Fix: Fehler 😢';
        });
}

// Alle 5 Sekunden abrufen
setInterval(ladeLiveDaten, 5000);
// Und direkt einmal beim Start
ladeLiveDaten();
</script>


</body>
</html>
