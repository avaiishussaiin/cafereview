<?php

require_once __DIR__ . '/session_bootstrap.php';
require_once __DIR__ . '/csrf_helper.php';

include "database.php";

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_verify();

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($first_name === '' || $last_name === '' || $username === '' || $password === '') {
        $message = "All fields are required.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($exists) {
            $message = "That username is already registered.";
        } else {

            $stmt = $conn->prepare(
                    "INSERT INTO users (first_name, last_name, username, password, role)
                 VALUES (?, ?, ?, ?, 'standard')"
            );
            $stmt->bind_param("ssss", $first_name, $last_name, $username, $password);
            $stmt->execute();
            $stmt->close();

            $message = "Registration complete. You can now log in.";
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
<h2>Register</h2>

<?php if ($message !== ''): ?>
    <!-- SECURITY FIX (XSS): escape output -->
    <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<form action="register.php" method="POST">
    <!-- No htmlspecialchars() here: csrf_token() only ever returns hex
         characters [0-9a-f] from bin2hex(), so there's nothing to encode. -->
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

    <label>First name:</label><br>
    <input type="text" name="first_name" maxlength="100" required><br><br>

    <label>Last name:</label><br>
    <input type="text" name="last_name" maxlength="100" required><br><br>

    <label>Username:</label><br>
    <input type="text" name="username" maxlength="50" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <input type="submit" value="Register">
</form>

<p><a href="index.php">Back to login</a></p>
</body>
</html>