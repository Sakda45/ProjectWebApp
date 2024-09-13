<?php
$servername = "151.106.124.154";
$username = "u583789277_wag20";
$password = "2567Knock";
$dbname = "u583789277_wag20";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
