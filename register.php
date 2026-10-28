<?php

require_once __DIR__ . '/session_bootstrap.php';

include "database.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // A username must be available so each account has its own login. 
    // This lookup also places raw input directly into SQL - need to protect it from SQL injection attacks.
    $result = $conn->query("SELECT * FROM users WHERE username='$username'");

    if ($result->num_rows > 0) {
        echo "That username is already registered.<br>";
    } else {
        // New registrations are standard users. Admin is supplied in setup.sql.
        // SQL injection is possible here.
        $sql = "INSERT INTO users (first_name,last_name,username,password,role)
                VALUES ('$first_name','$last_name','$username','$password','standard')";
        $conn->query($sql);
        echo "Registration complete. <a href='index.php'>Login</a><br>";
    }
}

?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
<h2>Register</h2>

<form action="register.php" method="POST">
    <label>First name:</label><br>
    <input type="text" name="first_name" required><br><br>

    <label>Last name:</label><br>
    <input type="text" name="last_name" required><br><br>

    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <input type="submit" value="Register">
</form>

<p><a href="index.php">Back to login</a></p>
</body>
</html>