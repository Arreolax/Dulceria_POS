<!DOCTYPE HTML>
<html>

<head>
  <link rel="icon" type="image/png" href="images/favicon.png">
  <title>◤ Facturas - Sugar Rush ◢</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
  <link rel="stylesheet" href="assets/css/main.css" />
  <script src="js/facturas.js"></script>
</head>
<?php
session_start();
?>

<body class="is-preload">
  <div id="page-wrapper">
    <header id="header">
      <div class="logo container">
        <div>
          <p>Facturas</p>
        </div>
      </div>
    </header>

    <?php require_once __DIR__ . '/header.php'; ?>

    <section id="main" style="border-bottom: 0px;">
      <div class="tk-container">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
          <h2 class="tk-title">Facturas</h2>
 
          <button id="btnNuevoFact" onclick="window.location.href='facturacion.php'"
            style="background:#5b2eff; font-size:14px; color:white; border:none; padding:10px 15px; border-radius:5px; cursor:pointer;">
            + Nueva Factura
          </button>
        </div>

          <table style="width: 100%; border-collapse: collapse; box-shadow: 0 4px 8px rgba(0,0,0,0.1); font-size: 14px;" >

            <thead style="background-color: #1f2937; color: white;">
              <tr>
                <th style="padding: 10px;">Folio</th>
                <th style="padding: 10px;">Fecha</th>
                <th style="padding: 10px;">Razón Social</th>
                <th style="padding: 10px;">Método Pago</th>
                <th style="padding: 10px;">Forma Pago</th>
                <th style="padding: 10px;">Uso CFDI</th>
                <th style="padding: 10px;">Subtotal</th>
                <th style="padding: 10px;">Impuestos</th>
                <th style="padding: 10px;">Total</th>
                <th style="padding: 10px;">Estado</th>
                <th style="padding: 10px;">Acciones</th>
              </tr>
            </thead>

            <tbody id="FacturasView">
            </tbody>

          </table>

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