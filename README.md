# Dulcería POS (Sugar Rush) — Sistema de Punto de Venta y Facturación CFDI 4.0

Sistema web integral de **Punto de Venta (POS)**, control de inventario y **Facturación Electrónica (SAT CFDI 4.0)** diseñado para dulcerías y comercios minoristas. Desarrollado con **PHP**, **MySQL**, **JavaScript** y una arquitectura de servicios REST para garantizar velocidad, modularidad y facilidad de despliegue.

---

## Características Principales

- **Punto de Venta y Carrito de Compras:** Gestión ágil de compras con validaciones de existencias en tiempo real y cálculo automático de totales e impuestos.
- **Emisión de Tickets:** Generación de comprobantes y tickets con folios únicos automáticos.
- **Control de Inventario y Stock:** Consulta, registro y ajuste de existencias para cada producto y categoría.
- **Cancelación Segura con Procedimiento Almacenado:** Cancelación transaccional (`sp_cancelar_venta`) que revierte y restaura de forma atómica el stock en inventario.
- **Facturación CFDI 4.0 (México):**
  - Registro y gestión de clientes fiscales (RFC, Razón Social, Régimen, Uso de CFDI, Domicilio).
  - Timbrado y emisión mediante el API de **Facturama**.
  - Descarga directa de archivos **XML** y representaciones impresas en **PDF**.
  - Integración con catálogos oficiales del SAT.
- **Control de Accesos Basado en Roles (RBAC):**
  - **Administrador:** Acceso completo a inventario, usuarios, categorías, productos y reportes.
  - **Vendedor:** Operación del punto de venta, emisión de tickets y cobro.
  - **Cliente:** Visualización de catálogo, carrito y solicitud de autofacturación con ticket.

---

## Arquitectura y Tecnologías

### **Backend**
- **Lenguaje:** PHP 8.x (Arquitectura basada en endpoints REST bajo `/api`).
- **Persistencia:** MySQL / MariaDB mediante PDO con consultas preparadas y transacciones seguras.
- **Facturación:** API REST de Facturama (CFDI 4.0 / Sandbox y Producción).

### **Frontend**
- **Estructura e Interfaz:** HTML5 semántico, CSS3, SASS.
- **Librerías:** JavaScript nativo + jQuery, FontAwesome.
- **Diseño Responsivo:** Adaptado a pantallas de terminal punto de venta, laptops y dispositivos móviles.

### **Testing & Automatización**
- **Playwright Test Runner:** Pruebas de integración y UI en Node.js.
- **Python (Opcional / Utilidad):** Script `db.py` con PyMySQL para tareas auxiliares de base de datos.

---

## Requisitos Previos

Contar con las siguientes herramientas instaladas en tu entorno local:

- **PHP** >= 8.0 (con extensiones `pdo_mysql`, `curl`, `json`, `mbstring` habilitadas).
- **Servidor Web y Base de Datos:** XAMPP, Laragon, WampServer o Apache/Nginx + MariaDB/MySQL >= 8.0.
- **Node.js** >= 18.x (necesario únicamente si deseas ejecutar las pruebas con Playwright).
- **Git** para clonar el repositorio.

---

## Instalación y Configuración

### Configuración de la Base de Datos

1. Abre tu cliente MySQL preferido (phpMyAdmin, MySQL Workbench, DBeaver o la consola).
2. Ejecuta los siguientes scripts en el orden indicado:
   - `bd.sql`: Crea la base de datos `Dulceria` y todas sus tablas (`usuarios`, `categorias`, `productos`, `carrito`, `venta`, `venta_detalle`, `ticket`, `clientes_facturacion`, `facturas`, etc.).
   - `sp_cancelar.sql`: Crea el stored procedure `sp_cancelar_venta`.
   - `inserts-test.sql` *(Opcional)*: Carga los usuarios, categorías y productos iniciales de prueba.

### Configuración de Conexión y Facturama

#### Base de Datos
Edita el archivo [config/conexion.php](config/conexion.php) para ajustar las credenciales de tu servidor de base de datos:

```php
function pdo(): PDO {
  $host = "127.0.0.1";
  $db   = "Dulceria";
  $user = "root";
  $pass = ""; // Contraseña de MySQL
  ...
}
```

#### Facturama (CFDI 4.0)
El archivo [config/facturama.php](config/facturama.php) permite configurar las credenciales mediante variables de entorno o valores por defecto:

| Variable de Entorno | Descripción | Valor por Defecto |
| :--- | :--- | :--- |
| `FACTURAMA_BASE_URL` | URL de la API de Facturama | `https://apisandbox.facturama.mx` |
| `FACTURAMA_USER` | Usuario de Facturama | `facturama_user` |
| `FACTURAMA_PASS` | Contraseña / Token de Facturama | `facturama_password` |
| `FACTURAMA_EXPEDITION_PLACE` | Código Postal del lugar de expedición | `34217` |

> [!TIP]
> Para pruebas se recomienda mantener la URL de **Sandbox**. Al pasar a producción, define las variables con tus credenciales de timbrado real.

### Servidor Web

Inicia Apache y MySQL desde tu panel de control (XAMPP o Laragon) y abre tu navegador en:

```text
http://localhost/Dulceria_POS/
```

Serás redirigido automáticamente a la vista principal en `views/index.php`.

---

## Roles y Usuarios de Prueba

Si ejecutaste `inserts-test.sql`, puedes acceder con las siguientes cuentas preconfiguradas:

| Rol | Correo Electrónico | Contraseña | Permisos Principales |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin@dulceria.com` | `admin` | Inventario, productos, categorías, reportes y ventas |
| **Vendedor** | `juan.perez@dulceria.com` | `vendedor` | Registro de ventas, consulta de tickets y cobro |
| **Cliente** | `maria.lopez@dulceria.com` | `cliente` | Exploración de catálogo, carrito y facturación de tickets |

---

## Facturación Electrónica (Facturama API)

1. El cliente o cajero ingresa a la sección **Facturación** con el folio o ID de su ticket de compra.
2. Se capturan o confirman los datos fiscales: **RFC**, **Razón Social**, **Código Postal**, **Régimen Fiscal** y **Uso de CFDI**.
3. El sistema valida los datos con los catálogos del SAT (`config/sat_catalogos_4_0.json`).
4. Se envía la solicitud al API de Facturama para generar el comprobante fiscal digital.
5. El sistema almacena el `uuid`, folio fiscal y los enlaces para descargar el archivo **XML** y la representación en **PDF**.
6. Todo evento y respuesta de la API queda registrado en `files/facturas/logs/` para trazabilidad y auditoría.