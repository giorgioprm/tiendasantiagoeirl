$(document).ready(function() {
  // Obtener tipo de cambio al cargar la página
  obtenerTipoCambio();
  
  // Actualizar cada 30 minutos (1800000 ms)
  setInterval(obtenerTipoCambio, 1800000);
});

function obtenerTipoCambio() {
  var fecha = $("#fecha").val();
  
  $.ajax({
    url: "ajax/tipo_cambio.php",
    method: "POST",
    data: {
      tipo_cambio: 1,
      fecha: fecha
    },
    dataType: "json",
    success: function(respuesta) {
      if (respuesta.compra && respuesta.venta) {
        // Guardar en el input hidden
        $("#tipo_cambio").val(respuesta.venta);
        
        // Actualizar el botón en el header con icono de dólar
        $("#tipocambio").html(
          '<i class="fas fa-dollar-sign"></i> Compra: S/ ' + respuesta.compra + 
          ' | Venta: S/ ' + respuesta.venta
        );
        
        // Agregar tooltip al botón
        $("#tipocambio").attr('title', 'Tipo de cambio del día ' + fecha);
        
        // Actualizar otros campos si existen
        if($(".tipo-cambio-compra").length > 0) {
          $(".tipo-cambio-compra").text(respuesta.compra);
        }
        if($(".tipo-cambio-venta").length > 0) {
          $(".tipo-cambio-venta").text(respuesta.venta);
        }
        if($("#tipo_cambio_compra").length > 0) {
          $("#tipo_cambio_compra").val(respuesta.compra);
        }
        if($("#tipo_cambio_venta").length > 0) {
          $("#tipo_cambio_venta").val(respuesta.venta);
        }
        
        // console.log("Tipo de cambio actualizado: Compra=" + respuesta.compra + " Venta=" + respuesta.venta);
      }
    },
    error: function(xhr, status, error) {
      console.log("Error al obtener tipo de cambio: " + error);
      // Mostrar mensaje de error en el botón
      $("#tipocambio").html('<i class="fas fa-exclamation-triangle"></i> T.C. No disponible');
      // Valor por defecto en caso de error
      $("#tipo_cambio").val(3.75);
    }
  });
}

// Función para obtener el tipo de cambio desde otros archivos JS
function getTipoCambio() {
  return parseFloat($("#tipo_cambio").val()) || 3.75;
}

// Función para obtener la compra (si la necesitas por separado)
function getTipoCambioCompra() {
  var textoBoton = $("#tipo_cambio").text();
  var compraMatch = textoBoton.match(/Compra: S\/ ([\d.]+)/);
  return compraMatch ? parseFloat(compraMatch[1]) : 3.72;
}

// Función para obtener la venta (si la necesitas por separado)
function getTipoCambioVenta() {
  return parseFloat($("#tipo_cambio").val()) || 3.75;
}

// Permitir hacer clic en el botón para actualizar manualmente
$(document).on('click', '#tipo_cambio', function() {
  $(this).html('<i class="fas fa-sync-alt fa-spin"></i> Actualizando...');
  obtenerTipoCambio();
});