<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/conectionDB.php';

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

//INICIO DE SESIÓN
if ($accion === 'login') {
    $user = trim($_POST['usuario'] ?? '');
    $pass = trim($_POST['contrasenia'] ?? '');

    $stmt = $conection->prepare("SELECT id_usuario, usuario FROM usuario WHERE usuario = ? AND contrasenia = ?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        $_SESSION['id_usuario'] = $fila['id_usuario'];
        $_SESSION['usuario'] = $fila['usuario'];
        header("Location: ../index.php?page=productos");
        exit();
    } else {
        header("Location: ../index.php?page=login&error=1");
        exit();
    }
}

if ($accion === 'registro') {
    $user = trim($_POST['usuario'] ?? '');
    $pass = trim($_POST['contrasenia'] ?? '');
    $es_vet = isset($_POST['veterinario']) ? 1 : 0;

    $stmt = $conection->prepare("INSERT INTO usuario (usuario, contrasenia, `veterinario?`) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $user, $pass, $es_vet);

    if ($stmt->execute()) {
        // Inicia sesión automáticamente al registrarse y lo manda a productos
        $_SESSION['id_usuario'] = $conection->insert_id;
        $_SESSION['usuario'] = $user;
        $_SESSION['veterinario'] = $es_vet;
        header("Location: ../index.php?page=productos");
        exit();
    } else {
        header("Location: ../index.php?page=login&error=registro");
        exit();
    }
}

//CERRAR SESIÓN
if ($accion === 'logout') {
    session_destroy();
    header("Location: ../index.php?page=login");
    exit();
}

//AÑADIR PRODUCTO AL CARRITO
if ($accion === 'agregar' && isset($_POST['id_producto'])) {
    $id = (int) $_POST['id_producto'];
    $cantidad = max(1, (int) ($_POST['cantidad'] ?? 1));

    $stmt = $conection->prepare("SELECT id_producto, producto, precio, stock FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($prod = $stmt->get_result()->fetch_assoc()) {
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] = min($_SESSION['carrito'][$id]['cantidad'] + $cantidad, $prod['stock']);
        } else {
            $_SESSION['carrito'][$id] = [
                'producto' => $prod['producto'],
                'precio'   => (float) $prod['precio'],
                'stock'    => (int) $prod['stock'],
                'cantidad' => min($cantidad, (int) $prod['stock'])
            ];
        }
    }
    header("Location: ../index.php?page=facturas");
    exit();
}

//MODIFICAR CANTIDAD EN EL CARRITO
if ($accion === 'modificar' && isset($_POST['id_producto'], $_POST['cantidad'])) {
    $id = (int) $_POST['id_producto'];
    $cant = (int) $_POST['cantidad'];

    if (isset($_SESSION['carrito'][$id])) {
        if ($cant <= 0) {
            unset($_SESSION['carrito'][$id]);
        } else {
            $_SESSION['carrito'][$id]['cantidad'] = min($cant, $_SESSION['carrito'][$id]['stock']);
        }
    }
    header("Location: ../index.php?page=facturas");
    exit();
}

//ELIMINAR PRODUCTO DEL CARRITO
if ($accion === 'eliminar' && isset($_GET['id'])) {
    unset($_SESSION['carrito'][(int) $_GET['id']]);
    header("Location: ../index.php?page=facturas");
    exit();
}

//VACIAR CARRITO
if ($accion === 'vaciar') {
    $_SESSION['carrito'] = [];
    header("Location: ../index.php?page=facturas");
    exit();
}
?>