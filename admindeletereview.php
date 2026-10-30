<?php

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/csrf_helper.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
if ($_SESSION['role'] != 'admin') {
    die("Admin users only. <a href='cafelist.php'>Cafe list</a>");
}

csrf_verify();
include "database.php";

$review_id = $_GET['id'] ?? '';

if (!ctype_digit((string) $review_id)) {
    die("Invalid review id.");
}

$stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ?");
$stmt->bind_param("i", $review_id);
$stmt->execute();
$stmt->close();

header("Location: admindashboard.php");
exit;

?>