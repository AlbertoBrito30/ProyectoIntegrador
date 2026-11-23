<?php
$accion = $_POST['accion'] ?? '';

//REGISTRAR SERVICIO
if ($accion === 'crear_servicio') {
    $servicio = trim($_POST['servicio'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    $precio = (float) ($_POST['precio'] ?? 0);

    $stmt = $conection->prepare("INSERT INTO servicios (servicio, duracion, precio, activo) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("ssd", $servicio, $duracion, $precio);
    $stmt->execute();
}

//EDITAR SERVICIO
if ($accion === 'editar_servicio') {
    $id = (int) ($_POST['id_servicio'] ?? 0);
    $servicio = trim($_POST['servicio'] ?? '');
    $duracion = trim($_POST['duracion'] ?? '');
    $precio = (float) ($_POST['precio'] ?? 0);
    $activo = (int) ($_POST['activo'] ?? 1);

    $stmt = $conection->prepare("UPDATE servicios SET servicio = ?, duracion = ?, precio = ?, activo = ? WHERE id_servicio = ?");
    $stmt->bind_param("ssdii", $servicio, $duracion, $precio, $activo, $id);
    $stmt->execute();
}

//CAMBIAR ESTADO DEL SERVICIO
if ($accion === 'cambiar_estado_servicio') {
    $id = (int) ($_POST['id_servicio'] ?? 0);
    $nuevo_estado = (int) ($_POST['nuevo_estado'] ?? 0);

    $stmt = $conection->prepare("UPDATE servicios SET activo = ? WHERE id_servicio = ?");
    $stmt->bind_param("ii", $nuevo_estado, $id);
    $stmt->execute();
}

$total_servicios = $conection->query("SELECT COUNT(*) AS total FROM servicios")->fetch_assoc()['total'];
$activos_servicios = $conection->query("SELECT COUNT(*) AS total FROM servicios WHERE activo = 1")->fetch_assoc()['total'];
$resultado = $conection->query("SELECT id_servicio, servicio, duracion, precio, activo FROM servicios");
?>

<h1>Gestión de servicios</h1>
<p>Administración de servicios, precios y duración de procedimientos veterinarios.</p>

<p>Servicios registrados: <strong><?= $total_servicios ?></strong> | Servicios activos: <strong><?= $activos_servicios ?></strong></p>

<button popovertarget="nuevo-servicio">+ Registrar servicio</button>
<div id="nuevo-servicio" popover>
    <h3>Registrar servicio</h3>
    <form action="index.php?page=servicios" method="POST">
        <input type="hidden" name="accion" value="crear_servicio">
        <p><label>Servicio: <input type="text" name="servicio" required></label></p>
        <p><label>Duración estimada: <input type="text" name="duracion" placeholder="Ej. 30 min" required></label></p>
        <p><label>Precio: <input type="number" name="precio" step="0.01" min="0" required></label></p>
        <button type="submit">Guardar</button>
        <button type="button" popovertarget="nuevo-servicio" popovertargetaction="hide">Cerrar</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Servicio</th>
            <th>Duración</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['servicio']) ?></td>
            <td><?= htmlspecialchars($row['duracion'] ?? '') ?></td>
            <td>$<?= number_format($row['precio'], 2) ?></td>
            <td><?= $row['activo'] ? 'Activo' : 'Inactivo' ?></td>
            <td>
                <form action="config/sesion.php" method="POST">
                    <input type="hidden" name="accion" value="agregar_servicio">
                    <input type="hidden" name="id_servicio" value="<?= $row['id_servicio'] ?>">
                    <button type="submit" <?= !$row['activo'] ? 'disabled' : '' ?>>Añadir al carrito</button>
                </form>

                <!--dentro de editar estara el de desactivar/activar-->
                <button type="button" popovertarget="serv-<?= $row['id_servicio'] ?>">Editar</button>
                <div id="serv-<?= $row['id_servicio'] ?>" popover>
                    <h3>Editar servicio</h3>
                    <form action="index.php?page=servicios" method="POST">
                        <input type="hidden" name="accion" value="editar_servicio">
                        <input type="hidden" name="id_servicio" value="<?= $row['id_servicio'] ?>">
                        <p><label>Servicio: <input type="text" name="servicio" value="<?= htmlspecialchars($row['servicio']) ?>" required></label></p>
                        <p><label>Duración: <input type="text" name="duracion" value="<?= htmlspecialchars($row['duracion'] ?? '') ?>" required></label></p>
                        <p><label>Precio: <input type="number" name="precio" step="0.01" min="0" value="<?= $row['precio'] ?>" required></label></p>
                        <p>
                            <label>Estado:
                                <select name="activo">
                                    <option value="1" <?= $row['activo'] ? 'selected' : '' ?>>Activo</option>
                                    <option value="0" <?= !$row['activo'] ? 'selected' : '' ?>>Inactivo</option>
                                </select>
                            </label>
                        </p>
                        <button type="submit">Guardar cambios</button>
                    </form>
                    <hr>
                    <form action="index.php?page=servicios" method="POST">
                        <input type="hidden" name="accion" value="cambiar_estado_servicio">
                        <input type="hidden" name="id_servicio" value="<?= $row['id_servicio'] ?>">
                        <input type="hidden" name="nuevo_estado" value="<?= $row['activo'] ? 0 : 1 ?>">
                        <button type="submit"><?= $row['activo'] ? 'Desactivar' : 'Activar' ?></button>
                    </form>
                    <button type="button" popovertarget="serv-<?= $row['id_servicio'] ?>" popovertargetaction="hide">Cerrar</button>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>