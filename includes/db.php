<?php
$servername = "localhost";
$username = "yawjcoam_goreading";
$password = "Nb@ts9~!f[mq";
$dbname = "yawjcoam_go-read";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
