// Agregar esta función al inicio del archivo productos.js
function generarCodigoYSerie() {
    const ahora = new Date();
    const año = ahora.getFullYear().toString().slice(-2);
    const categoria = $("#nuevaCategoria").val() || '01'; // ID de categoría o '01' por defecto
    const random = Math.floor(Math.random() * 90 + 10); // 10-99

    // Código: CAT-AÑO-RANDOM (ej: 01-24-57)
    let codigo = `C${categoria}-${año}-${random}`;

    // Serie: S-AÑO-TIMESTAMP-CORTO (ej: S24-8A3F)
    let serie = `S${año}-${Math.random().toString(36).substr(2, 4).toUpperCase()}`;

    $("#nuevoCodigo").val(codigo);
    $("#nuevaSerie").val(serie);

    return { codigo: codigo, serie: serie };
}

// Modificar el evento click del botón
$(".btn-nuevo-producto").on("click", function (e) {
    e.preventDefault();

    // Generar código y serie automáticamente si están vacíos
    if ($("#nuevoCodigo").val() == '' || $("#nuevaSerie").val() == '') {
        generarCodigoYSerie();
    }

    // Calcular precios
    calcularPrecios();

    let formData = new FormData($("#formProductos")[0]);

    // Validación (puedes quitar nuevoCodigo y nuevaSerie de la validación)
    if ($("#nuevaCategoria").val() == '' ||
        $("#nuevaDescripcion").val() == '' ||
        $("#nuevoStock").val() == '' ||
        $("#nuevoPrecioUnitario").val() == '') {

        // Tu código de alerta...
    } else {
        // Tu código AJAX...
    }
});

$(".btn-nuevo-producto").on("click", function (e) {
    e.preventDefault();

    // Calcular los valores antes de enviar
    calcularPrecios();

    let formData = new FormData($("#formProductos")[0]);

    // Validar campos requeridos
    if ($("#nuevaCategoria").val() == '' ||
        $("#nuevaDescripcion").val() == '' ||
        $("#nuevoStock").val() == '' ||
        $("#nuevoPrecioUnitario").val() == '' ||
        $("#nuevoValorUnitario").val() == '' ||
        $("#nuevoigv").val() == '' ||
        $("#nuevoPrecioCompra").val() == '') {

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
        Toast.fire({
            icon: 'warning',
            title: 'Por favor, complete todos los campos requeridos',
        })
    } else {
        $.ajax({
            method: "POST",
            url: 'ajax/productos.ajax.php',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $(".reload-all").fadeIn(50).html("");
            },
            success: function (respuesta) {
                console.log(respuesta);
                $(".reload-all").fadeOut(50);
                $(".modal-backdrop, #modalAgregarProducto").hide();

                if (respuesta == 'ok') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5500,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    })
                    Toast.fire({
                        icon: 'success',
                        title: 'AGREGADO CORRECTAMENTE',
                        html: 'SE AGREGÓ EL PRODUCTO O SERVICIO'
                    })
                    // Recargar la tabla de productos
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al guardar el producto: ' + respuesta
                    })
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor'
                })
            }
        })
    }
});

// Función para calcular precios y asegurar que los campos tengan valores
function calcularPrecios() {
    // Asegurarse de que los campos hidden tengan valores
    let valorUnitario = $("#nuevoValorUnitario").val();
    let igv = $("#nuevoigv").val();
    let precioCompra = $("#nuevoPrecioCompra").val();

    // Si están vacíos, calcularlos
    if (!valorUnitario || !igv || !precioCompra) {
        // Tu lógica de cálculo aquí
        let precioUnitario = parseFloat($("#nuevoPrecioUnitario").val()) || 0;

        // Ejemplo de cálculo (ajusta según tu lógica)
        let igvCalculado = precioUnitario * 0.18;
        let valorUnitarioCalculado = precioUnitario - igvCalculado;
        let precioCompraCalculado = valorUnitarioCalculado;

        $("#nuevoValorUnitario").val(valorUnitarioCalculado.toFixed(2));
        $("#nuevoigv").val(igvCalculado.toFixed(2));
        $("#nuevoPrecioCompra").val(precioCompraCalculado.toFixed(2));
    }
}

