<?php

$conn = new mysqli(
    "127.0.0.1",
    "root",
    "",
    "cafe_review",
    3306
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>