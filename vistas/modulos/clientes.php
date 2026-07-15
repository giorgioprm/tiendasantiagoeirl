<?php

use Controladores\ControladorClientes;
?>
<div class="content-wrapper panel-medio-principal">
  <div style="padding:5px"></div>
  <section class="container-fluid">
    <section class="content-header dashboard-header">

      <div class="box container-fluid" style="border:0px; margin:0px; padding:0px;">
        <div class="col-lg-12 col-xs-12" style="border:0px; margin:0px; padding:0px; border-radius:10px;">

          <div class="col-md-3 hidden-sm hidden-xs">
            <button class=""><i class="fas fa-users"></i> Clientes</button>
          </div>
          <div class="col-lg-9 col-md-12 col-sm-12 btns-dash">
          </div>
        </div>
      </div>
    </section>
  </section>
  <!-- <section class="content"> -->
  <section class="container-fluid panel-medio">
    <!-- BOX INI -->
    <div class="box rounded">

      <div class="box-header ">
        <i class="fas fa-file-invoice"></i>&nbsp;
        <h3 class="box-title">Administración de clientes</h3>

        <button class="btn btn-success  pull-right btn-radius" data-toggle="modal" data-target="#modalAgregarCliente">
          <i class="fas fa-plus-square"></i>Nuevo cliente <i class="fa fa-th"></i>
        </button>

      </div>
      <!-- /.box-header -->
      <div class="box-body table-user">

        <div class="contenedor-busqueda">
          <div class="input-group-search">

            <select class="selectpicker show-tick" data-style="btn-select" data-width="70px" id="selectnum" name="selectnum" onchange="loadClientes(1)">
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>

            <div class="input-search">
              <input type="search" class="search" id="search" name="search" placeholder="Buscar" onkeyup="loadClientes(1)" style="width: 100%;">
              <span class="input-group-addo icon-search"><i class="fa fa-search"></i></span>
            </div>
          </div>
        </div>
        <input type="hidden" id="perfilOcultoc" value="<?php echo $_SESSION['perfil'] ?>">
        <!-- table-bordered table-striped  -->
        <div class="table-responsive">
          <table class="table  dt-responsive tabla-clientes tbl-t" width="100%">

            <thead>
              <tr>
                <th style="width:10px;">#</th>
                <th>Nombre</th>
                <th>RUC/DNI</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Direccion</th>
                <th>Fecha de Registro</th>
                <th width="100px">Acciones</th>
              </tr>
            </thead>

            <?php
            $clientesTable = new ControladorClientes();
            $clientesTable->ctrListarClient();

            ?>

          </table>

        </div>

      </div>

    </div>
    <!-- BOX FIN -->
    <!-- /.box-footer -->
  </section>

</div>

<!-- MODAL AGREGAR CLIENTE-->
<!-- Modal -->
<div id="modalAgregarCliente" class="modal fade modal-forms fullscreen-modalB" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">

      <form role="form" method="post" id="formClientes" autocomplete="off">

        <!-- CABEZA DEL MODAL -->

        <div class="modal-header" style="background:#0e6edf; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">AGREGAR CLIENTE</h4>

        </div>

        <!-- CUERPO DEL MODAL -->

        <div class="modal-body">

          <div class="box-body">
            <!-- ENTRADA PARA EL DNI -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>

                <input type=" number" maxlength="9" class="form-control " name="nuevoDni" id="nuevoDni" placeholder="Ingresar DNI" title="DNI">

                <div class="resultadoCliente" idCliente=""><a href="#" class="btn-add"></a></div>

              </div>

            </div>
            <!-- ENTRADA PARA EL NOMBRE -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-user"></i></span>

                <input type="text" class="form-control" name="nuevoCliente" id="nuevoCliente" placeholder="Ingresar nombre" title="Nombre">

              </div>

            </div>
            <!-- ENTRADA PARA EL RUC -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>

                <input type=" number" maxlength="12" class="form-control " name="nuevoRuc" id="nuevoRuc" placeholder="Ingresar RUC" title="RUC">

              </div>

            </div>
            <!-- ENTRADA PARA RAZÓN SOCIAL -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>

                <input type=" text" class="form-control " name="nuevoRS" id="nuevoRS" placeholder="Ingresar Razón Social" title="Razón Social">

              </div>

            </div>
            <!-- ENTRADA PARA EL EMAIL -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-at"></i></span>

                <input type="email" class="form-control " name="nuevoEmail" id="nuevoEmail" placeholder="Ingresar email" title="Correo Electrónico" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL TELÉFONO -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-phone-alt"></i></span>

                <input type="text" class="form-control" name="nuevoTelefono" id="nuevoTelefono" placeholder="Ingresar teléfono" title="Teléfono" maxlength="9" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL DIRECCIÓN -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-map-marker-alt"></i></span>

                <input type="text" class="form-control " name="nuevaDireccion" id="nuevaDireccion" placeholder="Ingresar dirección" title="Dirección" required>

              </div>

            </div>
            <!-- ENTRADA PARA FECHA NACIMIENTO -->
            <div class="form-group" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>

                <input type="text" class="form-control " name="nuevaFechaNacimiento" id="nuevaFechaNacimiento" placeholder="Ingresar fecha nacimiento">

              </div>

            </div>

          </div>

        </div>

        <!-- PIE DEL MODAL -->
        <div class="modal-footer">

          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="far fa-times-circle fa-lg"></i> Salir</button>

          <button type="submit" class="btn btn-primary">Guardar cliente</button>

        </div>

        <?php

        $crearCliente = new ControladorClientes();
        $crearCliente->ctrCrearCliente();

        ?>

      </form>

    </div>
  </div>
