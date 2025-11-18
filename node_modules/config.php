<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "election";

$conn = mysqli_connect($host, $user, $pass, $database);

if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

?>
