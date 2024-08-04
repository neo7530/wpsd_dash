<?php
/**
 * Exports last heard data as CSV file
 */
$api_url = 'http://localhost/api/';
$json_data = file_get_contents($api_url);

$data = json_decode($json_data, true);

if ($data === null) {
    die('Error decoding JSON data');
}

$csv_file = '/tmp/transmissions.csv';

$file = fopen($csv_file, 'w');

fputcsv($file, array_keys($data[0]));

foreach ($data as $row) {
    fputcsv($file, $row);
}

fclose($file);

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename='.gethostname().'_transmissions.csv');
readfile($csv_file);

unlink($csv_file);