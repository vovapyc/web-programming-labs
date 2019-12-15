<?php

function date_compare($element1, $element2) {
    $datetime1 = strtotime($element1->date);
    $datetime2 = strtotime($element2->date);
    return $datetime1 - $datetime2;
}

function isValidJSON($str) {
    json_decode($str);
    return json_last_error() == JSON_ERROR_NONE;
}

function getAllTasks() {
    $string = file_get_contents("tasks.json");
    return json_decode($string);
}

function addTask() {
    // Read task info from requests
    $jsonParams = file_get_contents("php://input");

    if (strlen($jsonParams) > 0 && isValidJSON($jsonParams))
        $taskToAdd = json_decode($jsonParams);

    // Write to `tasks.json`
    $result = getAllTasks();
    array_push($result, $taskToAdd);

    // Sort by date
    usort($result, 'date_compare');

    $fp = fopen('tasks.json', 'w');
    fwrite($fp, json_encode($result));
    fclose($fp);

    return $result;
}

echo json_encode(addTask());