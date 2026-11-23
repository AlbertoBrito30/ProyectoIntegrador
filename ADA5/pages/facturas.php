<?php
$pacientes = $conection->query("SELECT id_paciente, nombre, nombre_dueno FROM pacientes WHERE activo = 1");
$facturas = $conection->query("SELECT f.id_factura, f.fecha, f.total, u.usuario, p.nombre AS paciente, p.nombre_dueno AS dueno FROM facturas f LEFT JOIN usuario u ON f.id_usuario = u.id_usuario LEFT JOIN pacientes p ON f.id_paciente = p.id_paciente ORDER BY f.id_factura DESC");
?>

<h1>Facturas (Carrito de compras)</h1>

<?php if (empty($_SESSION['carrito'])): ?>
    <p>El carrito está vacío. Ve a <a href="index.php?page=productos">Productos</a> o <a href="index.php?page=servicios">Servicios</a> para agregar conceptos.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Concepto</th>
                <th>Precio Unitario</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            foreach ($_SESSION['carrito'] as $clave => $item):
                $subtotal = $item['precio'] * $item['cantidad'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= $item['tipo'] === 'servicio' ? 'Servicio' : 'Producto' ?></td>
                <td><?= htmlspecialchars($item['producto']) ?></td>
                <td>$<?= number_format($item['precio'], 2) ?></td>
                <td>
                    <form action="config/sesion.php" method="POST">
                        <input type="hidden" name="accion" value="modificar">
                        <input type="hidden" name="clave" value="<?= htmlspecialchars($clave) ?>">
                        <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" <?= isset($item['stock']) ? 'max="' . $item['stock'] . '"' : '' ?>>
                        <button type="submit">Modificar</button>
                    </form>
                </td>
                <td>$<?= number_format($subtotal, 2) ?></td>
                <td>
                    <a href="config/sesion.php?accion=eliminar&clave=<?= urlencode($clave) ?>">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">Total:</th>
                <th colspan="2">$<?= number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>

    <form action="config/sesion.php" method="POST">
        <input type="hidden" name="accion" value="finalizar_venta">
        <p>
            <label for="id_paciente">Paciente / Dueño (opcional):</label>
            <select name="id_paciente" id="id_paciente">
                <option value="">Venta de mostrador</option>
                <?php while ($pac = $pacientes->fetch_assoc()): ?>
                    <option value="<?= $pac['id_paciente'] ?>"><?= htmlspecialchars($pac['nombre']) ?> - <?= htmlspecialchars($pac['nombre_dueno']) ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Generar factura</button>
        </p>
    </form>

    <p><a href="config/sesion.php?accion=vaciar">Vaciar carrito</a></p>
<?php endif; ?>

<hr>

<h2>Historial de facturas</h2>
<table>
    <thead>
        <tr>
            <th>Folio</th>
            <th>Fecha</th>
            <th>Atendió</th>
            <th>Paciente</th>
            <th>Dueño</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($fac = $facturas->fetch_assoc()): ?>
        <tr>
            <td>#<?= $fac['id_factura'] ?></td>
            <td><?= htmlspecialchars($fac['fecha']) ?></td>
            <td><?= htmlspecialchars($fac['usuario'] ?? 'Sin usuario') ?></td>
            <td><?= htmlspecialchars($fac['paciente'] ?? 'Mostrador') ?></td>
            <td><?= htmlspecialchars($fac['dueno'] ?? 'Mostrador') ?></td>
            <td>$<?= number_format($fac['total'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>