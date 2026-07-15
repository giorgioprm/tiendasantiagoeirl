<aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel2">
      <div class="image">
        <?php if (!empty($_SESSION['foto'])): ?>
          <img src="<?php echo $_SESSION['foto']; ?>" class="img-circle img-user" alt="User Image">
        <?php else: ?>
          <img src="vistas/img/demo.svg" class="img-circle img-user" alt="User Image">
        <?php endif; ?>
      </div>
      <div class="info">
        <p><?php echo $_SESSION['nombre'] ?? 'Usuario'; ?></p>
        <a href="#" class="btn btn-primary btn-sm boton-user" style="background-color: #0e6edf;">
          <i class="fas fa-user icon-user"></i> <?php echo $_SESSION['perfil'] ?? 'Perfil'; ?>
        </a>
      </div>
    </div>

    <ul class="sidebar-menu" data-widget="tree">
      <!-- INICIO -->
      <li class="menu-ini-p">
        <a href="?ruta=inicio">
          <i class="fas fa-home fa-lg bg-menu"></i>
          <span class="mg-menu">Inicio</span>
        </a>
      </li>

      <!-- ADMINISTRACIÓN -->
      <li class="treeview">
        <a href="#">
          <i class="fa fa-cog fa-lg"></i>
          <span class="mg-menu">Administración</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="?ruta=empresa"><i class="fa fa-cog fa-lg"></i> Configurar empresa</a></li>
          <li><a href="?ruta=unidad-medida"><i class="fa fa-cog fa-lg"></i> Configurar Unidad Medida</a></li>
        </ul>
      </li>

      <!-- VENTAS -->
      <li class="treeview">
        <a href="#">
          <i class="fas fa-file-invoice-dollar fa-lg"></i>
          <span class="mg-menu">Ventas</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="?ruta=crear-factura"><i class="fa fa-file-invoice"></i> Emitir factura</a></li>
          <li><a href="?ruta=crear-boleta"><i class="fa fa-file-invoice"></i> Emitir boleta</a></li>
          <li><a href="?ruta=crear-nota"><i class="fa fa-file-invoice"></i> Emitir nota de venta</a></li>
          <li><a href="?ruta=nota-credito"><i class="fa fa-file-invoice"></i> Emitir nota de crédito</a></li>
          <li><a href="?ruta=nota-debito"><i class="fa fa-file-invoice"></i> Emitir nota de débito</a></li>
        </ul>
      </li>

      <!-- COTIZACIONES -->
      <li class="treeview">
        <a href="#">
          <i class="fas fa-file-invoice fa-lg"></i>
          <span class="mg-menu">Cotizaciones</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="?ruta=crear-cotizacion"><i class="fa fa-file-invoice"></i> Crear Cotización</a></li>
          <li><a href="?ruta=listar-cotizaciones"><i class="fa fa-file-invoice"></i> Listar Cotizaciones</a></li>
        </ul>
      </li>

      <!-- RESUMEN DIARIO -->
      <li class="treeview">
        <a href="#">
          <i class="fas fa-file-invoice-dollar fa-lg"></i>
          <span class="mg-menu">Resumen diario boletas</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="?ruta=resumen-diario"><i class="fa fa-file-invoice"></i> Crear resúmenes de boletas</a></li>
          <li><a href="?ruta=resumen-diario"><i class="fa fa-file-invoice"></i> Ver resúmenes de boletas</a></li>
        </ul>
      </li>

      <!-- ADMINISTRAR VENTAS -->
      <li>
        <a href="?ruta=ventas">
          <i class="fas fa-users fa-lg"></i>
          <span class="mg-menu">Administrar ventas</span>
        </a>
      </li>

      <!-- REPORTES -->
      <li class="treeview">
        <a href="#">
          <i class="fas fa-file-invoice-dollar fa-lg"></i>
          <span class="mg-menu">Reportes</span>
          <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
          </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="?ruta=reporte-ventas"><i class="fa fa-file-invoice"></i> Reporte de ventas</a></li>
          <li><a href="?ruta=reporte-compras"><i class="fa fa-file-invoice"></i> Reporte de compras</a></li>
        </ul>
      </li>

      <!-- COMPRAS -->
      <li>
        <a href="?ruta=reporte-compras">
          <i class="fas fa-file-invoice-dollar fa-lg"></i>
          <span class="mg-menu">Compras</span>
        </a>
      </li>

      <!-- USUARIOS -->
      <li>
        <a href="?ruta=usuarios">
          <i class="fas fa-users fa-lg"></i>
          <span class="mg-menu">Usuarios</span>
        </a>
      </li>

      <!-- CATEGORÍAS -->
      <li>
        <a href="?ruta=categorias">
          <i class="fab fa-elementor fa-lg"></i>
          <span class="mg-menu">Categorías</span>
        </a>
      </li>

      <!-- PRODUCTOS -->
      <li>
        <a href="?ruta=productos">
          <i class="fab fa-product-hunt fa-lg"></i>
          <span class="mg-menu">Productos</span>
        </a>
      </li>

      <!-- CLIENTES -->
      <li>
        <a href="?ruta=clientes">
          <i class="fas fa-users fa-lg"></i>
          <span class="mg-menu">Clientes</span>
        </a>
      </li>
    </ul>
  </section>
</aside>