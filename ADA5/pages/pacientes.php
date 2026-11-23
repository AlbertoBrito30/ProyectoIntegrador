<?php
$accion = $_POST['accion'] ?? '';

//AGREGAR PACIENTE
if ($accion === 'crear_paciente') {
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $edad = trim($_POST['edad'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $raza = trim($_POST['raza'] ?? '');
    $peso = (float) ($_POST['peso'] ?? 0);
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $nombre_dueno = trim($_POST['nombre_dueno'] ?? '');
    $telefono_dueno = trim($_POST['telefono_dueno'] ?? '');

    $stmt = $conection->prepare("INSERT INTO pacientes (id_usuario, nombre, edad, especie, raza, peso, diagnostico, nombre_dueno, telefono_dueno) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssdsss", $id_usuario, $nombre, $edad, $especie, $raza, $peso, $diagnostico, $nombre_dueno, $telefono_dueno);
    $stmt->execute();
}

//EDITAR PACIENTE
if ($accion === 'editar_paciente') {
    $id = (int) ($_POST['id_paciente'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $edad = trim($_POST['edad'] ?? '');
    $especie = trim($_POST['especie'] ?? '');
    $raza = trim($_POST['raza'] ?? '');
    $peso = (float) ($_POST['peso'] ?? 0);
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $nombre_dueno = trim($_POST['nombre_dueno'] ?? '');
    $telefono_dueno = trim($_POST['telefono_dueno'] ?? '');

    $stmt = $conection->prepare("UPDATE pacientes SET nombre = ?, edad = ?, especie = ?, raza = ?, peso = ?, diagnostico = ?, nombre_dueno = ?, telefono_dueno = ? WHERE id_paciente = ?");
    $stmt->bind_param("ssssdsssi", $nombre, $edad, $especie, $raza, $peso, $diagnostico, $nombre_dueno, $telefono_dueno, $id);
    $stmt->execute();
}

//ELIMINAR PACIENTE
if ($accion === 'eliminar_paciente') {
    $id = (int) ($_POST['id_paciente'] ?? 0);

    $stmt = $conection->prepare("UPDATE pacientes SET activo = 0 WHERE id_paciente = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$resultado = $conection->query("SELECT id_paciente, nombre, edad, especie, raza, peso, diagnostico, nombre_dueno, telefono_dueno FROM pacientes WHERE activo = 1");
?>

<h1>Pacientes</h1>

<button popovertarget="nuevo-paciente">Agregar paciente</button>
<div id="nuevo-paciente" popover>
    <h3>Nuevo paciente</h3>
    <form action="index.php?page=pacientes" method="POST">
        <input type="hidden" name="accion" value="crear_paciente">
        <p><label>Nombre: <input type="text" name="nombre" required></label></p>
        <p><label>Edad: <input type="text" name="edad" required></label></p>
        <p><label>Especie: <input type="text" name="especie" required></label></p>
        <p><label>Raza: <input type="text" name="raza"></label></p>
        <p><label>Peso (kg): <input type="number" name="peso" step="0.01" min="0"></label></p>
        <p><label>Diagnóstico: <input type="text" name="diagnostico"></label></p>
        <p><label>Dueño: <input type="text" name="nombre_dueno" required></label></p>
        <p><label>Teléfono: <input type="text" name="telefono_dueno"></label></p>
        <button type="submit">Guardar</button>
        <button type="button" popovertarget="nuevo-paciente" popovertargetaction="hide">Cerrar</button>
    </form>
</div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Dueño</th>
            <th>Edad</th>
            <th>Diagnóstico</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['nombre_dueno']) ?></td>
            <td><?= htmlspecialchars($row['edad'] ?? '') ?></td>
            <td><?= htmlspecialchars($row['diagnostico'] ?? '') ?></td>
            <td>
                <button popovertarget="mas-<?= $row['id_paciente'] ?>">ver</button>
                <div id="mas-<?= $row['id_paciente'] ?>" popover>
                    <h3>Datos de la mascota</h3>
                    <p><strong>Nombre:</strong> <?= htmlspecialchars($row['nombre']) ?></p>
                    <p><strong>Especie:</strong> <?= htmlspecialchars($row['especie']) ?></p>
                    <p><strong>Raza:</strong> <?= htmlspecialchars($row['raza'] ?? '') ?></p>
                    <p><strong>Peso:</strong> <?= $row['peso'] ?> kg</p>
                    <p><strong>Dueño:</strong> <?= htmlspecialchars($row['nombre_dueno']) ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($row['telefono_dueno'] ?? '') ?></p>
                    <button popovertarget="mas-<?= $row['id_paciente'] ?>" popovertargetaction="hide">cerrar</button>
                </div>

                <button popovertarget="edit-mas-<?= $row['id_paciente'] ?>">editar</button>
                <div id="edit-mas-<?= $row['id_paciente'] ?>" popover>
                    <h3>Editar paciente</h3>
                    <form action="index.php?page=pacientes" method="POST">
                        <input type="hidden" name="accion" value="editar_paciente">
                        <input type="hidden" name="id_paciente" value="<?= $row['id_paciente'] ?>">
                        <p><label>Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($row['nombre']) ?>" required></label></p>
                        <p><label>Edad: <input type="text" name="edad" value="<?= htmlspecialchars($row['edad'] ?? '') ?>" required></label></p>
                        <p><label>Especie: <input type="text" name="especie" value="<?= htmlspecialchars($row['especie']) ?>" required></label></p>
                        <p><label>Raza: <input type="text" name="raza" value="<?= htmlspecialchars($row['raza'] ?? '') ?>"></label></p>
                        <p><label>Peso (kg): <input type="number" name="peso" step="0.01" min="0" value="<?= $row['peso'] ?>"></label></p>
                        <p><label>Diagnóstico: <input type="text" name="diagnostico" value="<?= htmlspecialchars($row['diagnostico'] ?? '') ?>"></label></p>
                        <p><label>Dueño: <input type="text" name="nombre_dueno" value="<?= htmlspecialchars($row['nombre_dueno']) ?>" required></label></p>
                        <p><label>Teléfono: <input type="text" name="telefono_dueno" value="<?= htmlspecialchars($row['telefono_dueno'] ?? '') ?>"></label></p>
                        <button type="submit">Guardar cambios</button>
                    </form>
                    <hr>
                    <form action="index.php?page=pacientes" method="POST">
                        <input type="hidden" name="accion" value="eliminar_paciente">
                        <input type="hidden" name="id_paciente" value="<?= $row['id_paciente'] ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                    <button type="button" popovertarget="edit-mas-<?= $row['id_paciente'] ?>" popovertargetaction="hide">cerrar</button>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>