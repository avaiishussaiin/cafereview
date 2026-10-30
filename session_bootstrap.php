<?php

// helps with csrf and session hijacking attacks by setting the SameSite attribute to Lax and Secure to true.
ini_set('session.cookie_samesite', 'lax');
ini_set('session.cookie_secure', '1');

session_start();

?>