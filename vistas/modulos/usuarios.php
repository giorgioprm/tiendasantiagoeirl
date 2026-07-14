<?php

use Controladores\ControladorUsuarios;

?>
<div class="content-wrapper panel-medio-principal">
  <?php
  if ($_SESSION['perfil'] == 'Vendedor' || $_SESSION['perfil'] == 'Especial') {

    echo '
      <section class="container-fluid panel-medio">
      <div class="box alert-dangers text-center">
     <div><h3> Área restringida, solo el administrador puede tener acceso</h3></div>
    <div class="img-restringido"></div>
     
     </div>
     </div>';
  } else {


  ?>
    <div style="padding:5px"></div>

    <!-- <section class="content"> -->
    <section class="container-fluid panel-medio">
      <!-- BOX INI -->
      <div class="box rounded">

        <div class="box-header ">
          <i class="fas fa-file-invoice"></i>&nbsp;
          <h3 class="box-title">Administración de Usuarios</h3>

          <?php
          if ($_SESSION['perfil'] == 'Administrador') {
          ?>
            <button class="btn btn-success  pull-right btn-radius" data-toggle="modal" data-target="#modalAgregarUsuario"><i class="fas fa-plus-square"></i>Nuevo usuario <i class="fas fa-user-plus"></i>
            </button>
          <?php } ?>

        </div>
        <!-- /.box-header -->
        <div class="box-body table-user">
          <input type="hidden" id="perfilOculto" value="<?php echo $_SESSION['perfil'] ?>">
          <!-- table-bordered table-striped  -->
          <div class="table-responsive">

            <table class="table dt-responsive tablas tbl-t" width="100%">

              <thead>
                <tr>
                  <th style="width:10px;">#</th>
                  <th>Nombre</th>
                  <th>Usuario</th>
                  <th>Foto</th>
                  <th>Rol</th>
                  <th>Estado</th>
                  <th>Último login</th>
                  <th width="100px">Acciones</th>
                </tr>
              </thead>

              <tbody>
                <?php
                if ($_SESSION['perfil'] != 'Administrador') {
                  $item = 'id';
                  $valor = $_SESSION['id'];
                  $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);
                  $value = $usuarios;
                ?>

                  <tr>
                    <td>1</td>
                    <td><?php echo $value['nombre']; ?></td>
                    <td><?php echo $value['usuario']; ?></td>

                    <?php
                    if ($value['foto'] != '') {

                      echo '<td><img src="' . $value['foto'] . '" alt="" class="img-thumbnail" width="40px"></td>';
                    } else {

                      echo '<td><img src="vistas/img/usuarios/default/man_default.svg" alt="" class="img-thumbnail" width="40px"></td>';
                    }
                    ?>
                    <!-- <td><img src="" alt="" class="img-thumbnail" width="40px"></td> -->
                    <td><?php echo $value['perfil']; ?></td>


                    <td>
                      <div class="modo-contenedor-selva">

                        <input type="checkbox" data-toggle="toggle" data-on="Activado" data-off="Desactivado" data-onstyle="success" data-offstyle="danger" id="" name="usuarioEstado<?php $value['estado'] ?>" value="<?php $value['estado'] ?>" data-size="mini" data-width="110" idUsuario="<?php echo $value['id'] ?>" <?php if ($value['estado'] == 0) {
                                                                                                                                                                                                                                                                                                                            } else { ?>checked <?php } ?>>
                      </div>
                    </td>


                    <!-- <td><button class="btn btn-success btn-xs">activo</button></td> -->
                    <td><?php echo date_format(date_create($value['ultimo_login']), 'd/m/Y H:i:s'); ?></td>
                    <td>
                      <div class="btn-group">

                        <button class="btn btn-warning btnEditarUsuario" idUsuario="<?php echo $value['id'] ?>" data-toggle="modal" data-target="#modalEditarUsuario"><i class="fas fa-user-edit"></i></button>
                        <?php
                        if ($_SESSION['perfil'] == 'Administrador') {
                        ?>
                          <button class="btn btn-danger btnEliminarUsuario" idUsuario="<?php echo $value['id'] ?>" fotoUsuario="<?php echo $value['foto'] ?>" usuario="<?php echo $value['usuario'] ?>"><i class="fas fa-trash-alt"></i></button>
                        <?php } ?>

                      </div>


                    </td>

                  </tr>


                  <?php
                } else {
                  $item = null;
                  $valor = null;
                  $usuarios = ControladorUsuarios::ctrMostrarUsuarios($item, $valor);

                  foreach ($usuarios as $key => $value) :

                  ?>
                    <tr>
                      <td><?php echo ++$key; ?></td>
                      <td><?php echo $value['nombre']; ?></td>
                      <td><?php echo $value['usuario']; ?></td>

                      <?php
                      if ($value['foto'] != '') {

                        echo '<td><img src="' . $value['foto'] . '" alt="" class="img-thumbnail" width="40px"></td>';
                      } else {

                        echo '<td><img src="vistas/img/usuarios/default/man_default.svg" alt="" class="img-thumbnail" width="40px"></td>';
                      }
                      ?>
                      <!-- <td><img src="" alt="" class="img-thumbnail" width="40px"></td> -->
                      <td><?php echo $value['perfil']; ?></td>


                      <td>
                        <div class="modo-contenedor-selva">

                          <input type="checkbox" data-toggle="toggle" data-on="Activado" data-off="Desactivado" data-onstyle="success" data-offstyle="danger" id="usuarioEstado" name="usuarioEstado<?php $value['estado'] ?>" value="<?php $value['estado'] ?>" data-size="mini" data-width="110" idUsuario="<?php echo $value['id'] ?>" <?php if ($value['estado'] == 0) {
                                                                                                                                                                                                                                                                                                                                          } else { ?>checked <?php } ?>>
                        </div>
                      </td>


                      <!-- <td><button class="btn btn-success btn-xs">activo</button></td> -->
                      <td><?php echo date_format(date_create($value['ultimo_login']), 'd/m/Y H:i:s'); ?></td>
                      <td>
                        <div class="btn-group">

                          <button class="btn btn-warning btnEditarUsuario" idUsuario="<?php echo $value['id'] ?>" data-toggle="modal" data-target="#modalEditarUsuario"><i class="fas fa-user-edit"></i></button>
                          <?php
                          if ($_SESSION['perfil'] == 'Administrador') {
                          ?>
                            <button class="btn btn-danger btnEliminarUsuario" idUsuario="<?php echo $value['id'] ?>" fotoUsuario="<?php echo $value['foto'] ?>" usuario="<?php echo $value['usuario'] ?>"><i class="fas fa-trash-alt"></i></button>
                          <?php } ?>

                        </div>


                      </td>

                    </tr>

                <?php
                  endforeach;
                }
                ?>
              </tbody>
            </table>
          </div>

        </div>

      </div>
      <!-- BOX FIN -->
      <!-- /.box-footer -->
    </section>

  <?php } ?>
  <!-- <button type="button" class="btn btn-primary printsave">Print</button>
