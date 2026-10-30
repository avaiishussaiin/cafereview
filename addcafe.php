<?php

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/csrf_helper.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

//Checks if the role is for admin and then allows you to add a cafe.
if ($_SESSION['role'] != 'admin') {
    die("Admin users only. <a href='cafelist.php'>Cafe list</a>");
}

include "database.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();

    $cafe_name = trim($_POST['cafe_name'] ?? '');

    if ($cafe_name === '') {
        $message = "Cafe name is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO cafes (cafe_name) VALUES (?)");
        $stmt->bind_param("s", $cafe_name);
        $stmt->execute();
        $stmt->close();
        $message = "Cafe added.";
    }
}

?>
<!DOCTYPE html>
<html>
<head><title>Add cafe</title></head>
<body>
<h2>Add cafe</h2>

<?php if ($message !== ''): ?>
    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<form action="addcafe.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    <label>Cafe name:</label><br>
    <input type="text" name="cafe_name" required><br><br>
    <input type="submit" value="Add cafe">
</form>

<p><a href="admindashboard.php">Back to admin dashboard</a></p>
</body>
</html>