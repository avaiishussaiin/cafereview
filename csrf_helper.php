<?php

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): void
{
    $submitted = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    if (
        empty($_SESSION['csrf_token']) ||
        !is_string($submitted) ||
        !hash_equals($_SESSION['csrf_token'], $submitted)
    ) {
        http_response_code(403);
        die("Invalid or missing CSRF token. Please go back and try again.");
    }
}
