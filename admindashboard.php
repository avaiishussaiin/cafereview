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

include "database.php";

$sql = "SELECT reviews.*, users.username, cafes.cafe_name FROM reviews
        JOIN users ON reviews.user_id=users.user_id
        JOIN cafes ON reviews.cafe_id=cafes.cafe_id
        ORDER BY reviews.review_id DESC";
$reviews = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head><title>Admin dashboard</title></head>
<body>
<h2>Admin dashboard</h2>
<p>
    <a href="addcafe.php">Add a cafe</a> | <a href="cafelist.php">List of Cafes</a> | <a href="logout.php">Logout</a>
</p>

<h3>All reviews</h3>

<?php
if ($reviews->num_rows == 0) {
    echo "No reviews added so far....";
}

while ($review = $reviews->fetch_assoc()) {
    $safeCafe = htmlspecialchars($review['cafe_name'], ENT_QUOTES, 'UTF-8');
    $safeUser = htmlspecialchars($review['username'], ENT_QUOTES, 'UTF-8');
    $safeText = htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8');

    echo "<p><b>" . $safeCafe . "</b> - " . $safeUser . "</p>";
    echo "<p>" . $safeText . "</p>";

    if ($review['file_name'] != '') {

        $safeFile = rawurlencode($review['file_name']);
        $safeFileLabel = htmlspecialchars($review['file_name'], ENT_QUOTES, 'UTF-8');
        echo "<p><a href='uploads/" . $safeFile . "'>View uploaded file (" . $safeFileLabel . ")</a></p>";
    }

    $deleteUrl = "admindeletereview.php?id=" . urlencode((string) $review['review_id'])
        . "&csrf_token=" . urlencode(csrf_token());
    echo "<a href='" . htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8') . "' onclick=\"return confirm('Delete this review?');\">Delete this review</a>";

    echo "<hr>";
}
?>
</body>
</html>