<?php

require_once __DIR__ . '/session_bootstrap.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include "database.php";
$review_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

//$result = $conn->query("SELECT * FROM reviews WHERE review_id='$review_id'");
$result = $conn->query("SELECT * FROM reviews WHERE review_id='$review_id' AND user_id='$user_id'");
$review = $result->fetch_assoc();
if (!$review) {
    die("You can only delete your own reviews. <a href='cafelist.php'>Cafe list</a>");
}

//$conn->query("DELETE FROM reviews WHERE review_id='$review_id'");
$conn->query("DELETE FROM reviews WHERE review_id='$review_id' AND user_id='$user_id'");

header("Location: cafereview.php?id=" . $review['cafe_id']);
exit;

?>