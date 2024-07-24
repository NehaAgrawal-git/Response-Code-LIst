<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "response_code_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
