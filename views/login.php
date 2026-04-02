<!DOCTYPE HTML>
<html>

<head>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <title>◤ Iniciar Sesion - Sugar Rush ◢</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script src="js/login.js"></script>
</head>

<?php
session_start();

if (isset($_SESSION["logged"])) {
    header("Location: index.php");
    exit;
}
?>

<body class="is-preload fondo-app">
    <nav id="nav">
  <ul>
    <li class="current"><a href="index.php">Inicio</a></li>
  </ul>
</nav>
    <div style="justify-items: center;">
        <form id="loginForm" style="width: 100%; max-width: 500px; margin: 75px auto; padding: 30px; background: #ffffff; border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15); display: flex; flex-direction: column; gap: 15px; font-family: Arial, sans-serif;">
            <img style="place-self: center; border-radius: 9999px; height: 10em; width: 10em;" src="images/user.png" alt="" />
            <h2 style=" text-align: center; margin-bottom: 10px; color: #333;">
                Iniciar Sesión
            </h2>

            <label style="display: block;font-size: 14px; color: #555; font-weight: bold; margin: 0; padding: 0; line-height: 1;">
                Correo electrónico
            </label>
            <input required id="email" type="text" placeholder="correo@ejemplo.com" style="display: block; margin: 0; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 14px; outline: none; font-weight: 700;">

            <label style="display: block;font-size: 14px; color: #555; font-weight: bold; margin: 0; padding: 0; line-height: 1;">
                Contraseña
            </label>
            <input required id="password" type="password" placeholder="••••••••" style="display: block; margin: 0; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 14px; outline: none; font-weight: 700;">

            <button type="submit" class="btn-enviar" style=" margin-top: 10px; padding: 12px; background: #5100ff; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer;">
                Ingresar
            </button>

        </form>

    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.dropotron.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>

</body>

</html>