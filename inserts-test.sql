-- -------------------------
-- usuarios
-- -------------------------

INSERT INTO usuarios 
(id, nombre, email, password_hash, direccion, telefono, rol, created_at)
VALUES
(1, 'Juan Pérez', 'juan.perez@dulceria.com', '$2y$10$hashjuan', 'Av. Central 123', '5512345678', 'vendedor', NOW()),
(2, 'María López', 'maria.lopez@dulceria.com', '$2y$10$hashmaria', 'Calle Dulce 45', '5587654321', 'cliente', NOW()),
(3, 'Carlos Ramírez', 'carlos.ramirez@dulceria.com', '$2y$10$hashcarlos', 'Col. Centro #89', '5544332211', 'vendedor', NOW()),
(4, 'Ana Torres', 'ana.torres@dulceria.com', '$2y$10$hashana', 'Av. Azúcar 200', '5599887766', 'cliente', NOW()),
(5, 'Alejandro Arreola', 'admin@dulceria.com', 'admin', 'Av. Azúcar 200', '5599887760', 'admin', NOW());
-- -------------------------

-- -------------------------
-- categorias
-- -------------------------

INSERT INTO categorias (id, nombre, descripcion, imagen, created_at) VALUES
(1,'Paletas','Dulces en presentación de paleta',NULL,NOW()),
(2,'Chicles','Dulces masticables',NULL,NOW()),
(3,'Chocolates','Productos derivados del cacao',NULL,NOW()),
(4,'Gomitas','Dulces suaves y gelatinosos',NULL,NOW()),
(5,'Caramelos','Caramelos duros tradicionales',NULL,NOW()),
(6,'Malvaviscos','Dulces esponjosos',NULL,NOW()),
(7,'Tamarindos','Dulces de tamarindo',NULL,NOW()),
(8,'Tradicionales','Dulces mexicanos tradicionales',NULL,NOW()),
(9,'Test_Category_Name','Test_Category_Description',NULL,NOW());

-- -------------------------
-- productos
-- -------------------------

INSERT INTO productos
(id, nombre, descripcion, id_categoria, precio, stock, activo, imagen, created_at)
VALUES
(1, 'Paleta de Fresa', 'Paleta dulce sabor fresa', 1, 85.00, 120, 1,
 'https://m.media-amazon.com/images/I/81BS82xg1uL._AC_UF894,1000_QL80_.jpg',
 NOW()),

(2, 'Paleta de Mango', 'Paleta dulce sabor mango', 1, 85.00, 100, 1,
 'https://m.media-amazon.com/images/I/817Povmme6L.jpg',
 NOW()),

(3, 'Chicle Globo', 'Chicle inflable sabor tutti frutti', 2, 60.00, 300, 1,
 'https://m.media-amazon.com/images/I/61j5T5kqNXL._AC_UF1000,1000_QL80_.jpg',
 NOW()),

(4, 'Chocolate con Leche', 'Barra de chocolate con leche 90g', 3, 180.00, 80, 1,
 'https://i5.walmartimages.com.mx/mg/gm/3pp/asr/1f7cf54e-e7d3-4851-b210-77874d64be1a.25864d36da87c60bb0a37f9637cf0492.jpeg',
 NOW()),

(5, 'Gomitas Osito', 'Gomitas de sabores frutales', 4, 60.00, 150, 1,
 'https://dulceriasvazquez.com/cdn/shop/files/85_dacda056-c2bf-41b2-b403-8575e538746f.png',
 NOW()),

(6, 'Caramelo Macizo', 'Caramelo duro sabor limón', 5, 75.00, 500, 1,
 'https://m.media-amazon.com/images/I/71hyYFK67nL._AC_UF894,1000_QL80_.jpg',
 NOW()),

(7, 'Malvavisco', 'Malvavisco suave sabor vainilla', 6, 60.00, 200, 1,
 'https://i5.walmartimages.com/asr/0af78b2b-8255-421a-9dc1-e5acce3528d0.fe9173383ad0800922d1790857a78b1b.jpeg',
 NOW()),

