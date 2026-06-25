<?php
session_start();
session_unset();
session_destroy();

// Redirect back to your login screen (change login.php if your file has a different name)
header("Location: login.php"); 
exit();