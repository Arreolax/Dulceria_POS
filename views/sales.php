<!DOCTYPE HTML>
<html>

<head>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <title>◤ Ventas - Sugar Rush ◢</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script src="js/sales.js"></script>
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
                    <p>Ventas</p>
                </div>
            </div>
        </header>

        <?php require_once __DIR__ . '/header.php'; ?>

<section id="main" style="border-bottom: 0px;">
  <div class="tk-container">
    <h2 class="tk-title">Listado de Ventas</h2>

    <div class="tk-table-wrapper">
      <table class="tk-table" style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:14px;">
            <thead>
              <tr style="color:white;">
                <th style="padding:10px; border:1px solid #ddd;">Fecha</th>
                <th style="padding:10px; border:1px solid #ddd;">Folio</th>
                <th style="padding:10px; border:1px solid #ddd;">Cliente</th>
                <th style="padding:10px; border:1px solid #ddd;">Productos</th>
                <th style="padding:10px; border:1px solid #ddd;">Estado</th>
                <th style="padding:10px; border:1px solid #ddd;">Total</th>
                <th style="padding:10px; border:1px solid #ddd;">Acciones</th>
              </tr>
            </thead>

            <tbody id="VentasView">

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