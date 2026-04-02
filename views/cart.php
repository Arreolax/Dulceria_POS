<!DOCTYPE HTML>
<html>

<head>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <title>◤ Carrito - Sugar Rush ◢</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script src="js/cart.js"></script>
</head>
<?php
session_start();

if (!isset($_SESSION["logged"])) {
    header("Location: login.php");
    exit;
}
?>

<body class="is-preload">
    <div id="page-wrapper">
        <header id="header">
            <div class="logo container">
                <div>
                    <p>Carrito</p>
                </div>
            </div>
        </header>

        <?php require_once __DIR__ . '/header.php'; ?>

<section id="main" style="border-bottom: 0px;"> 
  <div class="tk-container">
    <h2 class="tk-title">Listado de productos en el carrito</h2>

    <div class="tk-table-wrapper">
      <table class="tk-table">
        <thead>
          <tr>
            <th>Imagen</th>
            <th>ID</th>
            <th>Producto</th>
            <th>Descripción</th>
            <th>Categoria</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody id="CartView"> 
        </tbody>
      </table>
    </div>
  </div>
</section>

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