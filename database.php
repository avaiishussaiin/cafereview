<?php

/*To act as a global sql connection called in all pages.*/

$conn = new mysqli(
    "127.0.0.1",
    "root",
    "root",
    "cafe_review",
    8889
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>