<?php

require_once __DIR__ . '/session_bootstrap.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Admin pages need the admin role; a normal account cannot use this page.
if ($_SESSION['role'] != 'admin') {
    die("Admin users only. <a href='cafelist.php'>Cafe list</a>");
}

include "database.php";

/*Get all reviews with the username and cafe name. Connects the reviews table 
with the users and cafes tables to get the necessary information. 
then orders it by review_id in desc order so the latest reviews are shown first.*/
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
    // INSECURE: the same unescaped review content is also shown to the admin.
    echo "<p><b>" . $review['cafe_name'] . "</b> - " . $review['username'] . "</p>";
    echo "<p>" . $review['review_text'] . "</p>";
    if ($review['file_name'] != '') {
        echo "<p><a href='uploads/" . $review['file_name'] . "'>View uploaded file</a></p>";
    }
    echo "<a href='admindeletereview.php?id=" . $review['review_id'] . "'>Delete this review</a>";
    // Creates the gaps/lines between the reviews
    echo "<hr>";
}
?>
</body>
</html>