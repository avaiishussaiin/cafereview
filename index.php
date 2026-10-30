<?php

require_once __DIR__ . '/session_bootstrap.php';

include "database.php";

// Check whether the login form has been submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    /*$username = $_POST['username'];
    $password = $_POST['password'];

    // INSECURE user input is placed directly into SQL. No user/password htmlspecialchars.
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);*/

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // rejecting non-string inputs to prevent SQL injection and other attacks.
    if (!is_string($username) || !is_string($password)) {
      http_response_code(400);
       exit('Invalid login details.');
    }

    $userscredentials = $conn->prepare(
     "SELECT * FROM users WHERE username = ? AND password = ?"
    );

    $userscredentials->bind_param("ss", $username, $password);
    $userscredentials->execute();

    $result = $userscredentials->get_result();
    $userscredentials->close();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Give the authenticated user a new session ID overrides the old session id to prevent session fixation attacks.
        session_regenerate_id(true);

        //saving which database user logged in and their role.
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: admindashboard.php");
        } else {
            header("Location: cafelist.php");
        }
        exit;
    } else {
        echo "Username or password is not correct.<br>";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
<h2>Cafe reviews WITH security updates Applied.</h2>
<h3>Login</h3>

<form action="index.php" method="POST">
    <label>Username:</label><br>
    <input type="text" name="username" required><br>

    <!-- Added password field for login with type text instead of password to demonstrate SQLi vulnerability. -->
    <label>Password:</label><br>
    <input type="password" name="password" required><br>

    <input type="submit" value="Login">
</form>

<p><a href="register.php">Register</a></p>
</body>
</html>