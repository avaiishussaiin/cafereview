<?php

require_once __DIR__ . '/session_bootstrap.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
if ($_SESSION['role'] != 'admin') {
    die("Admin users only. <a href='cafelist.php'>Cafe list</a>");
}

include "database.php";
$review_id = $_GET['id'];

// Admin can delete a review belonging to any user.
$conn->query("DELETE FROM reviews WHERE review_id='$review_id'");

header("Location: admindashboard.php");
exit;

?>