(8, 'Pulparindo', 'Dulce de tamarindo picante', 7, 200.00, 180, 1,
 'https://m.media-amazon.com/images/I/71bIRTrnN6L._AC_UF894,1000_QL80_.jpg',
 NOW()),

(9, 'Mazapán', 'Mazapán de cacahuate tradicional', 8, 210.00, 220, 1,
 'https://dulceriatobi.shop/cdn/shop/files/MAZAPANOROGINAL30PZ2.jpg',
 NOW()),

(10, 'Chocolate Amargo', 'Chocolate 70% cacao', 3, 220.00, 60, 1,
 'https://www.soriana.com/on/demandware.static/-/Sites-soriana-grocery-master-catalog/default/dw378c44d1/images/product/9542444600_A.jpg',
 NOW());

-- -------------------------

-- -------------------------
-- carrito
-- -------------------------

INSERT INTO carrito
(id, id_usuario, id_producto, cantidad, created_at)
VALUES
(1, 1, 1, 3, NOW()),   
(2, 1, 4, 2, NOW()),   
(3, 2, 5, 4, NOW()),   
(4, 2, 9, 1, NOW()),   
(5, 3, 2, 5, NOW()),   
(6, 3, 8, 2, NOW()),  
(7, 4, 3, 6, NOW()),   
(8, 4, 6, 10, NOW());  
-- -------------------------

-- -------------------------
-- venta (cabecera)
-- -------------------------

INSERT INTO venta
(id, id_usuario, metodo_pago, total, estatus, created_at)
VALUES
(1, 1, 'Tarjeta', 42.00, 'PROCESANDO', NOW()),   
(2, 2, 'Efectivo', 55.00, 'PROCESANDO', NOW()),   
(3, 3, 'Tarjeta', 67.50, 'PAGADA', NOW()),       
(4, 4, 'Transferencia', 33.00, 'CREADA', NOW());       
-- -------------------------

-- -------------------------
-- venta_detalle (líneas)
-- -------------------------

INSERT INTO venta_detalle
(id, id_venta, id_producto, cantidad, precio_unitario, subtotal)
VALUES
-- Venta 1 
(1, 1, 1, 3, 8.50, 25.50),   
(2, 1, 4, 2, 18.00, 36.00), 

-- Venta 2 
(3, 2, 5, 4, 12.00, 48.00), 
(4, 2, 9, 1, 7.00, 7.00),   

-- Venta 3 
(5, 3, 2, 5, 8.50, 42.50),  
(6, 3, 8, 2, 10.00, 20.00), 
(7, 3, 6, 3, 1.50, 4.50),  

-- Venta 4 
(8, 4, 3, 6, 3.00, 18.00),  
(9, 4, 6, 10, 1.50, 15.00);
-- -------------------------

-- -------------------------
-- ticket (cabecera)
-- -------------------------
INSERT INTO ticket
(id, id_venta, id_usuario, total, created_at)
VALUES
(1, 1, 1, 61.50, NOW()),
(2, 2, 2, 55.00, NOW()),
(3, 3, 3, 67.00, NOW()),
(4, 4, 4, 33.00, NOW());
-- -------------------------

-- -------------------------
-- ticket_detalle (líneas)
-- -------------------------

INSERT INTO ticket_detalle
(id, id_ticket, id_producto, cantidad, precio_unitario, subtotal)
VALUES

-- Ticket 1 (Venta 1)
(1, 1, 1, 3, 8.50, 25.50),
(2, 1, 4, 2, 18.00, 36.00),

-- Ticket 2 (Venta 2)
(3, 2, 5, 4, 12.00, 48.00),
(4, 2, 9, 1, 7.00, 7.00),

-- Ticket 3 (Venta 3)
(5, 3, 2, 5, 8.50, 42.50),
(6, 3, 8, 2, 10.00, 20.00),
(7, 3, 6, 3, 1.50, 4.50),

-- Ticket 4 (Venta 4)
(8, 4, 3, 6, 3.00, 18.00),
(9, 4, 6, 10, 1.50, 15.00);
