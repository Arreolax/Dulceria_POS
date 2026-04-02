<!DOCTYPE HTML>
<html>

<head>
	<title>◤ Sugar Rush ◢</title>
	<link rel="icon" type="image/png" href="images/favicon.png">
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main.css" />
</head>
<?php session_start(); ?>

<body class="homepage is-preload">
	<div id="page-wrapper">
		<?php require_once __DIR__ . '/header.php'; ?>

		<section id="banner">
			<div class="content">
				<h2>Bienvenido a
					<span style="font-style: italic;">
						Sugar Rush
					</span>
				</h2>
				<p>Una dulceria donde puedes encontrar una gran cantidad de dulces que estés buscando</p>
				<a href="#main" class="button scrolly">Continuar</a>
			</div>
		</section>

		<section id="main" style="border-bottom: 0px;">
			<div class="container">
				<div class="row gtr-200">
					<div class="col-12">
						<section class="box features">
							<h2 class="major"><span>Categorias</span></h2>
							<div>
								<div class="row">
									<div class="col-3 col-6-medium col-12-small">
										<section class="box feature">
											<a href="#" class="image featured"><img src="images/paletas.png" alt="" /></a>
											<h3><a href="#">Paletas</a></h3>
											<p>
												Phasellus quam turpis, feugiat sit amet ornare in, a hendrerit in
												lectus dolore. Praesent semper mod quis eget sed etiam eu ante risus.
											</p>
										</section>
									</div>
									<div class="col-3 col-6-medium col-12-small">
										<section class="box feature">
											<a href="#" class="image featured"><img src="images/chocolates.png" alt="" /></a>
											<h3><a href="#">Chocolates </a></h3>
											<p>
												Phasellus quam turpis, feugiat sit amet ornare in, a hendrerit in
												lectus dolore. Praesent semper mod quis eget sed etiam eu ante risus.
											</p>
										</section>

									</div>
									<div class="col-3 col-6-medium col-12-small">
										<section class="box feature">
											<a href="#" class="image featured"><img src="images/gomitas.png" alt="" /></a>
											<h3><a href="#">Gomitas</a></h3>
											<p>
												Phasellus quam turpis, feugiat sit amet ornare in, a hendrerit in
												lectus dolore. Praesent semper mod quis eget sed etiam eu ante risus.
											</p>
										</section>

									</div>
									<div class="col-3 col-6-medium col-12-small">
										<section class="box feature">
											<a href="#" class="image featured"><img src="images/caramelos.png" alt="" /></a>
											<h3><a href="#">Caramelos</a></h3>
											<p>
												Phasellus quam turpis, feugiat sit amet ornare in, a hendrerit in
												lectus dolore. Praesent semper mod quis eget sed etiam eu ante risus.
											</p>
										</section>

									</div>
									<div class="col-12">
										<ul class="actions">
											<li><a href="categories.php" class="button large">Otras Categorias</a></li>
										</ul>
									</div>
								</div>
							</div>
						</section>
					</div>
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