$("#nuevaCategoria").change(function () {
    let idCategoria = $(this).val();

    let datos = new FormData();
    datos.append('idCategoria', idCategoria);

    $.ajax({
        url: 'ajax/productos.ajax.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (respuesta) {
            let fechaActual = obtenerFechaActualEnFormatoDeseado();

            if (!respuesta) {
                // Si no hay productos en esta categoría, empezar con 1
                let nuevoCodigo = idCategoria + "1";
                let nuevaSerie = nuevoCodigo + fechaActual;

                $("#nuevoCodigo").val(nuevoCodigo);
                $("#nuevaSerie").val(nuevaSerie);
            } else {
                // Si ya existen productos, obtener el último código y sumar 1
                let ultimoCodigo = respuesta["codigo"];
                // Extraer solo el número del código (remover el prefijo de categoría)
                let numeroProducto = parseInt(ultimoCodigo.replace(idCategoria, ''));
                let nuevoNumero = numeroProducto + 1;

                let nuevoCodigo = idCategoria + nuevoNumero;
                let nuevaSerie = nuevoCodigo + fechaActual;

                $("#nuevoCodigo").val(nuevoCodigo);
                $("#nuevaSerie").val(nuevaSerie);
            }
        }
    })
})

// FUNCION PARA OBTENER LA FECHA ACTUAL - AÑO
function obtenerFechaActualEnFormatoDeseado() {
    let fechaActual = new Date();
    let anio = fechaActual.getFullYear().toString();
    return anio;
}

// AGREGANDO PRECIO DE VENTA
function changePrecios() {
    let precio_unitario = $("#nuevoPrecioUnitario").val();
    let tipo_afectacion = $("#tipo_afectacion").val();

    let datos = { "precio_unitario": precio_unitario, "tipo_afectacion": tipo_afectacion };
    $.ajax({
        url: 'ajax/redondeos.ajax.php',
        method: 'POST',
        data: datos,
        dataType: "json",
        success: function (respuesta) {
            console.log(respuesta);
            $("#nuevoValorUnitario").val(respuesta['valor_unitario']);
            $("#nuevoigv").val(respuesta['igv_producto']);
            $("#nuevoPrecioCompra").val(respuesta['precio_compra']);
        }
    })

}
$("#nuevoPrecioUnitario").on('keyup', function () {
    changePrecios();
})
$("#tipo_afectacion").on('change', function () {
    changePrecios();
})
// AGREGANDO PRECIO DE VENTA EDITAR
function changeEditarPrecios() {
    let precio_unitario = $("#editarPrecioUnitario").val();
    let tipo_afectacion = $("#editarAfectacion").val();
    ////(tipo_afectacion);
    let datos = { "precio_unitario": precio_unitario, "tipo_afectacion": tipo_afectacion };
    $.ajax({
        url: 'ajax/redondeos.ajax.php',
        method: 'POST',
        data: datos,
        dataType: "json",
        success: function (respuesta) {

            $("#editarValorUnitario").val(respuesta['valor_unitario']);
            $("#editarigv").val(respuesta['igv_producto']);
            $("#editarPrecioCompra").val(respuesta['precio_compra']);
        }
    })

}
$("#editarPrecioUnitario").on('keyup', function () {
    changeEditarPrecios();
})
$("#editarAfectacion").on('change', function () {
    changeEditarPrecios();
})

// SUBIENDO LA FOTO DEL PRODUCTO
$(".nuevaImagen").change(function () {
    let imagen = this.files[0];

    if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {
        $(".nuevaImagen").val('');
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'La imagen debe ser jpeg o png!'
            //footer: '<a href>Why do I have this issue?</a>'
        })
    } else if (imagen["size"] > 2000000) {

        $(".nuevaImagen").val('');
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'La imagen no debe pesar más de 2mb!'
            //footer: '<a href>Why do I have this issue?</a>'
        })
    }
    else {
        let datosImagen = new FileReader;
        datosImagen.readAsDataURL(imagen);
        $(datosImagen).on("load", function (event) {
            let rutaImagen = event.target.result;
            $(".previsualizar").attr("src", rutaImagen);

        })
    }
})

