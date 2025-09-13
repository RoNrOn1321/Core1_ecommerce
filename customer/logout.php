<?php
session_start();
session_destroy();
echo "Logged out successfully<br>";
echo "<a href='test_login.php'>Login Again</a>";
?>