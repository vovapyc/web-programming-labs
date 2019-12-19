<?php
$filepath = $_FILES['file']['tmp_name'];


// Write all data to temporary arrays
$handle = fopen($filepath, "r");
$csv_raw_data = array();
for ($i = 1; $row = fgetcsv($handle, 0, '|'); ++$i) {
    array_push($csv_raw_data, $row);
}
fclose($handle);

$csv_header = $csv_raw_data[0];
$csv_data = array();

for ($i = 1; $i < count($csv_raw_data); $i++) {
    $row = array();
    for ($x = 0; $x < count($csv_header); $x++) {
        $row[$csv_header[$x]] = $csv_raw_data[$i][$x];
    }
    array_push($csv_data, $row);
}


// Write data to json
$json_result = array('header' => $csv_header, 'data' => $csv_data);
$fp = fopen('results.json', 'w');
fwrite($fp, json_encode($json_result));
fclose($fp);


echo json_encode($json_result);
