<?php
$accion = $_POST['accion'] ?? '';

//AGREGAR PRODUCTO
if ($accion === 'crear_producto') {
    $codigo = trim($_POST['id_codigo_de_barras'] ?? '');
    $producto = trim($_POST['producto'] ?? '');
    $precio = (float) ($_POST['precio'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);

    $stmt = $conection->prepare("INSERT INTO productos (id_codigo_de_barras, producto, precio, stock) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $codigo, $producto, $precio, $stock);
    $stmt->execute();
}

//EDITAR PRODUCTO
if ($accion === 'editar_producto') {
    $id = (int) ($_POST['id_producto'] ?? 0);
    $codigo = trim($_POST['id_codigo_de_barras'] ?? '');
    $producto = trim($_POST['producto'] ?? '');
    $precio = (float) ($_POST['precio'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);

    $stmt = $conection->prepare("UPDATE productos SET id_codigo_de_barras = ?, producto = ?, precio = ?, stock = ? WHERE id_producto = ?");
    $stmt->bind_param("ssdii", $codigo, $producto, $precio, $stock, $id);
    $stmt->execute();
}

//ELIMINAR PRODUCTO
if ($accion === 'eliminar_producto') {
    $id = (int) ($_POST['id_producto'] ?? 0);

    $stmt = $conection->prepare("UPDATE productos SET activo = 0 WHERE id_producto = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$resultado = $conection->query("SELECT id_producto, producto, precio, stock, id_codigo_de_barras FROM productos WHERE activo = 1");
?>

<h1>Productos</h1>

<button popovertarget="nuevo-prod">Agregar producto</button>
<div id="nuevo-prod" popover>
    <h3>Nuevo producto</h3>
    <form action="index.php?page=productos" method="POST">
        <input type="hidden" name="accion" value="crear_producto">
        <p><label>Código: <input type="text" name="id_codigo_de_barras" required></label></p>
        <p><label>Producto: <input type="text" name="producto" required></label></p>
        <p><label>Precio: <input type="number" name="precio" step="0.01" min="0" required></label></p>
        <p><label>Stock: <input type="number" name="stock" min="0" required></label></p>
        <button type="submit">Guardar</button>
        <button type="button" popovertarget="nuevo-prod" popovertargetaction="hide">Cerrar</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Carrito</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id_codigo_de_barras']) ?></td>
            <td><?= htmlspecialchars($row['producto']) ?></td>
            <td>$<?= number_format($row['precio'], 2) ?></td>
            <td><?= $row['stock'] ?></td>
            <td>
                <form action="config/sesion.php" method="POST">
                    <input type="hidden" name="accion" value="agregar">
                    <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">
                    <input type="number" name="cantidad" value="1" min="1" max="<?= $row['stock'] ?>" <?= $row['stock'] <= 0 ? 'disabled' : '' ?>>
                    <button type="submit" <?= $row['stock'] <= 0 ? 'disabled' : '' ?>>Añadir al carrito</button>
                </form>
            </td>
            <td>
                <button type="button" popovertarget="edit-prod-<?= $row['id_producto'] ?>">editar</button>
                <div id="edit-prod-<?= $row['id_producto'] ?>" popover>
                    <h3>Editar producto</h3>
                    <form action="index.php?page=productos" method="POST">
                        <input type="hidden" name="accion" value="editar_producto">
                        <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">
                        <p><label>Código: <input type="text" name="id_codigo_de_barras" value="<?= htmlspecialchars($row['id_codigo_de_barras']) ?>" required></label></p>
                        <p><label>Producto: <input type="text" name="producto" value="<?= htmlspecialchars($row['producto']) ?>" required></label></p>
                        <p><label>Precio: <input type="number" name="precio" step="0.01" min="0" value="<?= $row['precio'] ?>" required></label></p>
                        <p><label>Stock: <input type="number" name="stock" min="0" value="<?= $row['stock'] ?>" required></label></p>
                        <button type="submit">Guardar cambios</button>
                    </form>
                    <hr>
                    <form action="index.php?page=productos" method="POST">
                        <input type="hidden" name="accion" value="eliminar_producto">
                        <input type="hidden" name="id_producto" value="<?= $row['id_producto'] ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                    <button type="button" popovertarget="edit-prod-<?= $row['id_producto'] ?>" popovertargetaction="hide">Cerrar</button>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>