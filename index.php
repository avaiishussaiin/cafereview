<?php

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/csrf_helper.php';

include "database.php";

// Check whether the login form has been submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // SECURITY FIX (login CSRF): without this, an attacker could forge a
    // cross-site request that logs the victim's browser into an
    // attacker-controlled account, tricking them into saving data (e.g.
    // a review) under an identity the attacker can see.
    csrf_verify();

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // SECURITY FIX (SQLi): prepared statement. Password hashing was
    // intentionally not added here — this app compares the submitted
    // password directly against the plaintext value stored in the
    // users table (matching register.php, which also stores plaintext).
    // That comparison now happens as a bound parameter in the query
    // itself rather than via string concatenation.
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        session_regenerate_id(true);

        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['role'] = $user['role'];
        // SECURITY FIX: refresh the CSRF token too, tied to the new
        // session identity.
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

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
<h2>Cafe reviews with security updates applied.</h2>
<h3>Login</h3>

<form action="index.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

    <label>Username:</label><br>
    <input type="text" name="username" required><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br>

    <input type="submit" value="Login">
</form>

<p><a href="register.php">Register</a></p>
</body>
</html>