<?php
$conf_file = "/usr/local/etc/tnygps.conf";
$service = "tnygps"; // Name des systemd-Dienstes

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
        textarea { width: 100%; height: 300px; }
        button { margin: 5px; }
    </style>
</head>
<body>
    <h2>tnygps-Dienststatus</h2>
    <p><strong>Aktiv:</strong> <?= $status['active'] ?> <br>
       <strong>Aktiviert:</strong> <?= $status['enabled'] ?></p>

    <form method="post">
        <button name="action" value="start">Start</button>
        <button name="action" value="stop">Stop</button>
        <button name="action" value="enable">Aktivieren</button>
        <button name="action" value="disable">Deaktivieren</button>
    </form>

    <hr>

    <h2>Konfiguration bearbeiten</h2>
    <form method="post">
        <textarea name="config"><?= $config ?></textarea><br>
        <button name="action" value="save">Speichern</button>
    </form>
</body>
</html>
