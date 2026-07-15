$(".resultadoProveedor").hide();
$(".resultadoSerie").hide();
$('#rucActivo').hide();


// BUSCAR RUC O DNI DE LA BASE DE DATOS SI NO SE ENCUENTRA PASA A BUSCAR EN LAS APIS 
$(".buscarRucP").on('click', function () {
    let numDocumento = $("#docIdentidad").val();
    let tipoDocu = $("#tipoDoc").val();
    let datos = { "numDocumentoP": numDocumento };

    console.log("🔍 Iniciando búsqueda:", {
        documento: numDocumento,
        tipoDocumento: tipoDocu,
        datosEnviados: datos
    });

    $.ajax({
        method: "POST",
        url: 'ajax/proveedores.ajax.php',
        data: datos,
        dataType: "json",

        beforeSend: function () {
            $("#reloadC").show(50).html("<img src='vistas/img/reload1.svg'> ");
            console.log("⏳ Enviando solicitud AJAX...");
        },
        success: function (respuesta) {
            console.log("✅ Respuesta recibida:", respuesta);

            if (respuesta != false) {
                console.log("📋 Datos encontrados en BD:", respuesta);
                // ... resto del código
            } else {
                console.log("🔎 No encontrado en BD, consultando API...");
                // ... código de consulta API

                $.ajax({
                    // ... configuración AJAX
                    success: function (respuestaAPI) {
                        console.log("🌐 Respuesta de API:", respuestaAPI);

                        if (respuestaAPI != 'error') {
                            console.log("🎯 Datos de API procesados:", respuestaAPI);
                            // ... resto del código
                        } else {
                            console.log("❌ Error en consulta API");
                            // ... manejo de error
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("💥 Error AJAX:", {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                    }
                })
            }
        },
        error: function (xhr, status, error) {
            console.error("💥 Error en primera consulta:", {
                status: status,
                error: error,
                response: xhr.responseText
            });
        }
    })
})// TIPO DOCUMENTO 0000000 PARA BOLETAS SIN DOCUMENTO
$("#tipoDoc").on('change', function () {
    let numDocumento = "00000000";
    let tipoDocu = $("#tipoDoc").val();
    let datos = { "numDocumento": "00000000" };
    ////(numDocumento)
    $.ajax({
        method: "POST",
        url: 'ajax/clientes.ajax.php',
        data: datos,
        dataType: "json",

        beforeSend: function () {
            $("#reloadC").show(50).html("<img src='vistas/img/reload1.svg'> ");

        },
        success: function (respuesta) {
            ////(respuesta)
            if (respuesta != false) {
                if (tipoDocu == 0) {
                    $('#razon_social').val(respuesta['nombre']);
                    $('#docIdentidad').val(respuesta['documento']);
                    $('#direccion').val(respuesta['direccion']);
                    //$('#ubigeo').val(respuesta['ruc']);
                    $('#celular').val(respuesta['telefono']);
                    $('#idProveedor').val(respuesta['id']);
                    $("#reloadC").hide();
                } else {
                    $("#reloadC").hide();
                    $('#razon_social').val('');
                    $('#docIdentidad').val('');
                    $('#direccion').val('');
                    $('#celular').val('');
                    $('#ubigeo').val('');
                    $('#docIdentidad').focus();
                }
            }
        }
    });
})

// BUSCAR CLIENTE PARA COMPROBANTE|
$("#docIdentidad").keyup(function () {
    let numeroDoc = $(this).val();
    let tipoDocumento = $("#tipoDoc").val();
    let datos = { "numeroDocP": numeroDoc, "tipoDocumentoP": tipoDocumento };
    $.ajax({
        method: "POST",
        url: 'ajax/proveedores.ajax.php',
        data: datos,
        // dataType: "json",

        beforeSend: function () {
            //$("#reloadC").show(50).html("<img src='vistas/img/reload1.svg'> ");

        },
        success: function (respuesta) {

            if (respuesta != false) {
                if (numeroDoc != '' && numeroDoc.length > 3) {
                    $(".resultadoProveedor").show();

                    $(".resultadoProveedor").html(respuesta);

                } else {
                    $(".resultadoProveedor").hide();

                }

            } else {
                $(".resultadoProveedor").hide();
            }
        }
    })
})

// AGREGAR CLIENTE A LOS INPUTS 
$(document).on("click", ".btn-add-p", function (e) {
    e.preventDefault();
    let tipoDocumento = $("#tipoDoc").val();
    let idProveedor = $(this).attr('idProveedor');
    let datos = new FormData();
    datos.append('idProveedor', idProveedor);
    $.ajax({
        url: "ajax/proveedores.ajax.php",
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (respuesta) {
            //(respuesta);
            if (tipoDocumento == 1) {

                $('#idProveedor').val(respuesta['id']);
                $('#razon_social').val(respuesta['nombre']);
                $('#docIdentidad').val(respuesta['documento']);
                $('#direccion').val(respuesta['direccion']);
                $('#ubigeo').val(respuesta['ubigeo']);
                $('#celular').val(respuesta['telefono']);
                $('#email').val(respuesta['email']);
                $(".resultadoProveedor").hide();
                $("#reloadC").hide();
            } else {

                $('#idProveedor').val(respuesta['id']);
                $('#razon_social').val(respuesta['razon_social']);
                $('#docIdentidad').val(respuesta['ruc']);
                $('#direccion').val(respuesta['direccion']);
                $('#ubigeo').val(respuesta['ubigeo']);
                $('#celular').val(respuesta['telefono']);
                $('#email').val(respuesta['email']);
                $(".resultadoProveedor").hide();
                $("#reloadC").hide();
            }

        }
    })
})
