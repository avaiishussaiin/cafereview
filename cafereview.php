<?php
 
require_once __DIR__ . '/session_bootstrap.php';
 
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
 
include "database.php";
 
$cafe_id = $_GET['id'];
$result = $conn->query("SELECT * FROM cafes WHERE cafe_id='$cafe_id'");
$cafe = $result->fetch_assoc();
//Checks if cafe exists and if it doesnt then show message.
if (!$cafe) {
    die("Cafe not found. <a href='cafelist.php'>Cafe list</a>");
}
 
//Get all reviews and the users who wrote them.
$sql = "SELECT reviews.*, users.username FROM reviews
        JOIN users ON reviews.user_id=users.user_id
        WHERE reviews.cafe_id='$cafe_id' ORDER BY reviews.review_id DESC";
$reviews = $conn->query($sql);
 
?>
 
<!DOCTYPE html>
<html>
<head><title>Cafe reviews</title></head>
<body>
<h2><?php echo $cafe['cafe_name']; ?></h2>
<h3>Reviews</h3>
 
<?php
if ($reviews->num_rows == 0) {
    echo "<p>No reviews yet.</p>";
}
 
while ($review = $reviews->fetch_assoc()) {
    echo "<p><b>" . $review['username'] . "</b></p>";
 
    // INSECURE: raw review text becomes HTML, allowing stored XSS.
    echo "<p>" . $review['review_text'] . "</p>";
 
    // Every attachment is linked directly, without checking its file type.
    if ($review['file_name'] != '') {
        echo "<p><a href='uploads/" . $review['file_name'] . "'>View uploaded file</a></p>";
    }
 
    // Standard users see edit/delete links only on their own reviews.
    if ($review['user_id'] == $_SESSION['user_id']) {
        echo "<a href='editreview.php?id=" . $review['review_id'] . "'>Edit my review</a> ";
        echo "<a href='deletereview.php?id=" . $review['review_id'] . "'>Delete my review</a>";
    }
    echo "<hr>";
}
?>
 
 
<h3>Submit a review</h3>
<form action="submitreview.php" method="POST" enctype="multipart/form-data">
<input type="hidden" name="cafe_id" value="<?php echo $cafe['cafe_id']; ?>">
 
    <label>Review:</label><br>
<textarea name="review_text" rows="5" cols="50" required></textarea><br><br>
 
    <label>Image or file (optional):</label><br>
<input type="file" name="review_file"><br><br>
<label>Or enter an image URL (server will fetch it):</label><br>
<input type="text" name="review_image_url" size="50" placeholder=""><br><br>
 
    <input type="submit" value="Submit review">
</form>
 
<p><a href="cafelist.php">Back to cafe list</a> | <a href="logout.php">Logout</a></p>
</body>
</html>