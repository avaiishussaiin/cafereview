<?php

require_once __DIR__ . '/session_bootstrap.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

//Checks if the role is for admin and then allows you to add a cafe.
if ($_SESSION['role'] != 'admin') {
    die("Admin users only. <a href='cafelist.php'>Cafe list</a>");
}

include "database.php";

//Checks if the form is submitted and then adds the cafe to the database.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cafe_name = $_POST['cafe_name'];
 
    $conn->query("INSERT INTO cafes (cafe_name) VALUES ('$cafe_name')");
    echo "Cafe added.<br>";
}

?>
<!DOCTYPE html>
<html>
<head><title>Add cafe</title></head>
<body>
<h2>Add cafe</h2>

<form action="addcafe.php" method="POST">
    <label>Cafe name:</label><br>
    <input type="text" name="cafe_name" required><br><br>
    <input type="submit" value="Add cafe">
</form>

<p><a href="admindashboard.php">Back to admin dashboard</a></p>
</body>
</html>