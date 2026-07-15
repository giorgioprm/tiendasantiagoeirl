<?php

use Controladores\ControladorEmpresa;

$emisor = ControladorEmpresa::ctrEmisor();
?>

<header class="main-header cabecera-m">
  <!-- Logo -->
  <a href="?ruta=inicio" class="logo" style="background-color: #0e6edf;">
    <span class="logo-mini">
      <img src="vistas/img/logo/<?php echo $emisor['logo'] ?? 'logo.png'; ?>" alt="" width="50px">
    </span>
    <span class="logo-lg" style="background-color: #0e6edf;">
      <b><?php echo $emisor['nombre_comercial'] ?? 'SISTEMA'; ?></b>
    </span>
  </a>

  <!-- Header Navbar -->
  <nav class="navbar navbar-static-top cabecera-m">
    <!-- Sidebar toggle button -->
    <button class="btn btn-success btn-menup" data-toggle="push-menu" role="button">
      <i class="fas fa-align-justify fa-lg"></i>
    </button>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <!-- Notificaciones -->
        <li class="dropdown notifications-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="far fa-bell"></i>
            <span class="label label-warning no-enviados"></span>
          </a>
          <ul class="dropdown-menu">
            <li class="header no-enviados-text"></li>
            <li>
              <ul class="menu">
                <li>
                  <a href="#">
                    <i class="fa fa-users text-aqua"></i>
                    <span class="no-enviados-items"></span>
                  </a>
                </li>
              </ul>
            </li>
            <li class="footer"><a href="?ruta=ventas">Ver todos</a></li>
          </ul>
        </li>

        <!-- User Account -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?php if (!empty($_SESSION['foto'])): ?>
              <img src="<?php echo $_SESSION['foto']; ?>" class="user-image" alt="User Image">
            <?php else: ?>
              <img src="vistas/img/man_default.svg" class="user-image" alt="User Image">
            <?php endif; ?>
            <span class="hidden-xs"><?php echo $_SESSION['nombre'] ?? 'Usuario'; ?></span>
          </a>
          <ul class="dropdown-menu menu-user" style="width: 200px; color:black;">
            <li>
              <a href="?ruta=usuarios">
                <i class="fas fa-user fa-lg" style="color: #0e6edf;"></i>
                <span class="mg-menu">Mi perfil</span>
              </a>
            </li>
            <li>
              <a href="?ruta=empresa">
                <i class="fas fa-cog fa-lg" style="color: #0e6edf;"></i>
                <span class="mg-menu">Configurar empresa</span>
              </a>
            </li>
            <li>
              <a href="?ruta=salir">
                <i class="fas fa-sign-out-alt fa-lg" style="color:tomato;"></i>
                <span class="mg-menu">Salir</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>