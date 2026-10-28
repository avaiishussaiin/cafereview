<?php

require_once __DIR__ . '/session_bootstrap.php';


// Review pages are private: users must log in before opening them.
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

include "database.php";
// Gets all the cafes from database.
$result = $conn->query("SELECT * FROM cafes ORDER BY cafe_id");

?>

<!DOCTYPE html>
<html>
<head><title>Cafe list</title></head>
<body>
<h2>Cafe list</h2>

<p>Hello <?php echo $_SESSION['first_name']; ?>.</p>

<!-- unordered list of the cafes list.-->
<ul>
<?php
// Each cafe links to its own page containing reviews and a submission form.
while ($cafe = $result->fetch_assoc()) {
    echo "<li><a href='cafereview.php?id=" . $cafe['cafe_id'] . "'>";
    // </li> is to create the bullet points.
    echo $cafe['cafe_name'] . "</a></li>";
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