<?php
$host = "localhost";
$dbname = "truper_db";
$username = "audit_user";
$password = "Audit123"; // La contraseña que creamos para el auditor
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