<div class="printerhere" width="100%" ></div> -->
  <!-- <embed class="printerhere" src="" type="application/pdf" width="100%" height="600px" class="printerhere" /> -->

</div>

<!-- MODAL AGREGAR USUARIO -->
<!-- Modal -->
<div id="modalAgregarUsuario" class="modal fade modal-forms fullscreen-modal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">


      <form role="form" id="formUser" class="form-inserta" enctype="multipart/form-data" autocomplete="off">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#0e6edf; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">AGREGAR USUARIO</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">
            <div class="col-md-8">
              <!-- ENTRADA PARA EL NOMBRE -->
              <div id="respuestaAjax"></div>

              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control " name="nuevoDni" id="nuevoDni" placeholder="Ingresar DNI" title="DNI">
                  <span class="input-group-addon btn buscarRuc"><i class="fa fa-search"></i></span>
                  <div class="resultadoCliente" idCliente=""><a href="#" class="btn-add"></a></div>

                </div>
              </div>

              <div class="form-group">
                <div class="input-group">
                  <input type="text" class="form-control " name="nuevoNombre" id="nuevoNombre" placeholder="Ingresar nombre" title="Nombre">
                  <span class="input-group-addon btn btn-secondary" onclick="limpiarInputs()"><i class="fas fa-eraser"></i></span>
                </div>
              </div>

              <script>
                function limpiarInputs() {
                  document.getElementById("nuevoDni").value = "";
                  document.getElementById("nuevoNombre").value = "";
                }
              </script>

              <!-- ENTRADA PARA EL USUARIO -->

              <div class="form-group">

                <input type="text" class="form-control " name="nuevoEmail" id="nuevoEmail" placeholder="Ingresar correo electrónico" title="Correo electrónico">

              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">

                    <input type="text" class="form-control nuevoUser" name="nuevoUsuario" id="nuevoUsuario" placeholder="Ingresar usuario" title="Usuario">

                  </div>
                </div>
                <div class="col-md-6">
                  <!-- ENTRADA PARA LA CONTRASEÑA -->
                  <div class="form-group">

                    <input type="password" class="form-control " name="nuevoPassword" id="nuevoPassword" placeholder="Ingresar contraseña" title="Contraseña">

                  </div>
                </div>
              </div>

              <!-- ENTRADA PARA SELECCIONAR SU PERFIL -->

              <div class="form-group">

                <select class="form-control " name="nuevoPerfil" id="nuevoPerfil" title="Perfil">

                  <option value="">Selecionar perfil</option>

                  <option value="Administrador">Administrador</option>

                  <option value="Logistica">Logistica</option>

                  <option value="Vendedor">Vendedor</option>

                </select>
              </div>
            </div>
            <div class="col-md-4">
              <!-- ENTRADA PARA SUBIR FOTO -->
              <div class="img-contenedor">
                <label for="editarFoto"></label>
                <input type="file" class="nuevaFoto" name="editarFoto" id="editarFoto">
                <p class="help-block">Peso máximo de la foto 2MB</p>
                <img src="vistas/img/usuarios/default/man_default.svg" class="img-thumbnail previsualizar" width="130px">
                <input type="hidden" name="fotoActual" id="fotoActual">
              </div>
            </div>

            <script>
              // Obtener la entrada de archivos
              var input = document.getElementById('editarFoto');

              // Agregar un evento de cambio a la entrada de archivos
              input.addEventListener('change', function() {
                // Obtener el archivo seleccionado
                var file = input.files[0];

                // Verificar si el tipo de archivo es válido
                if (!file.type.match('image/jpeg')) {
                  alert('Solo se permiten imágenes JPEG');
                  input.value = ''; // Limpiar la entrada de archivos
                  return;
                }

                // Verificar si el tamaño del archivo es válido
                if (file.size > 2000000) {
                  alert('El tamaño de la imagen debe ser menor o igual a 2MB');
                  input.value = ''; // Limpiar la entrada de archivos
                  return;
                }

                // Mostrar la imagen previa
                var img = document.querySelector('.previsualizar');
                var reader = new FileReader();
                reader.onload = function(e) {
                  img.src = e.target.result;
                };
                reader.readAsDataURL(file);
              });
            </script>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="far fa-times-circle fa-lg"></i> Salir</button>

          <button type="submit" class="btn btn-primary btnusuario">Guardar</button>

        </div>

        <?php

        // $crearUsuario = ControladorUsuarios::ctrCrearUsuario();

        ?>

      </form>


    </div>
  </div>