</div>

<!-- MODAL EDITAR CLIENTE-->
<!-- Modal -->
<div id="modalEditarCliente" class="modal fade modal-forms" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">

      <form role="form" method="post">

        <!-- CABEZA DEL MODAL -->

        <div class="modal-header" style="background:#0e6edf; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Editar Cliente</h4>

        </div>

        <!-- CUERPO DEL MODAL -->

        <div class="modal-body">

          <div class="box-body">

            <!-- ENTRADA PARA EL DNI -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>

                <input type=" number" maxlength="8" class="form-control " name="editarDni" id="editarDni" placeholder="Ingresar DNI" min="1" max="10" title="DNI">

              </div>

            </div>
            <!-- ENTRADA PARA EL NOMBRE -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-user"></i></span>

                <input type="hidden" name="id" id="id">

                <input type="text" class="form-control" name="editarCliente" id="editarCliente" placeholder="Ingresar nombre" title="Nombre">

              </div>

            </div>
            <!-- ENTRADA PARA EL RUC -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>

                <input type=" number" maxlength="12" class="form-control " name="editarRuc" id="editarRuc" placeholder="Ingresar RUC" title="RUC">

              </div>

            </div>
            <!-- ENTRADA PARA RAZÓN SOCIAL -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-address-card"></i></span>

                <input type=" text" class="form-control " name="editarRS" id="editarRS" placeholder="Ingresar Razón Social" title="Razón Social">

              </div>

            </div>
            <!-- ENTRADA PARA EL EMAIL -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-at"></i></span>

                <input type="email" class="form-control " name="editarEmail" id="editarEmail" placeholder="Ingresar email" title="Correo Electrónico" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL TELÉFONO -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-phone-alt"></i></span>

                <input type="text" class="form-control " name="editarTelefono" id="editarTelefono" placeholder="Ingresar teléfono" min="1" max="10" title="Teléfono" required>

              </div>

            </div>
            <!-- ENTRADA PARA EL DIRECCIÓN -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-map-marker-alt"></i></span>

                <input type="text" class="form-control " name="editarDireccion" id="editarDireccion" placeholder="Ingresar dirección" title="Dirección" required>

              </div>

            </div>
            <!-- ENTRADA PARA FECHA NACIMIENTO -->
            <div class="form-group" style="display:none">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-calendar-alt"></i></span>

                <input type="text" class="form-control " name="editarFechaNacimiento" id="editarFechaNacimiento" placeholder="Ingresar fecha nacimiento">

              </div>

            </div>

          </div>

        </div>

        <!-- PIE DEL MODAL -->

        <div class="modal-footer">

          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="far fa-times-circle fa-lg"></i> Salir</button>

          <button type="submit" class="btn btn-primary">Editar cliente</button>

        </div>

        <?php

        $editarCliente = new ControladorClientes();
        $editarCliente->ctrEditarCliente();

        ?>

      </form>

    </div>
  </div>
</div>