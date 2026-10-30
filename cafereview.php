<?php

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/csrf_helper.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include "database.php";

$cafe_id = $_GET['id'] ?? '';

if (!ctype_digit((string) $cafe_id)) {
    die("Invalid cafe id. <a href='cafelist.php'>Cafe list</a>");
}

$stmt = $conn->prepare("SELECT * FROM cafes WHERE cafe_id = ?");
$stmt->bind_param("i", $cafe_id);
$stmt->execute();
$result = $stmt->get_result();
$cafe = $result->fetch_assoc();
$stmt->close();

if (!$cafe) {
    die("Cafe not found. <a href='cafelist.php'>Cafe list</a>");
}

$stmt = $conn->prepare(
    "SELECT reviews.*, users.username FROM reviews
     JOIN users ON reviews.user_id = users.user_id
     WHERE reviews.cafe_id = ? ORDER BY reviews.review_id DESC"
);
$stmt->bind_param("i", $cafe_id);
$stmt->execute();
$reviews = $stmt->get_result();
$stmt->close();

?>

<!DOCTYPE html>
<html>
<head><title>Cafe reviews</title></head>
<body>
<h2><?php echo htmlspecialchars($cafe['cafe_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
<h3>Reviews</h3>

<?php
if ($reviews->num_rows == 0) {
    echo "<p>No reviews yet.</p>";
}

while ($review = $reviews->fetch_assoc()) {

    $safeUser = htmlspecialchars($review['username'], ENT_QUOTES, 'UTF-8');
    $safeText = htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8');

    echo "<p><b>" . $safeUser . "</b></p>";
    echo "<p>" . $safeText . "</p>";

    if ($review['file_name'] != '') {
        $safeFileHref = rawurlencode($review['file_name']);
        $safeFileLabel = htmlspecialchars($review['file_name'], ENT_QUOTES, 'UTF-8');
        echo "<p><a href='uploads/" . $safeFileHref . "'>View uploaded file (" . $safeFileLabel . ")</a></p>";
    }

    if ($review['user_id'] == $_SESSION['user_id']) {
        $safeReviewId = urlencode((string) $review['review_id']);
        $tokenParam = urlencode(csrf_token());
        echo "<a href='editreview.php?id=" . $safeReviewId . "'>Edit my review</a> ";
        // SECURITY FIX (CSRF): deletion requires a token.
        echo "<a href='deletereview.php?id=" . $safeReviewId . "&csrf_token=" . $tokenParam . "' onclick=\"return confirm('Delete this review?');\">Delete my review</a>";
    }
    echo "<hr>";
}
?>


<h3>Submit a review</h3>
<form action="submitreview.php" method="POST" enctype="multipart/form-data">
    <!-- SECURITY FIX (CSRF): token tied to this session, checked server-side
         in submitreview.php. This is the primary fix for the CSRF PoC.
         No htmlspecialchars() here: csrf_token() only ever returns hex
         characters [0-9a-f] from bin2hex(), so there's nothing to encode. -->
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="cafe_id" value="<?php echo (int) $cafe['cafe_id']; ?>">

    <label>Review:</label><br>
    <textarea name="review_text" rows="5" cols="50" maxlength="2000" required></textarea><br><br>

    <label>Image or file (optional):</label><br>
    <input type="file" name="review_file" accept="image/png,image/jpeg,image/gif"><br><br>

    <input type="submit" value="Submit review">
</form>
<p><a href="cafelist.php">Back to cafe list</a> | <a href="logout.php">Logout</a></p>
</body>
</html>
