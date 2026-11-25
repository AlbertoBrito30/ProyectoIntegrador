# Manual Técnico — Let's go vet

Este documento describe la arquitectura, estructura de archivos, base de datos y configuración necesaria para ejecutar y mantener el proyecto **Let's go vet**.

## 1. Requisitos del entorno

- Servidor web Apache con soporte para PHP 8 (desarrollado y probado en XAMPP).
- Gestor de base de datos MariaDB / MySQL (probado en MariaDB 10.11).
- Navegador web moderno con soporte para el atributo HTML `popover`.

## 2. Estructura del proyecto

Los archivos de la aplicación se encuentran organizados dentro de `ADA5/`:

- `index.php`: Archivo principal que carga el encabezado y cambia de página según `?page=`.
- `estilos.css`: Estilos generales de la página, tablas, menú y ventanas emergentes.
- `components/header.php`: Encabezado con el logo, estado de sesión y contador del carrito.
- `config/conectionDB.php`: Conexión a la base de datos
- `config/sesion.php`: Manejo de sesión PHP, autenticación (`login`, `registro`, `logout`), operaciones del carrito y guardado de facturas.
- `config/lets_go_vet.sql`: Respaldo de estructura, datos de prueba, procedimiento almacenado, función, vista y disparador.
- `media/imagenes/logo.png`: Logotipo mostrado en el encabezado.
- `pages/login.php`: Formularios de inicio de sesión y registro.
- `pages/servicios.php`: Módulo CRUD de servicios veterinarios e integración con el carrito.
- `pages/productos.php`: Módulo CRUD de inventario e integración con el carrito.
- `pages/pacientes.php`: Módulo CRUD de pacientes y ficha de detalles.
- `pages/facturas.php`: Administración del carrito de compras y consulta del historial de facturas.

## 3. Funcionamiento del servidor y sesiones

### Enrutamiento
`index.php` ejecuta `ob_start()` al inicio para permitir redirecciones con `header()` después de cargar la lógica, incluye `config/sesion.php` y `components/header.php`, y selecciona la vista dentro de `pages/` mediante un `switch` sobre `$_GET['page']`. Si no se recibe parámetro, muestra la pantalla de bienvenida.

### Autenticación y estado de sesión
`config/sesion.php` inicializa la sesión con `session_start()` si no está activa y administra las siguientes variables:
- `$_SESSION['id_usuario']`: ID del usuario autenticado.
- `$_SESSION['usuario']`: Nombre de usuario mostrado en el encabezado.
- `$_SESSION['veterinario']`: Indicador booleano (`1` o `0`) del rol de veterinario.
- `$_SESSION['carrito']`: Arreglo asociativo con los conceptos pendientes de facturar.

### Carrito unificado (productos y servicios)
Para almacenar productos y servicios en el mismo carrito sin colisión de identificadores, cada elemento usa una clave con prefijo:
- `prod_<id>` para productos (guarda `stock` para limitar la cantidad máxima).
- `serv_<id>` para servicios (`stock` queda en `null`).

Al ejecutar `finalizar_venta`, el sistema inserta el registro en `facturas`, recorre `$_SESSION['carrito']` para insertar cada renglón en `detalle_facturas` (usando `id_producto` o `id_servicio` según el tipo) y ejecuta un `UPDATE` en `productos` para descontar el stock únicamente cuando el concepto es un producto.

## 4. Operaciones CRUD y seguridad

- **Consultas preparadas:** Todas las operaciones de alta, edición, baja lógica, inicio de sesión y registro utilizan `prepare()` y `bind_param()` de `mysqli` para prevenir inyección SQL.
- **Borrado lógico:** En `pacientes`, `productos` y `servicios`, eliminar un registro ejecuta `UPDATE ... SET activo = 0` en lugar de `DELETE`, evitando romper claves foráneas para no afectar facturas anteriores.
- **Escapado de salida:** Los valores dinámicos impresos en HTML se escapan con `htmlspecialchars()`.
- **Interfaz modal nativa:** Las ventanas de alta, edición y visualización en los módulos CRUD utilizan los atributos nativos `popover` y `popovertarget` de HTML.

## 5. Base de datos (`lets_go_vet`)

### Tablas

Tablas principales:
- `usuario`: Cuentas de acceso y si es veterinario.
- `pacientes`: Datos de la mascota y del dueño.
- `productos`: Código, nombre, precio y stock.
- `servicios`: Nombre, duración, precio y estado.
- `facturas` y `detalle_facturas`: Registro de ventas y sus conceptos (productos o servicios).

### Encabezados de las tablas
- `usuario`: `id_usuario` (PK), `usuario` (UNIQUE), `contrasenia`, `veterinario?`, `activo`, `creado_en`.
- `pacientes`: `id_paciente` (PK), `id_usuario` (FK), `nombre`, `edad`, `especie`, `raza`, `peso`, `diagnostico`, `nombre_dueno`, `telefono_dueno`, `activo`, `creado_en`.
- `productos`: `id_producto` (PK), `id_codigo_de_barras` (UNIQUE), `producto`, `precio`, `stock`, `es_medicina`, `es_para_venta`, `activo`, `creado_en`.
- `servicios`: `id_servicio` (PK), `servicio`, `duracion`, `descripcion`, `tipo_cobro`, `precio`, `activo`, `creado_en`.
- `facturas`: `id_factura` (PK), `id_usuario` (FK nullable), `id_paciente` (FK nullable), `total`, `fecha`.
- `detalle_facturas`: `id_detalle` (PK), `id_factura` (FK), `id_producto` (FK nullable), `id_servicio` (FK nullable), `cantidad`, `precio_unitario`, `subtotal`.
- `bitacora_stock`: `id_bitacora` (PK), `id_producto`, `producto`, `stock_antes`, `stock_nuevo`, `modificado_en`.

### Objetos SQL incluidos
- **Trigger `trg_cambio_stock`:** Se ejecuta después de cada `UPDATE` en `productos`; si el valor de `stock` cambió, registra el stock anterior y el nuevo en `bitacora_stock`.
- **Vista `vista_inventario_estado`:** Lista los productos activos calculando su estado (`Agotado`, `Stock Bajo` o `Disponible`).
- **Procedimiento `sp_registrar_producto`:** Inserta productos dentro de una transacción validando que precio y stock no sean negativos.
- **Función `fn_estado_stock`:** Recibe un entero de stock y devuelve la etiqueta de estado correspondiente.

## 6. Instalación en local (XAMPP)
1. Copiar la carpeta del proyecto en `C:\xampp\htdocs\ProyectoIntegrador`.
2. Abrir XAMPP e iniciar **Apache** y **MySQL**.
3. Entrar a `http://localhost/phpmyadmin`, crear la base de datos **`lets_go_vet`** y seleccionarla.
4. Ir a la pestaña **Importar** y subir el archivo `ADA5/config/lets_go_vet.sql`.
5. Revisar los datos de conexión en `ADA5/config/conectionDB.php`:
   ```php
   $conection = new mysqli("localhost", "proyecto_desarrollo", "", "lets_go_vet");