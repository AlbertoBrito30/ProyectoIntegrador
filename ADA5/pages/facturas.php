<h1>Facturas (Carrito de compras)</h1>

<?php if (empty($_SESSION['carrito'])): ?>
    <p>El carrito está vacío. Ve a <a href="index.php?page=productos">Productos</a> para agregar artículos.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($_SESSION['carrito'] as $id => $item): 
                $subtotal = $item['precio'] * $item['cantidad'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['producto']) ?></td>
                <td>$<?= number_format($item['precio'], 2) ?></td>
                <td>
                    <form action="config/sesion.php" method="POST">
                        <input type="hidden" name="accion" value="modificar">
                        <input type="hidden" name="id_producto" value="<?= $id ?>">
                        <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" max="<?= $item['stock'] ?>">
                        <button type="submit">Modificar</button>
                    </form>
                </td>
                <td>$<?= number_format($subtotal, 2) ?></td>
                <td>
                    <a href="config/sesion.php?accion=eliminar&id=<?= $id ?>">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total:</th>
                <th colspan="2">$<?= number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>
    <br>
    <a href="config/sesion.php?accion=vaciar">Vaciar carrito</a>
<?php endif; ?>