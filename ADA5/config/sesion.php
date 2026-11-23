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

    $stmt = $conection->prepare("SELECT id_usuario, usuario, `veterinario?` FROM usuario WHERE usuario = ? AND contrasenia = ? AND activo = 1");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($fila = $res->fetch_assoc()) {
        $_SESSION['id_usuario'] = $fila['id_usuario'];
        $_SESSION['usuario'] = $fila['usuario'];
        $_SESSION['veterinario'] = $fila['veterinario?'];
        header("Location: ../index.php?page=productos");
        exit();
    } else {
        header("Location: ../index.php?page=login&error=1");
        exit();
    }
}

//REGISTRO
if ($accion === 'registro') {
    $user = trim($_POST['usuario'] ?? '');
    $pass = trim($_POST['contrasenia'] ?? '');
    $es_vet = isset($_POST['veterinario']) ? 1 : 0;

    $stmt = $conection->prepare("INSERT INTO usuario (usuario, contrasenia, `veterinario?`) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $user, $pass, $es_vet);

    if ($stmt->execute()) {
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
    $clave = 'prod_' . $id;

    $stmt = $conection->prepare("SELECT id_producto, producto, precio, stock FROM productos WHERE id_producto = ? AND activo = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($prod = $stmt->get_result()->fetch_assoc()) {
        if (isset($_SESSION['carrito'][$clave])) {
            $_SESSION['carrito'][$clave]['cantidad'] = min($_SESSION['carrito'][$clave]['cantidad'] + $cantidad, $prod['stock']);
        } else {
            $_SESSION['carrito'][$clave] = [
                'tipo'     => 'producto',
                'id'       => $id,
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

//AÑADIR SERVICIO AL CARRITO
if ($accion === 'agregar_servicio' && isset($_POST['id_servicio'])) {
    $id = (int) $_POST['id_servicio'];
    $clave = 'serv_' . $id;

    $stmt = $conection->prepare("SELECT id_servicio, servicio, precio FROM servicios WHERE id_servicio = ? AND activo = 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($serv = $stmt->get_result()->fetch_assoc()) {
        if (isset($_SESSION['carrito'][$clave])) {
            $_SESSION['carrito'][$clave]['cantidad'] += 1;
        } else {
            $_SESSION['carrito'][$clave] = [
                'tipo'     => 'servicio',
                'id'       => $id,
                'producto' => $serv['servicio'],
                'precio'   => (float) $serv['precio'],
                'stock'    => null,
                'cantidad' => 1
            ];
        }
    }
    header("Location: ../index.php?page=facturas");
    exit();
}

//MODIFICAR CANTIDAD EN EL CARRITO
if ($accion === 'modificar' && isset($_POST['clave'], $_POST['cantidad'])) {
    $clave = $_POST['clave'];
    $cant = (int) $_POST['cantidad'];

    if (isset($_SESSION['carrito'][$clave])) {
        if ($cant <= 0) {
            unset($_SESSION['carrito'][$clave]);
        } else {
            if ($_SESSION['carrito'][$clave]['tipo'] === 'producto') {
                $_SESSION['carrito'][$clave]['cantidad'] = min($cant, $_SESSION['carrito'][$clave]['stock']);
            } else {
                $_SESSION['carrito'][$clave]['cantidad'] = $cant;
            }
        }
    }
    header("Location: ../index.php?page=facturas");
    exit();
}

//ELIMINAR DEL CARRITO
if ($accion === 'eliminar' && isset($_GET['clave'])) {
    unset($_SESSION['carrito'][$_GET['clave']]);
    header("Location: ../index.php?page=facturas");
    exit();
}

//VACIAR CARRITO
if ($accion === 'vaciar') {
    $_SESSION['carrito'] = [];
    header("Location: ../index.php?page=facturas");
    exit();
}

//FINALIZAR VENTA Y GUARDAR FACTURA
if ($accion === 'finalizar_venta' && !empty($_SESSION['carrito'])) {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $id_paciente = !empty($_POST['id_paciente']) ? (int) $_POST['id_paciente'] : null;
    $total = 0;

    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    $stmt = $conection->prepare("INSERT INTO facturas (id_usuario, id_paciente, total) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $id_usuario, $id_paciente, $total);
    $stmt->execute();
    $id_factura = $conection->insert_id;

    foreach ($_SESSION['carrito'] as $item) {
        $subtotal = $item['precio'] * $item['cantidad'];

        if ($item['tipo'] === 'producto') {
            $stmt_det = $conection->prepare("INSERT INTO detalle_facturas (id_factura, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt_det->bind_param("iiidd", $id_factura, $item['id'], $item['cantidad'], $item['precio'], $subtotal);
            $stmt_det->execute();

            $stmt_stock = $conection->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
            $stmt_stock->bind_param("ii", $item['cantidad'], $item['id']);
            $stmt_stock->execute();
        } else {
            $stmt_det = $conection->prepare("INSERT INTO detalle_facturas (id_factura, id_servicio, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt_det->bind_param("iiidd", $id_factura, $item['id'], $item['cantidad'], $item['precio'], $subtotal);
            $stmt_det->execute();
        }
    }

    $_SESSION['carrito'] = [];
    header("Location: ../index.php?page=facturas");
    exit();
}
?>