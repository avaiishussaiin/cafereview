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
    if ($_FILES['review_file']['name'] != '') {
        $file_name = $_FILES['review_file']['name'];
        if (!move_uploaded_file($_FILES['review_file']['tmp_name'], "uploads/" . $file_name)) {
            die("Image upload failed.");
        }
    }

    //no CSRF token.
    $sql = "INSERT INTO reviews (user_id,cafe_id,review_text,file_name)
            VALUES ('$user_id','$cafe_id','$review_text','$file_name')";
    $conn->query($sql);

    echo "Review submitted.<br><br>";
    echo "<a href='cafereview.php?id=$cafe_id'>Back to cafe reviews</a>";
} else {
    echo "Submit a review using the form on the cafe page.";
}
?>