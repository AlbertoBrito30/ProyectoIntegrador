<?php
$conection = new mysqli("localhost", "proyecto_desarrollo", "", "lets_go_vet");
    if ($conection->connect_error) {
        die("Error de conexión: " . $conection->connect_error);
    }
    $conection->set_charset("utf8mb4");
?>