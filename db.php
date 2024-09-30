<?php
$host = 'localhost';
$db = 'tenant';
$user = 'root'; // Change if necessary
$pass = '2718@Denny'; // Change if necessary

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
