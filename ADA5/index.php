<?php require_once __DIR__ . '/config/sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Let's go vet</title>
</head>
<body>
    <?php include 'components/header.php'; ?>
    <main>
        <?php
        switch ($_GET['page'] ?? '') {
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
                break;
        }
    
        ?>
    </main>
</body>
</html>