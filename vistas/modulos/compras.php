<?php

use Controladores\ControladorCompras;
?>
<div class="content-wrapper panel-medio-principal">
    <div style="padding:5px"></div>
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
        <section class="container-fluid panel-medio">
            <div class="box rounded">
                <div class="box-header">
                    <i class="fas fa-file-invoice"></i>&nbsp;
                    <h3 class="box-title">Administración de compras</h3>

                    <button class="btn btn-success pull-right btn-radius" onclick="window.location.href='?ruta=nueva-compra'">
                        <i class="fas fa-plus-square"></i> Nueva compra <i class="fa fa-th"></i>
                    </button>
                </div>

                <div class="box-body table-user">
                    <div class="contenedor-busqueda">
                        <!-- row fechas -->
                        <div class="row fechas-reportes">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="fechareportes" id="fechaInicial" name="fechaInicial" placeholder="Fecha Inicial" style="width:100%" value="<?php echo date("d/m/Y"); ?>" onchange="loadReportesCompras(1)">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="text" class="fechareportes" id="fechaFinal" name="fechaFinal" placeholder="Fecha Final" style="width:100%" value="<?php echo date("d/m/Y"); ?>" onchange="loadReportesCompras(1)">
                                </div>
                            </div>
                        </div>

                        <div class="contenedor-radios">
                            <label for="factura" class="btn-radios"><i class="fa fa-file-invoice"></i> Facturas</label>
                            <input type="radio" id="factura" class="comp" name="tipocomp" value="01" checked>
                            <label for="boleta" class="btn-radios"><i class="fa fa-file-invoice"></i> Boletas</label>
                            <input type="radio" id="boleta" class="comp" name="tipocomp" value="03">
                            <label for="notac" class="btn-radios"><i class="fa fa-file-invoice"></i> Notas C</label>
                            <input type="radio" id="notac" class="comp" name="tipocomp" value="07">
                            <label for="notad" class="btn-radios"><i class="fa fa-file-invoice"></i> Notas D</label>
                            <input type="radio" id="notad" class="comp" name="tipocomp" value="08">
                            <label for="cfb" class="btn-radios"><i class="fa fa-file-invoice"></i> Facturas y Boletas</label>
                            <input type="radio" id="cfb" class="comp" name="tipocomp" value="00">
                        </div>

                        <div class="form-group contenedor-btn-reportes">
                            <button class="btn btn-primary pull-right btn-show-envio-reporte"><i class="far fa-paper-plane fa-lg"></i> ENVIAR</button>
                            <div class="box-tools pull-right reporte-compras-excel" width="100%"></div>
                            <button class="btn btn-default pull-right btn-reporte-pdf-compras" data-toggle="modal" data-target="#modalImprimir"><i class="far fa-file-pdf fa-lg" style="color:red;"></i> REPORTE PDF</button>
                        </div>

                        <div class="input-group-search">
                            <select class="selectpicker show-tick" data-style="btn-select" data-width="70px" id="selectnum" name="selectnum" onchange="loadReportesCompras(1)">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <div class="input-search">
                                <input type="search" class="search" id="searchReportes" name="searchReportes" placeholder="Buscar" onkeyup="loadReportesCompras(1)">
                                <span class="input-group-addo"><i class="fa fa-search"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered dt-responsive tablaVentas tabla-reportes" width="100%">
                            <thead>
                                <tr>
                                    <th style="width:10px;">#</th>
                                    <th>FECHA EMISIÓN</th>
                                    <th>COMPROBANTE</th>
                                    <th>PROVEEDOR</th>
                                    <th>I.G.V.</th>
                                    <th>SUBTOTAL</th>
                                    <th>TOTAL</th>
                                    <th>PDF</th>
                                    <th>ACCIÓN</th>
                                </tr>
                            </thead>
                            <tbody class="body-reporte-compras">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>
</div>

<!-- Modal IMPRIMIR -->
<div class="modal fade bd-example-modal-lg" id="modalImprimir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-12">
                    <div class="printerhere" width="100%"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="far fa-times-circle fa-lg"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        loadReportesCompras(1);
    });
</script>