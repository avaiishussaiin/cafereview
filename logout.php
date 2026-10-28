<?php

require_once __DIR__ . '/session_bootstrap.php';

// End the current login and return to the login form.
session_destroy();
header("Location: index.php");
exit;

?>