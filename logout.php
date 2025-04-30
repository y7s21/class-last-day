<?php
session_start(); // Start the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Clear local storage
echo "<script>localStorage.removeItem('loggedInNo'); window.location.href='index.html';</script>";
exit;
?>