-- =========================================================
-- DB: Dulceria
-- Tablas: usuarios, productos, carrito, venta, venta_detalle, categorias,
-- =========================================================
CREATE DATABASE IF NOT EXISTS Dulceria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE Dulceria;

-- -------------------------
-- usuarios
-- -------------------------
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  direccion VARCHAR(255),
  telefono VARCHAR(20) UNIQUE,
  rol VARCHAR(40) NOT NULL DEFAULT 'cliente',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB;

-- -------------------------
-- categorias
-- -------------------------
CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(180) NOT NULL,
  descripcion TEXT,
  imagen VARCHAR(255),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (nombre)
) ENGINE = InnoDB;

-- -------------------------
-- productos
-- -------------------------
CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(60) NOT NULL UNIQUE DEFAULT (
    CONCAT(
      'PRD-',
      SUBSTRING(REPLACE(UUID(), '-', ''), 1, 6)
    )
  ),
  nombre VARCHAR(180) NOT NULL,
  descripcion TEXT,
  id_categoria INT NOT NULL,
  precio DECIMAL(10, 2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  imagen VARCHAR(255),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_producto_categoria FOREIGN KEY (id_categoria) REFERENCES categorias(id) ON DELETE RESTRICT,
  INDEX (nombre),
  INDEX (activo)
) ENGINE = InnoDB;

-- -------------------------
-- carrito (ítems por usuario)
-- (Si el producto ya existe en carrito, se actualiza cantidad desde la API)
-- -------------------------
CREATE TABLE IF NOT EXISTS carrito (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_carrito_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_carrito_producto FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE RESTRICT,
  UNIQUE KEY uq_carrito_usuario_producto (id_usuario, id_producto),
  INDEX (id_usuario)
) ENGINE = InnoDB;

-- -------------------------
-- venta (cabecera)
-- -------------------------
CREATE TABLE IF NOT EXISTS venta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  folio VARCHAR(40) NOT NULL UNIQUE DEFAULT (
    CONCAT('VT-', SUBSTRING(REPLACE(UUID(), '-', ''), 1, 6))
  ),
  id_usuario INT NOT NULL,
  metodo_pago VARCHAR(40) NOT NULL,
  total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  estatus VARCHAR(40) NOT NULL DEFAULT 'CREADA',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_venta_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE RESTRICT,
  INDEX (id_usuario),
  INDEX (created_at)
) ENGINE = InnoDB;

-- -------------------------
-- venta_detalle (líneas)
-- -------------------------
CREATE TABLE IF NOT EXISTS venta_detalle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10, 2) NOT NULL,
  subtotal DECIMAL(10, 2) NOT NULL,
  CONSTRAINT fk_venta_detalle_venta FOREIGN KEY (id_venta) REFERENCES venta(id) ON DELETE CASCADE,
  CONSTRAINT fk_venta_detalle_producto FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE RESTRICT,
  INDEX (id_venta),
  INDEX (id_producto)
) ENGINE = InnoDB;

-- -------------------------
-- ticket 
-- -------------------------
CREATE TABLE IF NOT EXISTS ticket (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_venta INT NOT NULL,
  id_usuario INT NOT NULL,
  total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ticket_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE RESTRICT,
  CONSTRAINT fk_ticket_venta FOREIGN KEY (id_venta) REFERENCES venta(id) ON DELETE CASCADE,
  INDEX idx_ticket_usuario (id_usuario),
  INDEX idx_ticket_created_at (created_at)
) ENGINE = InnoDB;

-- -------------------------
-- ticket_detalle  
-- -------------------------
CREATE TABLE IF NOT EXISTS ticket_detalle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ticket INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10, 2) NOT NULL,
  subtotal DECIMAL(10, 2) NOT NULL,
  CONSTRAINT fk_ticket_detalle_venta FOREIGN KEY (id_ticket) REFERENCES ticket(id) ON DELETE CASCADE,
  CONSTRAINT fk_ticket_detalle_producto FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE RESTRICT,
  INDEX (id_ticket),
  INDEX (id_producto)
) ENGINE = InnoDB;

-- -------------------------
-- clientes_facturas
-- -------------------------
CREATE TABLE IF NOT EXISTS clientes_facturacion (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rfc VARCHAR(20) NOT NULL,
  razon_social VARCHAR(255) NOT NULL,
  uso_cfdi VARCHAR(10) DEFAULT 'G03',
  email VARCHAR(180),
  direccion TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_rfc (rfc)
) ENGINE = InnoDB;

-- -------------------------
-- Facturas
-- -------------------------
CREATE TABLE IF NOT EXISTS facturas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_ticket INT NOT NULL,
  id_cliente INT NOT NULL,
  folio VARCHAR(50) NOT NULL,
  uuid VARCHAR(50) DEFAULT NULL,
  facturama_id VARCHAR(100) DEFAULT NULL,
  serie VARCHAR(20) DEFAULT NULL,
  subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  impuestos DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  metodo_pago VARCHAR(10) DEFAULT NULL,
  forma_pago VARCHAR(10) DEFAULT NULL,
  uso_cfdi VARCHAR(10) DEFAULT NULL,
  xml_path TEXT,
  pdf_path TEXT,
  fecha_emision DATETIME NOT NULL,
  estado VARCHAR(20) NOT NULL DEFAULT 'generada',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT fk_factura_ticket FOREIGN KEY (id_ticket) REFERENCES ticket(id) ON DELETE RESTRICT,
  CONSTRAINT fk_factura_cliente FOREIGN KEY (id_cliente) REFERENCES clientes_facturacion(id) ON DELETE RESTRICT,

  UNIQUE KEY uq_factura_ticket (id_ticket),
  INDEX idx_factura_cliente (id_cliente),
  INDEX idx_factura_fecha (fecha_emision)
) ENGINE = InnoDB;

-- -------------------------
-- Facturas_detalle
-- -------------------------
CREATE TABLE IF NOT EXISTS factura_detalles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_factura INT NOT NULL,
  id_ticket_detalle INT NOT NULL,
  id_producto INT NOT NULL,
  descripcion VARCHAR(255) NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10, 2) NOT NULL,
  importe DECIMAL(10, 2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_factura_detalle_factura FOREIGN KEY (id_factura) REFERENCES facturas(id) ON DELETE CASCADE,
  CONSTRAINT fk_factura_detalle_ticket FOREIGN KEY (id_ticket_detalle) REFERENCES ticket_detalle(id) ON DELETE RESTRICT,
  CONSTRAINT fk_factura_detalle_producto FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE RESTRICT,
  INDEX idx_detalle_factura (id_factura),
  INDEX idx_detalle_producto (id_producto)
) ENGINE = InnoDB;


-- -------------------------
-- Vista Facturas
-- -------------------------
CREATE OR REPLACE VIEW vista_facturas AS
SELECT 
  f.id AS id_factura,
  f.folio,
  f.uuid, 
  f.total,
  c.razon_social,
  t.id AS id_ticket
FROM facturas f
JOIN clientes_facturacion c ON c.id = f.id_cliente
JOIN ticket t ON t.id = f.id_ticket;

-- -------------------------
-- Vista Facturas Detalles
-- -------------------------
CREATE OR REPLACE VIEW vista_factura_detalles AS
SELECT 
    fd.id_factura AS id_factura,
    p.nombre AS producto,
    fd.cantidad AS cantidad,
    fd.precio_unitario AS precio_unitario,
    fd.importe AS importe
FROM factura_detalles fd
JOIN productos p 
    ON fd.id_producto = p.id;