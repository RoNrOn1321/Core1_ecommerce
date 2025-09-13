<?php
session_start();

// Simulate login as user ID 1 (John Doe)
$_SESSION['customer_id'] = 1;
$_SESSION['customer_first_name'] = 'John';
$_SESSION['customer_last_name'] = 'Doe';
$_SESSION['customer_email'] = 'john.doe@email.com';

echo "Logged in as John Doe (ID: 1)<br>";
echo "<a href='support/chat.php'>Go to Chat</a><br>";
echo "<a href='logout.php'>Logout</a>";
?>