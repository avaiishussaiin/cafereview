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

    // created a restriction of types of items that can be uploaded. only images allowed.
    /*if (
    isset($_POST['review_image_url']) &&
    $_POST['review_image_url'] !== ''
) {
    http_response_code(400);
    exit('Image URLs are not supported. Please upload an image.');
}*/

    //ssrf vulnerability code, allows any file upladed and then fetches it from the server, allowing an attacker to fetch internal resources.
    /*elseif (!empty($_POST['review_image_url'])) {
        $image_url = $_POST['review_image_url'];
 
    // Iserver blindly fetches whatever URL the user supplies — SSRF
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
    $conn->query($sql); */

    // fixed for ssrf vulnerability, now only allows https urls and rejects local file paths. 
    elseif (!empty($_POST['review_image_url'])) {
    $image_url = $_POST['review_image_url'] ?? '';

    // Requires a valid HTTPS URL; reject any other local filesystem filepaths.
    if (
        !is_string($image_url) ||
        !preg_match('~\Ahttps://[^\s]+\z~i', $image_url) ||
        filter_var($image_url, FILTER_VALIDATE_URL) === false
    ) {
        http_response_code(400);
        exit('Please enter a valid HTTPS image URL.');
    }

    $file_contents = @file_get_contents($image_url);

    if ($file_contents === false) {
        die("Could not fetch the URL.");
    }

    $original_name = basename(parse_url($image_url, PHP_URL_PATH));
    $file_name = 'fetched_' . time() . '_' . $original_name;

    file_put_contents(
        __DIR__ . "/uploads/" . $file_name,
        $file_contents
    );
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