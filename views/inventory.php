<!DOCTYPE HTML>
<html>

<head>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <title>◤ Inventario - Sugar Rush ◢</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script src="js/inventory.js"></script>
</head>
<?php
session_start();

if ($_SESSION["role"] != "admin") {
    header("Location: index.php");
    exit;
}
?>

<body class="is-preload">
    <div id="page-wrapper">
        <header id="header">
            <div class="logo container">
                <div>
                    <p>Inventario</p>
                </div>
            </div>
        </header>

        <?php require_once __DIR__ . '/header.php'; ?>

        <section id="main" style="border-bottom: 0px;">
            <div class="tk-container">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h2 class="tk-title">Inventario</h2>

                    <button id="btnNuevoProd"
                        style="background:#5b2eff; color:white; font-size:14px;  border:none; padding:10px 15px; border-radius:5px; cursor:pointer;">
                        + Agregar Producto
                    </button>
                </div>


                <!-- Tabla -->
                <div style="background:white; border-radius:10px; padding:15px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">

                    <table class="tk-table" style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:14px;">

                        <thead>
                            <tr style="color:white;">
                                <th style="padding:10px; border:1px solid #ddd;">ID</th>
                                <th style="padding:10px; border:1px solid #ddd;">Nombre</th>
                                <th style="padding:10px; border:1px solid #ddd;">Descripcion</th>
                                <th style="padding:10px; border:1px solid #ddd;">Categoria</th>
                                <th style="padding:10px; border:1px solid #ddd;">Precio</th>
                                <th style="padding:10px; border:1px solid #ddd;">Stock</th>
                                <th style="padding:10px; border:1px solid #ddd;">Estado</th>
                                <th style="padding:10px; border:1px solid #ddd;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody id="ProductosView">
                            
                        </tbody>

                    </table>

                </div>
            </div>
        </section>

        <!-- Agregar / Editar producto -->
<div id="modalProducto" class="modal oculto">
    <div class="modal-contenido">
        <button class="cerrar" id="btnCerrar">✖</button>

        <h3 id="tituloModalProducto">Agregar producto</h3>

        <form id="formProducto" class="form-grid">

            <!-- 🔥 NUEVO -->
            <input type="hidden" name="id_producto" id="id_producto">

            <label>
                Nombre
                <input type="text" name="nombre" required>
            </label>

            <label>
                Descripción
                <input type="text" name="descripcion" required>
            </label>

            <label>
                Precio
                <input type="number" name="precio" step="0.01" required>
            </label>

            <label>
                Stock
                <input type="number" name="stock" required>
            </label>

            <label>
                Categoría
                <select name="id_categoria" id="selectCategorias" required>
                    <option value="">Selecciona una categoría</option>
                </select>
            </label>

            <label>
                Disponible
                <select name="activo" required>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </label>

            <div class="acciones full">
                <button type="submit" class="btn-enviar" id="btnSubmitProducto">Agregar</button>
            </div>
        </form>
    </div>
</div>


        <?php require_once __DIR__ . '/footer.php'; ?>

    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.dropotron.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>