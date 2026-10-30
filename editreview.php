<?php

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/csrf_helper.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include "database.php";
$review_id = $_GET['id'];
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
    die("No review found, or you don't have permission to edit it. <a href='cafelist.php'>Cafe list</a>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();

    $review_text = trim($_POST['review_text'] ?? '');
    $file_name = $review['file_name'];

    if ($review_text === '') {
        die("Review text is required.");
    }

    if (isset($_FILES['review_file']) && $_FILES['review_file']['error'] === UPLOAD_ERR_OK) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];

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
            die("File upload failed. Check the uploads folder.");
        }
    }

    $stmt = $conn->prepare(
        "UPDATE reviews SET review_text = ?, file_name = ? WHERE review_id = ? AND user_id = ?"
    );
    $stmt->bind_param("ssii", $review_text, $file_name, $review_id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "Review updated. <a href='cafereview.php?id=" . urlencode((string) $review['cafe_id']) . "'>View review</a>";

    $stmt = $conn->prepare("SELECT * FROM reviews WHERE review_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $review_id, $user_id);
    $stmt->execute();
    $review = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

?>
<!DOCTYPE html>
<html>
<head><title>Edit my review</title></head>
<body>
<h2>Edit my review</h2>

<form action="editreview.php?id=<?php echo urlencode((string) $review['review_id']); ?>" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

    <label>My review:</label><br>
    <textarea name="review_text" rows="5" cols="50" maxlength="2000" required><?php echo htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>

    <?php
    if ($review['file_name'] != '') {
        $safeFileHref = rawurlencode($review['file_name']);
        $safeFileLabel = htmlspecialchars($review['file_name'], ENT_QUOTES, 'UTF-8');
        echo "<p>Current file: <a href='uploads/" . $safeFileHref . "'>View file (" . $safeFileLabel . ")</a></p>";
    }
    ?>

    <label>Change image (optional):</label><br>
    <input type="file" name="review_file" accept="image/png,image/jpeg,image/gif"><br><br>
    <input type="submit" value="Save changes">
</form>

<p><a href="cafereview.php?id=<?php echo urlencode((string) $review['cafe_id']); ?>">Back to cafe reviews</a></p>
</body>
</html>
