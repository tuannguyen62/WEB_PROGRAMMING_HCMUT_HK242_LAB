<?php
// Kết nối database
$host = 'localhost'; 
$dbname = 'music_store'; // Tên database
$username = 'root'; // Mặc định của mamp
$password = 'root'; // Password mặc định

//Kết nối đến database
$mysqli = new mysqli($host, $username, $password, $dbname);

// nếu kết nối thất bại
if ($mysqli->connect_errno != 0) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>