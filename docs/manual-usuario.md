# Manual de Usuario — Let's go vet

Este documento explica cómo utilizar las funciones principales del sistema web de gestión veterinaria **Let's go vet**.

## 1. Acceso y navegación

En la parte superior de todas las pantallas se muestra el logotipo de la clínica, el estado de la sesión actual y el menú de navegación con cinco secciones:

- **Inicio:** Pantalla principal con la presentación del sistema.
- **Servicios:** Administración de procedimientos veterinarios.
- **Productos:** Control de inventario y artículos en venta.
- **Pacientes:** Registro de mascotas y datos de sus dueños.
- **Facturas / Carrito:** Carrito de cobro activo e historial de facturas generadas.

El contador entre paréntesis en **Facturas / Carrito** se actualiza automáticamente conforme se agregan o quitan productos y servicios.

## 2. Inicio de sesión y registro

Por defecto el sistema permite navegar como **Invitado**, pero para registrar qué usuario atiende las ventas se puede iniciar sesión desde el enlace **Iniciar sesión** en el encabezado.

### Iniciar sesión
1. Escribe tu nombre de usuario y contraseña.
2. Presiona el botón **Iniciar sesión**.
3. Si los datos son correctos, el sistema te llevará al módulo de productos y mostrará tu usuario en la barra superior.

Cuentas de prueba registradas:
- Usuario: `brito` | Contraseña: `1234`
- Usuario: `israel` | Contraseña: `1234`

### Registrar un nuevo usuario
1. En la parte inferior de la pantalla de inicio de sesión, ingresa el nuevo nombre de usuario y su contraseña.
2. Marca la casilla **¿Es veterinario?** si corresponde.
3. Presiona **Registrarse**. La sesión quedará iniciada automáticamente.

Para salir de la cuenta, haz clic en **Cerrar sesión** en el encabezado.

## 3. Gestión de servicios

Este módulo está pensado para el control interno de los procedimientos que ofrece la clínica. En la parte superior se muestra el total de servicios registrados y cuántos están activos.

- **Registrar servicio:** Haz clic en **+ Registrar servicio**, llena el nombre, la duración estimada (por ejemplo, `30 min`) y el precio, y presiona **Guardar**.
- **Cobrar servicio:** Presiona **Añadir al carrito** en la fila del servicio. Si el servicio está marcado como `Inactivo`, el botón aparecerá deshabilitado.
- **Editar o cambiar estado:** Presiona **Editar** para modificar los datos del servicio o cambiar su estado entre `Activo` e `Inactivo`. También hay un botón rápido de **Desactivar** / **Activar** dentro de la misma ventana para no borrar servicios con historial.

## 4. Productos

Permite gestionar el inventario de medicamentos, insumos, alimentos y accesorios.

- **Agregar producto:** Haz clic en **Agregar producto**, captura el código de barras, nombre del producto, precio y stock inicial, y presiona **Guardar**.
- **Añadir al carrito:** Indica la cantidad deseada en el recuadro de la columna **Carrito** y presiona **Añadir al carrito**. El sistema no permite superar el stock disponible y deshabilita el botón si el stock es `0`.
- **Editar o eliminar:** Presiona **editar** para actualizar código, nombre, precio o stock. Dentro de la misma ventana está el botón **Eliminar**, que desactiva el producto para que deje de aparecer en el listado.

## 5. Pacientes

Administra el expediente básico de cada mascota y el contacto de su dueño.

- **Agregar paciente:** Haz clic en **Agregar paciente** y captura nombre, edad, especie, raza, peso (kg), diagnóstico, nombre del dueño y teléfono. Presiona **Guardar**.
- **Ver detalles:** Presiona **ver** en la fila de cualquier paciente para consultar su ficha completa (especie, raza, peso, dueño y teléfono).
- **Editar o eliminar:** Presiona **editar** para actualizar los datos de la mascota o del dueño, o utiliza el botón **Eliminar** para dar de baja el registro.

## 6. Facturas y carrito de compras

Esta sección se divide en el carrito actual y el historial de ventas.

### Carrito activo
- Muestra cada concepto agregado indicando si es **Producto** o **Servicio**, su precio unitario, cantidad y subtotal, así como el total general.
- Para cambiar la cantidad de un renglón, ajusta el número y presiona **Modificar**.
- Para quitar un concepto usa **Eliminar**, o usa **Vaciar carrito** para limpiarlo por completo.

### Generar una factura
1. En el selector **Paciente / Dueño (opcional)** elige una mascota registrada o deja la opción **Venta de mostrador**.
2. Presiona **Generar factura**.
3. La venta se guarda en la base de datos, se descuenta el stock únicamente de los productos vendidos y el carrito queda vacío.

### Historial de facturas
En la parte inferior aparece la tabla con todas las facturas registradas, ordenadas de la más reciente a la más antigua, mostrando folio, fecha, usuario que atendió, paciente, dueño y total.