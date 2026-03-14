<?php
$host = "localhost";
$user = "root";          // o el usuario que uses
$pass = "";              // contraseña (normalmente vacío en XAMPP)
$db   = "bomberos";

$con = mysqli_connect($host, $user, $pass, $db) 
    or die("Error de conexión: " . mysqli_connect_error());
?>
