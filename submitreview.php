<?php

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/csrf_helper.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include "database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    csrf_verify();

    $user_id = $_SESSION['user_id'];
    $cafe_id = $_POST['cafe_id'] ?? '';
    $review_text = trim($_POST['review_text'] ?? '');
    $file_name = '';

    if (!ctype_digit((string) $cafe_id)) {
        die("Invalid cafe id.");
    }
    if ($review_text === '') {
        die("Review text is required.");
    }


    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];

    if (isset($_FILES['review_file']) && $_FILES['review_file']['error'] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['review_file']['tmp_name'];
        $origName = $_FILES['review_file']['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
            die("Only JPG, PNG, or GIF image files are allowed.");
        }

        $file_name = bin2hex(random_bytes(16)) . '.' . $ext;
        $uploadDir = __DIR__ . '/uploads/';
        if (!move_uploaded_file($tmpPath, $uploadDir . $file_name)) {
            die("Image upload failed.");
        }
    }

    $stmt = $conn->prepare(
        "INSERT INTO reviews (user_id, cafe_id, review_text, file_name) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("iiss", $user_id, $cafe_id, $review_text, $file_name);
    $stmt->execute();
    $stmt->close();

    echo "Review submitted.<br><br>";
    echo "<a href='cafereview.php?id=" . urlencode((string) $cafe_id) . "'>Back to cafe reviews</a>";
} else {
    echo "Submit a review using the form on the cafe page.";
}

?>