// EDITAR PRODUCTO
$(document).on("click", ".btnEditarProducto", function () {
    let idProducto = $(this).attr("idProducto");
    let datos = new FormData();
    datos.append('idProducto', idProducto);
    $.ajax({
        url: 'ajax/productos.ajax.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',

        success: function (respuesta) {
            // //(respuesta)
            $("#editarid").val(respuesta["id"]);
            $("#editarCodigo").val(respuesta["codigo"]);
            $("#editarSerie").val(respuesta["serie"]);
            $("#editarDescripcion").val(respuesta["descripcion"]);
            $("#editarStock").val(respuesta["stock"]);
            $("#editarPrecioCompra").val(respuesta["precio_compra"]);
            $("#editarPrecioUnitario").val(respuesta["precio_unitario"]);
            $("#editarValorUnitario").val(respuesta["valor_unitario"]);
            $("#editarigv").val(respuesta["igv"]);

            if (respuesta['imagen'] != '') {
                $("#imagenActual").val(respuesta['imagen']);
                $('.previsualizar').attr('src', respuesta['imagen']);
            }

            let datosCategoria = new FormData();
            datosCategoria.append('idCategoria', respuesta['id_categoria']);

            $.ajax({
                url: 'ajax/categorias.ajax.php',
                method: 'POST',
                data: datosCategoria,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (respuestac) {

                    $("#editarCategoria").val(respuestac['id']);
                    $("#editarCategoria").html(respuestac['categoria']);



                }
            })
            let datosCodigoAfectacion = new FormData();
            datosCodigoAfectacion.append('codigoAfectacion', respuesta['codigoafectacion']);
            $.ajax({
                url: 'ajax/sunat.ajax.php',
                method: 'POST',
                data: datosCodigoAfectacion,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (afectacion) {
                    if (afectacion != false) {

                        $("#editarAfectacion").val(afectacion['codigo']);

                    }

                }
            })
            let datosUnidad = new FormData();
            datosUnidad.append('codUnidad', respuesta['codunidad']);

            $.ajax({
                url: 'ajax/sunat.ajax.php',
                method: 'POST',
                data: datosUnidad,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (unidad) {
                    ////(unidad)
                    if (unidad != false) {

                        $("#editarUnidadMedida").val(unidad['codigo']);

                    }

                }
            })
        }
    })
})

// ELIMINAR PRODUCTO
$(document).on("click", ".btnEliminarProducto", function () {
    let idProducto = $(this).attr("idProducto");
    let codigo = $(this).attr("codigo");
    let imagen = $(this).attr("imagen");
    // DATOS PARA ENIAR POR POST
    let datos = new FormData();
    datos.append('idEliminarProducto', idProducto);
    datos.append('codigo', codigo);
    datos.append('imagen', imagen);
    // datos = {"idProducto":idProducto, "codigo":codigo, "imagen":imagen};
    Swal.fire({
        title: '¿Estás seguro de eliminar este producto?',
        text: "¡Si no lo está puede  cancelar la acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo!'
    }).then((result) => {
        if (result.isConfirmed) {

            window.location = "index.php?ruta=productos&idProducto=" + idProducto + "&codigo=" + codigo + "&imagen=" + imagen;
            // $.ajax({
            //     url: 'ajax/productos.ajax.php',
            //     method: 'POST',
            //     data: datos,
            //     cache: false,
            //     contentType: false,
            //     processData: false,
            //     success: function(respuesta){
            //         //(respuesta);
            //         // $('#resultados').html(respuesta);

            //     }
            // })
        }
    })

})

// LISTAR PRODUCTOS CON BUSCADOR
let perfilOculto = $('#perfilOculto').val();
function loadProductos(page) {
    let searchProducto = $("#searchProducto").val();
    let selectnum = $("#selectnum").val();
    let parametros = { "action": "ajax", "page": page, "searchProducto": searchProducto, "selectnum": selectnum, "dp": "dp", "perfilOculto": perfilOculto };

    $.ajax({
        url: 'vistas/tables/dataTables.php',
        // method: 'GET',
        data: parametros,
        // cache: false,
        // contentType: false,
        // processData: false,  
        beforeSend: function () {
            //  $("body").append(loadp);
        },
        success: function (data) {
            $(".reloadp").hide();
            $('.body-productos').html(data);
        }
    })
};

