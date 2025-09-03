<?php
// Destroys the session and sends you back to the login page
session_start();
session_destroy();
header("Location: login.php");
exit;
