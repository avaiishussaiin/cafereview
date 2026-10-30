<?php

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/csrf_helper.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

csrf_verify();

include "database.php";

$review_id = $_GET['id'] ?? '';
$user_id = $_SESSION['user_id'];

if (!ctype_digit((string) $review_id)) {
    die("Invalid review id.");
}

$stmt = $conn->prepare("SELECT * FROM reviews WHERE review_id = ? AND user_id = ?");
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$review = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$review) {
    die("You can only delete your own reviews. <a href='cafelist.php'>Cafe list</a>");
}

$stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ? AND user_id = ?");
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$stmt->close();

header("Location: cafereview.php?id=" . urlencode((string) $review['cafe_id']));
exit;

?>