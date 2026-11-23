<h1>Iniciar sesión</h1>
<?php if (isset($_GET['error'])): ?>
    <p class="error">
        <?= $_GET['error'] === 'registro' ? 'Error al registrar usuario.' : 'Usuario o contraseña incorrectos.' ?>
    </p>
<?php endif; ?>

<form method="POST" action="config/sesion.php">
    <input type="hidden" name="accion" value="login">

    <label for="usuario">Usuario:</label>
    <input type="text" name="usuario" id="usuario" required>

    <label for="contrasenia">Contraseña:</label>
    <input type="password" name="contrasenia" id="contrasenia" required>

    <button type="submit">Iniciar sesión</button>
</form>

<hr>

<h2>Registrar nuevo usuario</h2>
<form method="POST" action="config/sesion.php">
    <input type="hidden" name="accion" value="registro">

    <label for="reg_usuario">Nuevo Usuario:</label>
    <input type="text" name="usuario" id="reg_usuario" required>

    <label for="reg_contrasenia">Contraseña:</label>
    <input type="password" name="contrasenia" id="reg_contrasenia" required>

    <label>
        <input type="checkbox" name="veterinario" value="1"> ¿Es veterinario?
    </label>

    <button type="submit">Registrarse</button>
</form>