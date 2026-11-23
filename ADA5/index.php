<?php
require_once __DIR__ . '/config/sesion.php';

$page = $_GET['page'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let's go vet</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <?php include 'components/header.php'; ?>
    <main>
        <?php
        switch ($page) {
            case 'login':
                include 'pages/login.php';
                break;
            case 'servicios':
                include 'pages/servicios.php';
                break;
            case 'productos':
                include 'pages/productos.php';
                break;
            case 'pacientes':
                include 'pages/pacientes.php';
                break;
            case 'facturas':
                include 'pages/facturas.php';
                break;
            default:
                echo '<h2>Bienvenido a Let\'s go vet</h2>';
                echo '<p>Sistema de gestión veterinaria para el control de pacientes, servicios, inventario y facturas.</p>';
                echo '<p>Equipo: Alberto de Jesús Brito Ocampo y Jesús Israel Catzim Tzuc.</p>';
                break;
        }
        ?>
    </main>
</body>
</html>