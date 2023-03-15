<?php

use Controladores\ControladorEmpresa;
use Controladores\ControladorProductos;
use Controladores\ControladorCategorias;
use Controladores\ControladorSunat;

?>
<div class="content-wrapper panel-medio-principal">

  <?php
  if ($_SESSION['perfil'] == 'Vendedor') {

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
    <section class="container-fluid">
      <section class="content-header dashboard-header">
        <div class="box container-fluid" style="border:0px; margin:0px; padding:0px;">
          <div class="col-lg-12 col-xs-12" style="border:0px; margin:0px; padding:0px;  border-radius:10px;">

            <div class="col-md-3 hidden-sm hidden-xs">
              <button class=""><i class="fas fa-file-invoice"></i> Nueva compra</button>
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

        <div class="box-header" style="border: 0px; padding-top:5px;">
          <!-- <h3 class="box-title">Crear venta</h3> -->

        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">


            <!-- FORMULARIO -->
            <div class="col-lg-12 col-xs-12">

              <div class="box box-success" style="border-top: 0px;">
                <div class="box-header" style="border: 0px; padding:0px;">

                </div>


                <form role="form" method="post" class="formCompra" id="formCompra">

                  <input type="hidden" class="" id="tipo_cambio" name="tipo_cambio" value="">

                  <div class="box-body" style="border: 0px; padding-top:0px; ">

                    <legend class="text-bold" style="margin-left:15px; font-size:1.3em; letter-spacing: 1px;">DATOS DEL COMPROBANTE:</legend>
                    <!-- PRIMERA ENTRADA FORM -->
                    <div class="box" style="border: 0px; padding-top:0px;">

                      <!-- ENTRADA SERIE-->
                      <div class="col-md-3 col-xs-6">
                        <div class="form-group">
                          <div class="input-group">

                            <span class="input-group-addon"><i class="fa fa-key"></i></span>

                            <select class="form-control" name="tipoComprobante" id="tipoComprobante" title="Tipo de comprobante" value="">
                              <option value="">Tipo comprobante</option>
                              <option value="01">Factura</option>
                              <option value="03">Boleta</option>
                              <option value="07">Nota de crédito</option>
                              <option value="08">Nota de débito</option>
                            </select>

                          </div>
                        </div>
                      </div>

                      <!-- ENTRADA TIPO MONEDA-->
                      <div class="col-md-3 col-xs-6">
                        <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-money-bill"></i></span>
                            <select class="form-control" id="moneda" name="moneda" title="Moneda">
                              <option value="PEN">Soles (S/)</option>
                              <option value="USD">Dólares Americanos ($)</option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <!-- ENTRADA FECHA DOC-->
                      <div class="col-md-2 col-xs-6">
                        <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            <input type="text" class="form-control" name="fechaDoc" id="fechaDoc" value="<?php echo date("d/m/Y") ?>" title="Fecha" required>
                          </div>
                        </div>
                      </div>
                      <!-- ENTRADA SERIE DOC-->
                      <div class="col-md-2 col-xs-6">
                        <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-barcode"></i></span>
                            <input type="text" class="form-control" name="serieDoc" id="serieDoc" title="Serie" onkeyup="this.value=Numeros(this.value)" placeholder="Serie" required>
                          </div>
                        </div>
                      </div>
                      <!-- ENTRADA CORRELATIVO DOC-->
                      <div class="col-md-2 col-xs-6">
                        <div class="form-group">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-file-invoice"></i></span>
                            <input type="text" class="form-control" name="correlativoDoc" id="correlativoDoc" title="Correlativo" onkeyup="this.value=Numeros(this.value)" placeholder="Correlativo" required>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- <div class="row">
                                  <div class="col-md-12 col-xs-6">
                                  <input type="hidden" class="form-control" id="correlativo">
                              </div>
                                  </div> -->
                    <!-- ENTRADA CLIENTE -->
                    <div class="row contenedor-notascd">

                    </div>

                    <div class="row">
                      <legend class="text-bold" style="margin-left:15px; font-size:1.3em; letter-spacing: 1px;">DATOS PROVEEDOR:</legend>

                      <div class="col-md-3">
                        <div class="form-group">
                          <div class="input-group">
                            <!-- ID CLIENTE -->
                            <input type="hidden" name="idProveedor" id="idProveedor">

                            <span class="input-group-addon"><i class="fas fa-id-card"></i></span>

                            <select class="form-control" name="tipoDoc" id="tipoDoc" title="Tipo de documento">
                              <?php
                              $item = null;
                              $valor = null;
                              $tipoDocumento = ControladorSunat::ctrMostrarTipoDocumento($item, $valor);
                              foreach ($tipoDocumento as $key => $value) {

                                echo "<script>$('#tipoDoc').val(6);</script>";

                                echo '<option value=' . $value['codigo'] . '>' . $value['descripcion'] . '</option>';
                              }
                              ?>
                            </select>

                          </div>
                        </div>
                      </div>

                      <!-- ENTRADA DOCUMENTO -->
                      <div class="col-md-4">

                        <div class="form-group">
                          <div class="input-group">
                            <div id="rucActivo"></div>
                            <input type="text" class="form-control" id="docIdentidad" name="docIdentidad" placeholder="Ingrese número de documento" title="Número de documento">
                            <span class="input-group-addon btn buscarRucP"><i class="fa fa-search"></i></span>
                            <div id="reloadC"></div>
                            <div class="resultadoProveedor" idCliente=""><a href="#" class="btn-add-p"></a></div>
                          </div>
                        </div>
                      </div>
                      <!-- ENTRADA RESULTADO DOCUMENTO -->
                      <div class="col-md-5">
                        <div class="form-group">
                          <div class="input-group-adddon">
                            <input type="text" class="form-control" id="razon_social" name=" razon_social" placeholder="Ingrese nombre o razón social" title="Razón social">
                            <!-- <span class="input-group-addon"></span>  -->
                          </div>
                        </div>
                      </div>

                    </div>
                    <!-- ENTRADA CLIENTE 2 -->
                    <div class="row">
                      <!-- ENTRADA DOCUMENTO -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="input-group-adddon">
                            <input type="text" class="form-control" id="direccion" name=" direccion" placeholder="Ingrese la dirección" title="Dirección">
                            <!-- <span class="input-group-addon"><i class="fa fa-search"></i></span>  -->
                          </div>
                        </div>
                      </div>

                      <!-- ENTRADA DOCUMENTO -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="input-group-adddon">
                            <input type="text" class="form-control" id="ubigeo" name=" ubigeo" onkeyup="this.value=Numeros(this.value)" placeholder="Ingrese codigo de ubigeo" title="Código de ubigeo">
                            <!-- <span class="input-group-addon"><i class="fa fa-search"></i></span>  -->
                          </div>
                        </div>
                      </div>
                      <!-- ENTRADA RESULTADO DOCUMENTO -->
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="input-group-adddon">
                            <input type="text" class="form-control" id="celular" name=" celular" onkeyup="this.value=Numeros(this.value)" maxlength="9" placeholder="Ingrese su número de celular" title="Número de celular">
                            <!-- <span class="input-group-addon"></span>  -->
                          </div>
                        </div>
                      </div>

                    </div>

                    <!-- ENTRADA PARA AGREGAR PRODUCTOS -->
                    <div class="col-lg-12 col-xs-12">
                      <div class="row nuevoProductoC">

                        <div class="flex">
                          <button type="button" class="btn btn-success btn-radius pull-right btnproser" data-toggle="modal" data-target="#modalAgregarProducto"><i class="fas fa-plus-square"></i>Nuevo producto &nbsp;<i class="fa fa-th"></i>
                          </button>
                          <button type="button" class="btn btn-primary pull-right btn-agregar-carrito" data-toggle="modal" data-target="#modalProductosVenta"><i class="fas fa-cart-plus fa-lg"></i>&nbsp; Agregar Productos</button>

                        </div>
                        <div class="table-responsive items-c">
                          <!-- BOTÓN PARA AGREGAR PRODUCTO-->
                          <table class="table tabla-items">
                            <thead>
                              <tr>
                                <th>Código</th>
                                <th>Cantidad</th>
                                <th>Uni/medida</th>
                                <th>Descripción</th>
                                <th>Precio unitario</th>
                                <th>Valor unitario</th>
                                <th>Sub.Total</th>
                                <th>Total</th>
                                <th></th>
                              </tr>
                            </thead>
                            <tbody id="itemsP">

                            </tbody>

                          </table>
                        </div>
                        <!-- FIN ENTRADA AGREGAR PRODUCTOS  -->

                        <br>
                        <br>
                        <div class="box">

                          <!-- DESCUENTO GLOBAL| -->
                          <div class="col-md-6 col-sm-12">
                            <table class="table">
                              <thead>
                                <tr>
                                  <th>DESCUENTO</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>
                                    <div class="box" style="display: none">
                                      <div class="col col-lg-12 col-sm-12 col-xs-12">
                                        <div class="contenedor-tipo-descuento">
                                          <label for="soles" id="sol" class="">S/</label>
                                          <input type="radio" id="soles" class="tipo_desc" name="tipo_desc" value="S/" checked>
                                          <label for="porcen" id="por" class="">%</label>
                                          <input type="radio" id="porcen" class="tipo_desc" name="tipo_desc" value="%">
                                        </div>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-money-bill-wave"></i></span>
                                        <span class="input-group-addon">&nbsp;S/</i></span>
                                        <input type="number" class="form-control" style="display: none" min="0" placeholder="0.00" id="descuentoGlobalC" name=" descuentoGlobalP" value="" placeholder="Ingrese descuento">
                                        <input type="number" class="form-control" min="0" maxlength="3" placeholder="0.00" id="descuentoGlobal" name=" descuentoGlobal" value="" placeholder=" Ingrese descuento">
                                      </div>
                                        <p id="maxMsg" class="alert alert-danger" style="display:none;">El descuento no puede ser mayor que el subtotal</p>
                                        <script>
                                          var descuentocantidadInput = document.getElementById('descuentoGlobal');
                                          var maxMsg = document.getElementById('maxMsg');

                                          descuentocantidadInput.addEventListener('input', function(event) {
                                            if (descuentocantidadInput.value > 200) {
                                              maxMsg.style.display = 'block';
                                            } else {
                                              maxMsg.style.display = 'none';
                                            }
                                          });
                                        </script>
                                    </div>
                                  </td>
                                </tr>
                                <!-- MÉTODO DE PAGO ========] -->
                                <tr>
                                  <th>MÉTODO DE PAGO:</th>
                                </tr>
                                <tr>
                                  <td>
                                    <div class=" form-group">
                                      <div class="input-group">
                                        <span class="input-group-addon"><i class="fas fa-money-bill-wave"></i></span>
                                        <select style="width: 100%;" class="form-control" id="metodopago" name="metodopago">
                                          <option value="009">En efectivo</option>
                                          <option value="001">Depósito en cuenta</option>
                                          <option value="005">Tarjeta de débito</option>
                                          <option value="006">Tarjeta de crédito</option>
                                          <option value="003">Transferencia bancaria</option>
                                          <option value="002">Giro</option>
                                        </select>
                                      </div>
                                    </div>
                                  </td>
                                </tr>
                                <!-- FIN MÉTODO DE PAGO ======== -->
                                <!-- COMENTARIO=========== -->
                                <tr>
                                  <th>OBSERVACIONES</th>
                                </tr>
                                <tr>
                                  <td colspan="2">
                                    <div class="form-group">
                                      <div class="input-group">
                                        <span class="input-group-addon"><i class="far fa-comment-dots"></i></span>
                                        <textarea class="form-control" name="comentario" id="comentario" cols="50" rows="4"></textarea>
                                      </div>
                                    </div>
                                  </td>
                                </tr>

                                <!-- FIN COMENTARIO======= -->
                              </tbody>

                            </table>

                          </div>
                          <!-- FIN DESCUENTO GLOBAL -->

                          <!-- //ENTRADA DE REMUMMEN TOTALES  -->
                          <div class="col-md-6 col-sm-12">
                            <!-- <div class="table-responsive"> -->
                            <table class="table  tabla-totales">

                              <thead>
                                <tr>
                                  <th></th>
                                  <th>RESUMEN</th>
                                </tr>
                              </thead>
                              <tbody class="totales">

                                <tr class="">
                                  <td>SubTotal</td>
                                  <td><input type="text" class="" name="subtotalc" id="subtotalc" value="0.00" readonly /></td>
                                </tr>
                                </tr>
                                <tr class="">
                                  <td>Op.Gravadas</td>
                                  <td><input type="text" class="" name="op_gravadas" id="op_gravadas" value="0.00" readonly /></td>
                                </tr>
                                </tr>
                                <tr class="">
                                  <td>Op.Exoneradas</td>
                                  <td><input type="text" class="" name="op_exoneradas" id="op_exoneradas" value="0.00" readonly /></td>
                                </tr>
                                </tr>
                                <tr class="">
                                  <td>Op.Inafectas</td>
                                  <td><input type="text" class="" name="op_inafectas" id="op_inafectas" value="0.00" readonly /></td>
                                </tr>
                                </tr>
                                <tr class="">
                                  <td>Op.gratuitas</td>
                                  <td><input type="text" class="" name="op_gratuitas" id="op_gratuitas" value="0.00" readonly /></td>
                                </tr>
                                </tr>
                                <tr class="">
                                  <td>Descuento (-)</td>
                                  <td><input type="text" class="" name="descuento" id="descuento" value="0.00" readonly /></td>
                                </tr>
                                </tr>
                                <tr class="">
                                  <td>ICBPER</td>
                                  <td><input type="text" class="" name="icbper" id="icbper" value="0.00" readonly /></td>
                                </tr>
                                </tr>
                                <tr class="">
                                  <td>IGV(18%)</td>
                                  <td><input type="text" class="" name="igvc" id="igvc" value="0.00" readonly /></td>
                                </tr>
                                </tr>

                                <tr class="">
                                  <td>Total</td>
                                  <td><input type="text" class="" name="totalc" id="totalc" value="0.00" readonly /></td>
                                </tr>
                                </tr>

                              </tbody>
                            </table>
                            <!-- </div> -->
                            <!-- // FIN ENTRADA DE REMUMMEN TOTALES  -->
                          </div>
                        </div>
                      </div>

                      <hr>

                    </div>

                  </div>

                  <div class="box-footer contenedor-btns-carrito">

                    <button type="button" class="btnGuardarCompra"><i class="far fa-save"></i></button>

                    <!-- BOTÓN PARA ELIMINAR CARRO-->
                    <button type="button" class="btnEliminarCarro"><i class="fas fa-trash-alt"></i></button>
                  </div>


                </form>
              </div>
            </div>

          </div>

        </div>

      </div>
      <!-- BOX FIN -->
      <!-- /.box-footer -->
    </section>

  <?php } ?>

