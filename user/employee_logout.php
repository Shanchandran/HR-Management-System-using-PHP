<?php
// Start the session
session_start();

// Destroy the session to log out the user
session_destroy();

// Redirect to the employee login page
header("Location: employee_login.php");
exit(); // Ensure no further code is executed after the redirect
?>