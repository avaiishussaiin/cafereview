<?php

require_once __DIR__ . '/session_bootstrap.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include "database.php";

$review_id = $_GET['id'];
// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Idor fix, extra checks for user_id added to prevent other users accessing.
$result = $conn->query("SELECT * FROM reviews WHERE review_id='$review_id' AND user_id='$user_id'"); //idor attack fix

// fetch the review data 1 line at a time and stores it in an array called $review.
$review = $result->fetch_assoc();

// if no review is found then say no reviews.
if (!$review) {
    die("No Reviews. <a href='cafelist.php'>Cafe list</a>");
}

/*Gets the current review and then allowed editing and then if the file isn't 
changed if it exists then it uses the older one.*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $review_text = $_POST['review_text'];
    $file_name = $review['file_name'];

    if ($_FILES['review_file']['name'] != '') {
        $file_name = $_FILES['review_file']['name'];
        if (!move_uploaded_file($_FILES['review_file']['tmp_name'], "uploads/" . $file_name)) {
            die("File upload failed. Check the uploads folder.");
        }
    }

    //updates the review when submitted.
    // Idor fix, extra checks for user_id added to prevent other users accessing.
    $sql = "UPDATE reviews SET review_text='$review_text', file_name='$file_name' WHERE review_id='$review_id' AND user_id='$user_id'"; // idor attack fix
    
    $conn->query($sql);
    echo "Review updated. <a href='cafereview.php?id=" . $review['cafe_id'] . "'>View review</a>";

    // Idor fix, extra checks for user_id added to prevent other users accessing.
    $result = $conn->query("SELECT * FROM reviews WHERE review_id='$review_id' AND user_id='$user_id'"); //idor attack fix
    $review = $result->fetch_assoc();
}

?>
<!DOCTYPE html>
<html>
<head><title>Edit my review</title></head>
<body>
<h2>Edit my review</h2>

<form action="editreview.php?id=<?php echo $review['review_id']; ?>" method="POST" enctype="multipart/form-data">
    
<!-- INSECURE: the saved text is printed directly into the textarea. -->
    <label>My review:</label><br>
    <textarea name="review_text" rows="5" cols="50" required>
        
    <?php 
    // Securely output the review text to prevent XSS attacks
        echo htmlspecialchars($review['review_text']);
    ?></textarea><br><br>

    <?php
    if ($review['file_name'] != '') {
        echo "<p>Current file: <a href='uploads/" . $review['file_name'] . "'>View file</a></p>";
    }
    ?>

    <label>Change image (optional):</label><br>
    <input type="file" name="review_file"><br><br>
    <input type="submit" value="Save changes">
</form>

<p><a href="cafereview.php?id=<?php echo $review['cafe_id']; ?>">Back to cafe reviews</a></p>
</body>
</html>