</div>

<!-- Modal AGREGAR PRODUCTOS -->
<div class="modal fade bd-example-modal-lg" id="modalProductosVenta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-body contenedor-pro">

        <form action="" method="post" name="formItems" id="formItems">
          <div class="box">
            <!-- ENTRADA CORRELATIVO DOC-->
            <input type="hidden" name="idProductoc" id="idProductoc" value="">
            <div class="col-md-12">
              <div class="form-group c-productos">
                <span class="input-group-addonn">Descripción</span>
                <div class="input-group">
                  <input type="text" class="form-control" name="descripcion" id="descripcion" required placeholder="Descripción" autocomplete="off">
                  <span class="input-group-addon btn btn-secondary boton-limpiar" onclick="limpiarInputs()"><i class="fas fa-eraser"></i></span>
                </div>
                <div class="p-productos"></div>
              </div>
            </div>

          </div>

          <div class="box">

            <div class="col-md-4">
              <div class="form-group">

                <span class="input-group-addonn">Tipo afectación</span>
                <select class="form-control" name="tipo_afectacion" id="tipo_afectacion">
                  <?php
                  $item = null;
                  $valor = null;
                  $unidad_medida = ControladorSunat::ctrMostrarTipoAfectacion($item, $valor);
                  foreach ($unidad_medida as $k => $value) {

                    echo "<option value='" . $value['codigo'] . "'>" . $value['descripcion'] . "</option>";
                  }
                  ?>
                </select>
              </div>

            </div>

            <div class="col-md-4">
              <div class="form-group">

                <span class="input-group-addonn">U. Medida</span>
                <select class="form-control" name="unidad" id="unidad">
                  <?php
                  $item = null;
                  $valor = null;
                  $unidad_medida = ControladorSunat::ctrMostrarUnidadMedida($item, $valor);
                  foreach ($unidad_medida as $k => $value) {

                    if ($value['activo'] == 's') {

                      echo "<option value='" . $value['codigo'] . "'>" . $value['descripcion'] . "</option>";
                    }
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">

                <span class="input-group-addonn">Código</span>
                <input type="text" class="form-control" name="codigo" id="codigo" onkeyup="this.value=Numeros(this.value)" placeholder="Codigo" readonly required>
              </div>

            </div>
          </div>
          <div class="box">

            <div class="col-md-4">
              <div class="form-group">
                <span class="input-group-addonn">Precio Unitario</span>
                <input type="text" class="form-control" name="precio_unitario" id="precio_unitario" value="0" readonly>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <span class="input-group-addonn">Valor Unitario</span>
                <input type="text" class="form-control" name="valor_unitario" id="valor_unitario" onkeyup="this.value=Numeros(this.value)">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <span class="input-group-addonn">Cantidad</span>
                <input type="number" class="form-control" name="cantidad" id="cantidad" min="1" max="200" onkeyup="this.value=Numeros(this.value)" value="" required>
                <p id="maxMsg" class="alert alert-danger" style="display:none;">La cantidad no puede ser mayor que 200</p>
              </div>
            </div>

            <script>
              var cantidadInput = document.getElementById('cantidad');
              var maxMsg = document.getElementById('maxMsg');

              cantidadInput.addEventListener('input', function(event) {
                if (cantidadInput.value > 200) {
                  maxMsg.style.display = 'block';
                } else {
                  maxMsg.style.display = 'none';
                }
              });
            </script>

          </div>
          <div class="box">
            <div class="col-md-4">
              <div class="form-group">
                <span class="input-group-addonn">Sub Total</span>
                <input type="text" class="form-control" name="subtotal" id="subtotal" readonly required placeholder="">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <span class="input-group-addonn">IGV de la linea</span>
                <input type="text" class="form-control" name="igv" id="igv" readonly required placeholder="">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <span class="input-group-addonn">Total</span>
                <input type="text" class="form-control" name="total" id="total" readonly required placeholder="">

              </div>
            </div>
          </div>
          <div class="col-lg-12">
            <div class="box">
              <div class="col-md-2 col-xs-2">
                <div class="form-group">

                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <span class="input-group-addonn">ICBPER</span>
                  <div class="modo-contenedor-icbper">
                    <input type="checkbox" data-toggle="toggle" data-on="Sí" data-off="No" data-onstyle="success" data-offstyle="danger" id="icbper" name="icbper" value="0.30" data-size="small" data-width="80">

                  </div>
                </div>
              </div>
              <div class="col-md-4 col-xs-6">
                <div class="form-group">
                  <span class="input-group-addonn">Descuento</span>
                  <input type="text" class="form-control" name="descuento_item" id="descuento_item" onkeyup="this.value=Numeros(this.value)" placeholder="" value="0" required>

                </div>
              </div>
              <div class="col-md-4 col-xs-6">
                <div class="form-group">
                  <h3></h3>
                  <button class="btn btn-primary btn-lg" id="btnAddItem"><i class="fas fa-plus"></i> Agregar </button>

                </div>
              </div>
            </div>
          </div>

        </form>
      </div>
      <br>
      <br>
      <br>
      <div class="col-md-12" style="text-align: center">
        <p>* ICBPER: Impuesto al consumo de bolsas de plástico</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-times-circle fa-lg"></i> Cerrar</button>
      </div>
    </div>

  </div>
</div>
<!-- FIN MODAL            -->

<!-- LIMPIAR INPUTS DEL MODAL AGREGAR PRODUCTOS -->

<script>
  function limpiarInputs() {
    // Modal agregar producto
    document.getElementById("descripcion").value = "";
    // document.getElementById("tipo_afectacion").value = "";
    // document.getElementById("unidad").value = "";
    document.getElementById("codigo").value = "";
    document.getElementById("precio_unitario").value = "";
    document.getElementById("valor_unitario").value = "";
    document.getElementById("cantidad").value = "";
    document.getElementById("subtotal").value = "";
    document.getElementById("igv").value = "";
    document.getElementById("total").value = "";
    document.getElementById("descuento_item").value = "";
  }
</script>


<!-- MODAL NUEVO PRODUCTO-->
<!-- Modal -->
<div id="modalAgregarProducto" class="modal fade modal-forms fullscreen-modal" role="dialog" tabindex="-1">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">

      <form role="form" method="post" id="formProductos" enctype="multipart/form-data">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#0e6edf; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">NUEVO PRODUCTO</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <div class="col-md-8">
              <!-- PRIMERA SECCIÓN============= -->
              <!-- ENTRADA PARA LA DESCRIPCIÓN -->
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">

                    <select class="form-control" name="nuevaCategoria" id="nuevaCategoria" required>

                      <option value="">Selecionar categoría</option>
                      <?php
                      $item = null;
                      $valor = null;
                      $categorias = ControladorCategorias::ctrMostrarCategorias($item, $valor);
                      foreach ($categorias as $k => $value) :

                        echo '<option value="' . $value['id'] . '">' . $value['categoria'] . '</option>';

                      endforeach;
                      ?>

                    </select>

                  </div>
                </div>

                <!-- ENTRADA PARA DEL CÓDIGO -->
                <div class="col-md-4">
                  <div class="form-group">

                    <input type="text" class="form-control" name="nuevoCodigo" id="nuevoCodigo" placeholder="Código" readonly required>

                  </div>
                </div>
                <!-- ENTRADA PARA LA SERIE -->
                <div class="col-md-4">
                  <div class="form-group">

                    <input type="text" class="form-control" name="nuevaSerie" id="nuevaSerie" onkeyup="this.value=Numeros(this.value)" placeholder="Serie del producto" readonly>

                  </div>
                </div>
              </div>

              <!-- FIN PRIMERA SECCIÓN======== -->
              <!-- ENTRADA PARA UNIDAD DE MEDIDA -->
              <div class="row">

                <div class="col-md-6">
                  <div class="form-group">

                    <select class="form-control" name="tipo_afectacion" id="tipo_afectacion">
                      <?php
                      $item = null;
                      $valor = null;
                      $unidad_medida = ControladorSunat::ctrMostrarTipoAfectacion($item, $valor);
                      foreach ($unidad_medida as $k => $value) {

                        echo "<option value='" . $value['codigo'] . "'>" . $value['descripcion'] . "</option>";
                      }
                      ?>
                    </select>

                  </div>
                </div>
                <!-- ENTRADA PARA UNIDAD DE MEDIDA -->
                <div class="col-md-6">
                  <div class="form-group">


                    <select class="form-control" name="unidad" id="unidad">
                      <?php
                      $item = null;
                      $valor = null;
                      $unidad_medida = ControladorSunat::ctrMostrarUnidadMedida($item, $valor);
                      foreach ($unidad_medida as $k => $value) {

                        if ($value['activo'] == 's') {

                          echo "<option value='" . $value['codigo'] . "'>" . $value['descripcion'] . "</option>";
                        }
                      }
                      ?>
                    </select>

                  </div>

                </div>

              </div>

              <!-- ENTRADA PARA LA DESCRIPCIÓN -->
              <div class="row">
                <div class="col-md-9">
                  <div class="form-group">

                    <input type="text" class="form-control" name="nuevaDescripcion" id="nuevaDescripcion" placeholder="Ingresar descripción" required>

                  </div>
                </div>

                <!-- ENTRADA PARA STOCK -->
                <div class="col-md-3">
                  <div class="form-group">

                    <input type="number" class="form-control" min="0" name="nuevoStock" id="nuevoStock" onkeyup="this.value=Numeros(this.value)" placeholder="Ingresar stock" required>

                  </div>
                </div>
              </div>
              <!-- ENTRADA PARA PRECIO VENTA-->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">

                    <input type="text" class="form-control" name="nuevoPrecioUnitario" id="nuevoPrecioUnitario" onkeyup="this.value=Numeros(this.value)" placeholder="Ingresar precio unitario" step="any" required>

                  </div>
                </div>

                <!-- ENTRADA PARA PRECIO COMPRA -->
                <div class="col-md-6">

                  <div class="form-group">

                    <input type="text" class="form-control" name="nuevoValorUnitario" id="nuevoValorUnitario" placeholder="Valor unitario" step="any" readonly required>

                  </div>
                </div>
              </div>

              <!-- CHECKBOX PARA PORCENTAJE -->
              <div class="row">
                <div class="col-md-6">

                  <div class="form-group">

                    <input type="text" class="form-control" name="nuevoigv" id="nuevoigv" placeholder="IGV 18%" readonly>

                  </div>
                </div>

                <!-- ENTRADA IGV  -->
                <div class="col-md-6">
                  <div class="form-group">

                    <input type="number" class="form-control" name="nuevoPrecioCompra" id="nuevoPrecioCompra" placeholder="Precio compra" readonly>

                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">

              <!-- ENTRADA PARA SUBIR FOTO -->

              <div class="img-contenedor">

                <label for="nuevaImagen"></label>
                <input type="file" class="nuevaImagen" name="nuevaImagen" id="nuevaImagen">

                <p class="help-block">Peso máximo de la imagen 2MB</p>

                <img src="vistas/img/productos/default/anonymous.png" class="img-thumbnail previsualizar" width="130px">

              </div>

            </div>
          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="far fa-times-circle fa-lg"></i> Salir</button>

          <button type="submit" class="btn btn-primary btn-nuevo-producto">Guardar producto</button>

        </div>

        <?php

        // $crearProducto = new ControladorProductos();
        // $crearProducto-> ctrCrearProducto();

        ?>

      </form>

    </div>
  </div>
</div>

<script>
  function Numeros(string) { //Solo numeros
    var out = '';
    var filtro = '1234567890'; //Caracteres validos

    //Recorrer el texto y verificar si el caracter se encuentra en la lista de validos 
    for (var i = 0; i < string.length; i++)
      if (filtro.indexOf(string.charAt(i)) != -1)
        //Se añaden a la salida los caracteres validos
        out += string.charAt(i);

    //Retornar valor filtrado
    return out;
  }
</script>