</div>

<!-- MODAL EDITAR USUARIO -->
<div id="modalEditarUsuario" class="modal fade modal-forms fullscreen-modal" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#0e6edf; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">EDITAR USUARIO</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <div class="col-md-8">
              <!-- ENTRADA PARA EL NOMBRE -->
              <div class="form-group">

                <input type="text" class="form-control " id="editarDni" name="editarDni" value="" title="DNI" readonly>


              </div>
              <div class="form-group">

                <input type="text" class="form-control " id="editarNombre" name="editarNombre" value="" title="Nombre" readonly>

              </div>

              <!-- ENTRADA PARA EL EMAIL -->

              <div class="form-group">

                <input type="text" class="form-control " id="editarEmail" name="editarEmail" value="" title="Correo Electrónico">

              </div>
              <!-- ENTRADA PARA EL USUARIO -->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control " id="editarUsuario" name="editarUsuario" value="" title="Usuario" readonly>

                  </div>
                </div>

                <!-- ENTRADA PARA LA CONTRASEÑA -->
                <div class="col-md-6">
                  <div class="form-group">

                    <input type="password" class="form-control " name="editarPassword" placeholder="Escriba la nueva contraseña" title="Contraseña">

                    <input type="hidden" id="passwordActual" name="passwordActual">

                  </div>
                </div>
              </div>

              <!-- ENTRADA PARA SELECCIONAR SU PERFIL -->

              <div class="form-group">

                <?php

                if ($_SESSION['perfil'] == 'Administrador') {
                ?>

                  <select class="form-control " name="editarPerfil">

                    <option value="" id="editarPerfil"></option>

                    <option value="Administrador">Administrador</option>

                    <option value="Especial">Especial</option>

                  </select>
                <?php } else { ?>

                  <select class="form-control " name="editarPerfil">

                    <option value="<?php echo $_SESSION['perfil'] ?>"><?php echo $_SESSION['perfil'] ?></option>

                  </select>

                <?php } ?>

              </div>
            </div>

            <div class="col-md-4">
              <!-- ENTRADA PARA SUBIR FOTO -->
              <div class="img-contenedor">

                <label for="editarFoto"></label>

                <input type="file" class="nuevaFoto" name="editarFoto" id="editarFoto" accept="image/jpeg" maxlength="2000000">

                <p class="help-block">Peso máximo de la foto 2MB</p>

                <img src="vistas/img/usuarios/default/man_default.svg" class="img-thumbnail previsualizar" width="130px">

                <input type="hidden" name="fotoActual" id="fotoActual">
              </div>
            </div>

          </div>

          <p style="text-align: center;">* El campo usuario no podra editarse posterior al registro</p>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="far fa-times-circle fa-lg"></i> Salir</button>

          <button type="submit" class="btn btn-primary">Modificar usuario</button>

        </div>

        <?php

        $editarUsuario = new ControladorUsuarios();
        $editarUsuario->ctrEditarUsuario();

        ?>

      </form>

    </div>

  </div>

</div>

<?php
$borrarUsuarios =  ControladorUsuarios::ctrBorrarUsuario();

?>

<div class="resultados"></div>