<?php

require_once __DIR__ . '/session_bootstrap.php';

// Review pages are private: users must log in before opening them.
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include "database.php";
$result = $conn->query("SELECT * FROM cafes ORDER BY cafe_id");

?>

<!DOCTYPE html>
<html>
<head><title>Cafe list</title></head>
<body>
<h2>Cafe list</h2>

<p>Hello <?php echo htmlspecialchars($_SESSION['first_name'], ENT_QUOTES, 'UTF-8'); ?>.</p>

<!-- unordered list of the cafes list.-->
<ul>
<?php
while ($cafe = $result->fetch_assoc()) {

    $safeId = urlencode((string) $cafe['cafe_id']);
    $safeName = htmlspecialchars($cafe['cafe_name'], ENT_QUOTES, 'UTF-8');
    echo "<li><a href='cafereview.php?id=" . $safeId . "'>" . $safeName . "</a></li>";
}
?>
</ul>

<?php
if ($_SESSION['role'] == 'admin') {
    echo "<p><a href='admindashboard.php'>Admin dashboard</a></p>";
}
?>

<p><a href="logout.php">Logout</a></p>
</body>
</html>
