<?php
session_start();
echo session_id();
// remove all session variables
session_unset();

// destroy the session
session_destroy();

header("Location: welcome.php");
?>