<!DOCTYPE HTML>
<html>

<head>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <title>◤ Productos - Sugar Rush ◢</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script src="js/products.js"></script>
</head>
<?php
session_start();
?>

<body class="is-preload">
    <div id="page-wrapper">
        <header id="header">
            <div class="logo container">
                <div>
                    <p>Productos</p>
                </div>
            </div>
        </header>

        <?php require_once __DIR__ . '/header.php'; ?>

        <?php if (!isset($_SESSION["role"]) || $_SESSION["role"] === "cliente"): ?>
            <section id="main" style="border-bottom: 0px;">
                <div class="container">
                    <div class="row" id="productosView">
                    </div>
                </div>

            <?php elseif ($_SESSION["role"] === "admin" || $_SESSION["role"] === "vendedor"): ?>
                <section id="main" style="border-bottom: 0px; padding: 1em 0 2em 0;">

                    <div style="text-align: center; padding-bottom: 2em; ">
                        <button id="btnNuevoProd">Agregar Nuevo Producto</button>
                    </div>


                    <div class="container">
                        <div class="row" id="productosView">
                        </div>
                    </div>
                <?php endif; ?>
                </section>

                <!-- Agregar producto -->
                <div id="modalProducto" class="modal oculto">
                    <div class="modal-contenido">
                        <button class="cerrar" id="btnCerrar">✖</button>

                        <h3>Agregar producto</h3>

                        <form id="formProducto" class="form-grid">

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
                                <button type="submit" class="btn-enviar">Agregar</button>
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