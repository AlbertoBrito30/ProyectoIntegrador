<?php
$total_items = 0;
foreach (($_SESSION['carrito'] ?? []) as $item) {
    $total_items += $item['cantidad'];
}
?>
<header>
    <h1>Let's go vet</h1>

    <div>
        <?php if (isset($_SESSION['usuario'])): ?>
            <p>Usuario: <strong><?= htmlspecialchars($_SESSION['usuario']) ?></strong>
            <a href="config/sesion.php?accion=logout">Cerrar sesión</a></p>
        <?php else: ?>
            <p>Invitado | <a href="index.php?page=login">Iniciar sesión</a></p>
        <?php endif; ?>
    </div>

    <nav>
        <ul>
            <li><a href="index.php">Inicio</a></li>
            <li><a href="index.php?page=servicios">Servicios</a></li>
            <li><a href="index.php?page=productos">Productos</a></li>
            <li><a href="index.php?page=pacientes">Pacientes</a></li>
            <li><a href="index.php?page=facturas">Facturas / Carrito (<?= $total_items ?>)</a></li>
        </ul>
    </nav>
</header>
<hr>