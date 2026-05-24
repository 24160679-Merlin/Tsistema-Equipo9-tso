<?php
// db.php — Conexion a MySQL con dev_user
$host = "localhost";
$dbname = "truper_db"; // 
$username = "dev_user";
$password = "Dev123"; // 
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
