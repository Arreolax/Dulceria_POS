<!DOCTYPE HTML>
<html>

<head>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <title>◤ Categorias - Sugar Rush ◢</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <script src="js/category.js"></script>
</head>
<?php
session_start();
?>

<body class="is-preload">
    <div id="page-wrapper">

        <header id="header">
            <div class="logo container">
                <div>
                    <p>Categorias</p>
                </div>
            </div>
        </header>

        <?php require_once __DIR__ . '/header.php'; ?>

        <section id="main" style="border-bottom: 0px;">
            <div class="container">
                <div id="categoriasView">
                </div>
            </div>
        </section>

        <?php require_once __DIR__ . '/footer.php'; ?>

    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.dropotron.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>