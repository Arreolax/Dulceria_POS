<!DOCTYPE HTML>
<html>

<head>
  <link rel="icon" type="image/png" href="images/favicon.png">
  <title>◤ Facturas - Sugar Rush ◢</title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
  <link rel="stylesheet" href="assets/css/main.css" />
</head>

<?php
session_start();
?>

<body class="is-preload">
  <div id="page-wrapper">
    <header id="header">
      <div class="logo container">
        <div>
          <p>Nueva Factura</p>
        </div>
      </div>
    </header>

    <?php require_once __DIR__ . '/header.php'; ?>

    <main style="font-family: Arial, sans-serif; background: #f5f7fb; padding: 2rem;">
      <section>
        <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 2rem; max-width: 1200px; margin: auto;">

          <form id="form-fact" onsubmit="return false;"
            style="background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); overflow: hidden;">

            <div style="padding: 2rem;">
              <h2>Generar Factura</h2>


              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem;">

                <label style="font-weight:500;">Seleccionar Cliente</label>

                <button id="btnNuevoCliente"
                  style="background:#5b2eff; font-size:14px; color:white; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">
                  + Nuevo Cliente
                </button>

              </div>
              <select id="select-cliente" style="width:100%; padding:.6rem; margin-bottom:1rem;">
                <option value="">Selecciona cliente</option>
              </select>

              <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem;">

                <div>
                  <label style="font-size:.8rem; color:#374151;">RFC</label>
                  <input id="fact-rfc" disabled style="width:100%;">
                </div>

                <div>
                  <label style="font-size:.8rem; color:#374151;">Razón social</label>
                  <input id="fact-razon" disabled style="width:100%;">
                </div>

                <div>
                  <label style="font-size:.8rem; color:#374151;">Correo electrónico</label>
                  <input id="fact-correo" disabled style="width:100%;">
                </div>

                <div>
                  <label style="font-size:.8rem; color:#374151;">Código Postal</label>
                  <input id="fact-cp" disabled style="width:100%;">
                </div>

                <div>
                  <label style="font-size:.8rem; color:#374151;">Régimen Fiscal</label>
                  <input id="fact-regimen" disabled style="width:100%;">
                </div>

                <div>
                  <label style="font-size:.8rem; color:#374151;">Uso de CFDI</label>
                  <input id="fact-usocfdi" disabled style="width:100%;">
                </div>

              </div>

              <hr style="margin:1.5rem 0;">

              <label>Seleccionar Ticket</label>
              <select id="select-ticket" style="width:100%; padding:.6rem; margin-bottom:1rem;">
                <option value="">Selecciona ticket</option>
              </select>

              <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem;">

                <div>
                  <label style="font-size:.8rem; color:#374151;">Folio</label>
                  <input id="ticket-folio" disabled style="width:100%;">
                </div>

                <div>
                  <label style="font-size:.8rem; color:#374151;">Total</label>
                  <input id="ticket-total" disabled style="width:100%;">
                </div>

                <div>
                  <label style="font-size:.8rem; color:#374151;">Fecha de venta</label>
                  <input id="ticket-fecha" disabled style="width:100%;">
                </div>

                <div>
                  <label style="font-size:.8rem; color:#374151;">Método de pago</label>
                  <input id="ticket-metodo" disabled style="width:100%;">
                </div>

                <div style="grid-column: span 2;">
                  <label style="font-size:.8rem; color:#374151;">Cliente</label>
                  <input id="ticket-cliente" disabled style="width:100%;">
                </div>

              </div>

              <div style="margin-top:1.5rem;">
                <button id="btn-generar-fact"
                  style="width: 100%; background: #4f46e5; color: white; padding: .8rem; border-radius: 10px;">
                  Generar factura
                </button>
              </div>
            </div>
          </form>

          <aside>
            <div style="background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08);">
              <div style="padding: 2rem;">
                <h2>Resultado</h2>
                <div id="fact-resumen">Esperando...</div>
              </div>
            </div>
          </aside>

        </div>
      </section>
    </main>

    <div id="modalCliente" style="
  display:none;
  position:fixed;
  top:0; left:0;
  width:100%; height:100%;
  background:rgba(0,0,0,0.6);
  justify-content:center;
  align-items:center;
  z-index:999;
">

      <div style="
    background:#fff;
    border-radius:14px;
    width:100%;
    max-width:520px;
    max-height:90vh;
    display:flex;
    flex-direction:column;
    box-shadow:0 20px 40px rgba(0,0,0,0.2);
    overflow:hidden;
  ">

        <!-- HEADER -->
        <div style="
      padding:16px 20px;
      border-bottom:1px solid #eee;
      display:flex;
      justify-content:space-between;
      align-items:center;
    ">
          <h3 style="margin:0; color:#5b2eff;">Nuevo Cliente</h3>
          <button onclick="cerrarModal()" style="border:none; background:none; font-size:18px; cursor:pointer;">✖</button>
        </div>

        <!-- BODY -->
        <form id="formCliente" style=" padding:20px; overflow-y:auto; display:grid; gap:14px;">

          <div>
            <label style="font-size:12px; color:#374151; font-weight:500;">RFC</label>
            <input id="cliente-rfc" style="width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db;">
          </div>

          <div>
            <label style="font-size:12px; color:#374151; font-weight:500;">Razón Social</label>
            <input id="cliente-razon" style="width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db;">
          </div>

          <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
            <div>
              <label style="font-size:12px; color:#374151; font-weight:500;">Uso CFDI</label>
              <input id="cliente-uso" placeholder="G03, S01"
                style="width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db;">
            </div>

            <div>
              <label style="font-size:12px; color:#374151; font-weight:500;">Régimen Fiscal</label>
              <input id="cliente-regimen" placeholder="616"
                style="width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db;">
            </div>
          </div>

          <div>
            <label style="font-size:12px; color:#374151; font-weight:500;">Email</label>
            <input id="cliente-email" type="email"
              style="width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db;">
          </div>

          <div>
            <label style="font-size:12px; color:#374151; font-weight:500;">Dirección</label>
            <textarea id="cliente-direccion"
              style="width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db; min-height:80px;"></textarea>
          </div>

          <div>
            <label style="font-size:12px; color:#374151; font-weight:500;">Código Postal</label>
            <input id="cliente-cp"
              style="width:100%; padding:8px; border-radius:6px; border:1px solid #d1d5db;">
          </div>

          <button onclick="cerrarModal()" style=" background:#e5e7eb; border:none; padding:8px 12px; border-radius:6px; cursor:pointer;">
            Cancelar
          </button>

          <button id="btnGuardarCliente" type="submit" style="background:#5b2eff; color:white; border:none; padding:10px; border-radius:6px;">
            Guardar Cliente
          </button>

        </form>

          

      </div>
    </div>
  </div>



  <?php require_once __DIR__ . '/footer.php'; ?>

  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/jquery.dropotron.min.js"></script>
  <script src="assets/js/jquery.scrolly.min.js"></script>
  <script src="assets/js/browser.min.js"></script>
  <script src="assets/js/breakpoints.min.js"></script>
  <script src="assets/js/util.js"></script>
  <script src="assets/js/main.js"></script>

  <script src="js/facturacion.js"></script>
</body>

</html>