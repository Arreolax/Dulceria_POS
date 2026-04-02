<nav id="nav">
  <ul>
    <li class="current"><a href="index.php">Inicio</a></li>

    <li>
      <a href="products.php">Productos</a>
    </li>

    <?php if (!isset($_SESSION["role"])): ?>
      <li style="margin-left: 47em;"><a href="register.php">Crear Cuenta</a></li>

      <li style="margin-left: -0.5em;"><a href="login.php">Iniciar sesión</a></li>

    <?php elseif ($_SESSION["role"] === "admin"): ?>
      <li><a href="inventory.php">Inventario</a></li>
      <li><a href="sales.php">Ventas</a></li>
      <li><a href="tickets.php">Tickets</a></li>
      <li><a href="facturas.php">Facturas</a></li>
      <li><a href="cart.php">Carrito</a></li>
      <li style="margin-left: 7.5em;">
        <a> Rol: <?= htmlspecialchars($_SESSION["role"]) ?> </a>
      </li>
      <li style="margin-left: -0.5em;">
        <a href="../api/logout.php">Cerrar sesión</a>
      </li>

    <?php elseif ($_SESSION["role"] === "cliente"): ?>
      <li><a href="cart.php">Carrito</a></li>
      <li style="margin-left: 40em;">
        <a> Rol: <?= htmlspecialchars($_SESSION["role"]) ?> </a>
      </li>
      <li style="margin-left: -0.5em;">
        <a href="../api/logout.php">Cerrar sesión</a>
      </li>

    <?php elseif ($_SESSION["role"] === "vendedor"): ?>
      <li><a href="inventory.php">Inventario</a></li>
      <li><a href="sales.php">Ventas</a></li>
      <li><a href="tickets.php">Tickets</a></li>
      <li><a href="cart.php">Carrito</a></li>
      <li style="margin-left: 14.5em;">
        <a> Rol: <?= htmlspecialchars($_SESSION["role"]) ?> </a>
      </li>
      <li style="margin-left: -0.5em;">
        <a href="../api/logout.php">Cerrar sesión</a>
      </li>

    <?php endif; ?>

  </ul>
</nav>