loadProductos(1);

// });

$(document).on("click", ".siu", function () {
    let id = $(this).attr('idm');

    let modo = $('.unidad-me .unidadmedida').val();
    let datos = { 'idUnidad': id, 'modo': modo };

    $.ajax({
        method: "POST",
        url: "ajax/productos.ajax.php",
        data: datos,
        beforeSend: function () {

        },
        success: function (respuesta) {
            if (modo == "s") {

                $(".unidad-me #sie" + id).addClass("unidadsi");
                $(".unidad-me #sie" + id).html("Sí");
                $(".unidad-me #noe" + id).html("||");
                $(".unidad-me #sie" + id).removeClass("alterno2");
                $(".unidad-me #noe" + id).addClass("alterno2");
            }
        }
    });
});

$(document).on("click", ".nou", function () {
    let id = $(this).attr('idm');
    let modo = $('.unidadmedidan').val();
    let datos = { 'idUnidad': id, 'modo': modo };
    $.ajax({
        method: "POST",
        url: "ajax/productos.ajax.php",
        data: datos,
        beforeSend: function () {

        },
        success: function (respuesta) {

            if (modo == "n") {

                $(".unidad-me #noe" + id).addClass("unidadno");
                $(".unidad-me #noe" + id).html("No");
                $(".unidad-me #sie" + id).html("||");
                $(".unidad-me #sie" + id).addClass("alterno2");
                $(".unidad-me #noe" + id).removeClass("alterno2");


            }
        }
    });
});

// function loadEmaiChange(){
//     let modo = $(".unidadmedida:checked").val();

//      if(modo == "s"){

//         $("#sie").addClass("emailsi");
//         $("#sie").html("Sí");
//         $("#noe").html("||");
//         $("#sie").removeClass("alterno");
//         $("#noe").addClass("alterno");


//         }else{
//             $("#noe").addClass("emailno");
//             $("#noe").html("No");
//             $("#sie").html("||");
//             $("#sie").addClass("alterno");
//             $("#noe").removeClass("alterno");
//             $('.email-colunma').hide();
//         }
//     };

$(document).on("change keyup", ".cantidad-stock", function () {
    let cantidad = $(this).val();
    let idProducto = $(this).attr("idProducto");
    let datos = { "idProducto": idProducto };
    $.ajax({
        method: "POST",
        url: "ajax/productos.ajax.php",
        data: datos,
        dataType: "json",
        beforeSend: function () {

        },
        success: function (respuesta) {

            let stockProducto = respuesta['stock'];
            let totalStock = stockProducto - cantidad;
            $(".stock" + idProducto).html(totalStock);

            if (Number(totalStock) <= 20) {
                $(".stock" + idProducto).removeClass('btn-primary').addClass('btn-danger');

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    // width: 600,
                    // padding: '3em',
                    showConfirmButton: false,
                    timer: 6000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'warning',
                    title: `<h4>El stock de este producto está llegando a su límite</h4>`,

                })
            } else {
                $(".stock" + idProducto).removeClass('btn-danger').addClass('btn-primary');
            }
            if (Number(totalStock) == 0 || cantidad >= Number(stockProducto)) {

                $("#cantidad" + idProducto).val(stockProducto);
                $(".stock" + idProducto).html(0);

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    // width: 600,
                    // padding: '3em',
                    showConfirmButton: false,
                    timer: 6000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'warning',
                    title: `<h4>El stock de este producto llegó a su límite</h4>`,

                })
            }
            // if(cantidad < 1 || cantidad == ''){
            //     $("#cantidad"+idProducto).val(1);
            // }

        }
    })
});


// REPONER STOCK ======================
$(document).on('click', '.btn-reponer-stock', function () {
    let idVenta = $(this).attr('idVenta');
})


//Limpiar inputs de modales de modulo de compras

$('#modalAgregarProducto').on('hidden.bs.modal', function () {
    $('#formProductos')[0].reset();
});