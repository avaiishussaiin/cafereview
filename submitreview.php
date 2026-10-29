<?php
 
require_once __DIR__ . '/session_bootstrap.php';
 
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
 
include "database.php";
 
// This page receives the review form rather than displaying another form.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $cafe_id = $_POST['cafe_id'];
    $review_text = $_POST['review_text'];
    $file_name = '';
 
    // INSECURE: accept the original filename and any file contents/type. A repeated filename can overwrite an earlier uploaded file.
      if (isset($_FILES['review_file']) && $_FILES['review_file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['review_file']['name'];
        if (!move_uploaded_file($_FILES['review_file']['tmp_name'], "uploads/" . $file_name)) {
            die("Image upload failed.");
        }
    }

    //ssrf vulnerability 
    elseif (!empty($_POST['review_image_url'])) {
        $image_url = $_POST['review_image_url'];
 
    // INSECURE: server blindly fetches whatever URL the user supplies — SSRF
    $file_contents = @file_get_contents($image_url);
 
    if ($file_contents === false) {
        die("Could not fetch the URL.");
    }
 
    // Use the original filename from the URL — preserves extension naturally
    $original_name = basename(parse_url($image_url, PHP_URL_PATH));
    $file_name = 'fetched_' . time() . '_' . $original_name;
    file_put_contents("uploads/" . $file_name, $file_contents);
}
 
    $sql = "INSERT INTO reviews (user_id,cafe_id,review_text,file_name)
            VALUES ('$user_id','$cafe_id','$review_text','$file_name')";
    $conn->query($sql);
 
    echo "Review submitted.<br><br>";
    echo "<a href='cafereview.php?id=$cafe_id'>Back to cafe reviews</a>";
} else {
    echo "Submit a review using the form on the cafe page.";
}

?>