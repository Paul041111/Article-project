<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'horizon_articles');

function db() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die(json_encode([
            "success" => false,
            "error" => "Database connection failed"
        ]));
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}