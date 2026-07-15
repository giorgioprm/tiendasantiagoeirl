<div class="content-wrapper panel-medio-principal">

  <div style="padding:5px"></div>
  <section class="container-fluid">
    <section class="content-header dashboard-header">

      <div class="box container-fluid" style="border:0px; margin:0px; padding:0px;">
        <div class="col-lg-12 col-xs-12" style="border:0px; margin:0px; padding:0px; border-radius:10px;">

          <div class="col-md-3 hidden-sm hidden-xs">
            <button class=""><i class="fas fa-bars"></i> Panel de Control</button>
          </div>
        </div>
      </div>
    </section>
  </section>



  <!-- <section class="content"> -->
  <section class="container-fluid panel-medio">
    <!-- BOX INI -->
    <div class="box rounded" style="margin:0px; padding:0px;">
      <div class="box-header with-border" style="flex-shrink: 0;">
        <h3 class="box-title"><a href="ventas">Resumen de Ventas</a></h3>

        <!-- row fechas -->
        <div class="row fechas-reportes ini-calendar-hide">
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
              <input type="text" class="fechareportes" id="fechaInicial" name="fechaInicial" placeholder="Fecha Inicial" style="width:100%" value="<?php echo date("d/m/Y"); ?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
              <input type="text" class="fechareportes" id="fechaFinal" name="fechaFinal" placeholder="Fecha Final" style="width:100%" value="<?php echo date("d/m/Y"); ?>"">
               </div>
             </div>
          </div>
          <!-- fin row fechas -->

                         
              <div class=" box-tools pull-right hidden" style="position: relative">
              <?php
              $fecha1 = date('Y-m') . '-01';
              $fecha2 = date('Y-m-d');
              $datetime1 = new DateTime($fecha1);
              $datetime2 = new DateTime($fecha2);
              $interval = $datetime1->diff($datetime2);
              $diferencia = $interval->format('%a');
              $fecha_actual = date("d-m-Y");
              $hoy =  date("d/m/Y");;
              $ayer =  date("d/m/Y", strtotime($fecha_actual . "- 1 days"));
              $semana =  date("d/m/Y", strtotime($fecha_actual . "- 1 week"));
              $mes =  date("d/m/Y", strtotime($fecha_actual . "- $diferencia days"));
              $ano =  date("d/m/Y", strtotime($fecha_actual . "- 1 year"));
              ?>
              <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle btn-calendar" data-toggle="dropdown">
                  <i class="far fa-calendar-alt"></i> Hoy</button>
                <ul class="dropdown-menu calendar-reports" role="menu">
                  <li><a href="#" class="exit-c" ini="<?php echo $hoy; ?>" fin="<?php echo $hoy; ?>">Hoy</a></li>
                  <li><a href="#" class="exit-c" ini="<?php echo $ayer; ?>" fin="<?php echo $ayer; ?>">Ayer</a></li>
                  <li><a href="#" class="exit-c" ini="<?php echo $semana; ?>" fin="<?php echo $hoy; ?>">Hace una semana</a></li>
                  <li><a href="#" class="exit-c" ini="<?php echo $mes; ?>" fin="<?php echo $hoy; ?>">Este mes</a></li>
                  <li><a href="#" class="exit-c" ini="<?php echo $ano; ?>" fin="<?php echo $hoy; ?>">Hace una año</a></li>
                  <li class="divider"></li>
                  <li><a href="#" ini="<?php echo $hoy; ?>" fin="<?php echo $hoy; ?>" class="personalizado">Personalizado</a></li>
                </ul>
              </div>

            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body" style="margin:0px; padding:0px;">

            <div class="container-fluid widgets-ini">
              <!-- carga dede javascript reportes.js -->

            </div>
            <br>
            <!-- DONUT CHART -->
            <div class="box" style="border-top:0px solid #fff">

              <div class="col-md-6 col-xs-12" style="margin:0px; padding:0px;">
                <!-- AREA CHART -->
                <div class="box box-primary">
                  <div class="box-header with-border">

                    <h3 class="box-title"><a href="ventas">Gráfico de Ventas</a></h3>

                  </div>
                  <div class="box-body chart-responsive chart-responsive-ventas">
                  </div>
                  <!-- /.box-body -->
                </div>
                <!-- /.box -->
              </div>
              <div class="col-md-6 col-xs-12" style="z-index: 10000 !important;">

                <?php
                include "vistas/modulos/dashboard-mas-vendidos.php";
                ?>

              </div>
            </div>
            <!-- /.box -->

          </div>

        </div>
        <!-- BOX FIN -->
        <!-- /.box-footer -->
  </section>

</div>