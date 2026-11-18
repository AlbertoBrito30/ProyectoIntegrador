<?php
$resultado = $conection->query("SELECT id_producto, producto, precio, stock, id_codigo_de_barras FROM productos");
?>

<h1>Productos</h1>

<table border="1" cellpadding="8">
    <thead>
        <tr>
            <th>Código</th>
            <th>Producto</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Cantidad</th>
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
            </td>
            <td>
                    <button type="submit" <?= $row['stock'] <= 0 ? 'disabled' : '' ?>>Añadir</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>