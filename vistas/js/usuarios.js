
//   cerrarSession();

function cerrarSession() {
  let datos = { cerrarS: "cerrarS" };
  $.ajax({
    url: "ajax/usuarios.ajax.php",
    method: "POST",
    data: datos,
    beforeSend: function () { },
    success: function (respuesta) {
      if (respuesta == "ok") {
        window.location = "salir";
      }
    },
  });
}
//LOGIN USUARIOS
//LOGIN USUARIOS
$("#logUser").click(function (e) {
  e.preventDefault();
  var conectar = $("#conectado").val();

  if (conectar == "ok") {
    grecaptcha.ready(function () {
      grecaptcha
        .execute("6LdTdcggAAAAAPzue7S6tJumtvWlWCS_Pa1kxPVE", {
          action: "validarUsuario",
        })
        .then(function (token) {
          $("#form-login").prepend(
            '<input type="hidden" name="token" id="token" value="' + token + '" >'
          );
          $("#form-login").prepend(
            '<input type="hidden" name="action" id="action" value="validarUsuario" >'
          );

          var token = $("#token").val();
          var action = $("#action").val();
          var ingUsuario = $("#ingUsuario").val();
          var ingPassword = $("#ingPassword").val();

          let datos = {
            ingUsuario: ingUsuario,
            ingPassword: ingPassword,
            token: token,
            conectar: conectar,
          };

          $.ajax({
            url: "ajax/usuarios.ajax.php",
            method: "POST",
            data: datos,
            dataType: "json",
            beforeSend: function () {
              $("#resultLogin").html(
                '<img src="vistas/img/reload1.svg" width="50px" style="display:block; margin:0 auto;">'
              );
            },
            success: function (respuesta) {
              console.log("Respuesta del servidor:", respuesta);

              if (respuesta.status == "success") {
                $("#resultLogin").html(
                  '<div class="alert alert-success">✅ Login exitoso. Redireccionando...</div>'
                );
                window.location.href = "?ruta=" + respuesta.redirect;
              } else {
                $("#resultLogin").html(
                  '<div class="alert alert-danger">❌ ' + (respuesta.message || 'Error desconocido') + '</div>'
                );
                if (typeof grecaptcha !== 'undefined') {
                  grecaptcha.reset();
                }
              }
            },
            error: function (xhr, status, error) {
              console.error("Error en AJAX:", error);
              console.log("Respuesta del servidor (texto):", xhr.responseText);

              // Intentar parsear la respuesta manualmente
              try {
                var respuesta = JSON.parse(xhr.responseText);
                $("#resultLogin").html(
                  '<div class="alert alert-danger">❌ ' + (respuesta.message || 'Error desconocido') + '</div>'
                );
              } catch (e) {
                // Si no es JSON, mostrar el texto como está
                $("#resultLogin").html(
                  '<div class="alert alert-danger">❌ Error del servidor<br><small>' + xhr.responseText.substring(0, 200) + '</small></div>'
                );
              }
            }
          });
        });
    });
  } else {
    var ingUsuario = $("#ingUsuario").val();
    var ingPassword = $("#ingPassword").val();

    let datos = {
      ingUsuario: ingUsuario,
      ingPassword: ingPassword,
      conectar: conectar,
    };

    $.ajax({
      url: "ajax/usuarios.ajax.php",
      method: "POST",
      data: datos,
      dataType: "json",
      beforeSend: function () {
        $("#resultLogin").html(
          '<img src="vistas/img/reload1.svg" width="50px" style="display:block; margin:0 auto;">'
        );
      },
      success: function (respuesta) {
        console.log("Respuesta del servidor:", respuesta);

        if (respuesta.status == "success") {
          $("#resultLogin").html(
            '<div class="alert alert-success">✅ Login exitoso. Redireccionando...</div>'
          );
          window.location.href = "?ruta=" + respuesta.redirect;
        } else {
          $("#resultLogin").html(
            '<div class="alert alert-danger">❌ ' + (respuesta.message || 'Error desconocido') + '</div>'
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en AJAX:", error);
        console.log("Respuesta del servidor (texto):", xhr.responseText);

        try {
          var respuesta = JSON.parse(xhr.responseText);
          $("#resultLogin").html(
            '<div class="alert alert-danger">❌ ' + (respuesta.message || 'Error desconocido') + '</div>'
          );
        } catch (e) {
          $("#resultLogin").html(
            '<div class="alert alert-danger">❌ Error del servidor<br><small>' + xhr.responseText.substring(0, 200) + '</small></div>'
          );
        }
      }
    });
  }
});
// SUBIENDO LA FOTO DEL USUARIO
$(".nuevaFoto").change(function () {
  let imagen = this.files[0];

  if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {
    $(".nuevaFoto").val("");
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "La imagen debe ser jpeg o png!",
      //footer: '<a href>Why do I have this issue?</a>'
    });
  } else if (imagen["size"] > 2000000) {
    $(".nuevaFoto").val("");
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "La imagen no debe pesar más de 2mb!",
      //footer: '<a href>Why do I have this issue?</a>'
    });
  } else {
    let datosImagen = new FileReader();
    datosImagen.readAsDataURL(imagen);
    $(datosImagen).on("load", function (event) {
      let rutaImagen = event.target.result;
      $(".previsualizar").attr("src", rutaImagen);
    });
  }
});

// Agregar USUARIO
$(".form-inserta").submit(function (e) {
  e.preventDefault();
  let datos = $(this).serialize();
  let formd = new FormData($("form.form-inserta")[0]);
  $.ajax({
    type: "POST",
    url: "ajax/usuarios.ajax.php",
    data: (datos, formd),
    cache: false,
    contentType: false,
    processData: false,
    success: function (respuesta) {
      $(".resultados").html(respuesta);
    },
  });
});
// EDITTAR USUARIO
$(document).on("click", ".btnEditarUsuario", function () {
  var idUsuario = $(this).attr("idUsuario");
  ////(idUsuario);
  var datos = new FormData();
  datos.append("idUsuario", idUsuario);
  $.ajax({
    url: "ajax/usuarios.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      $("#editarNombre").val(respuesta["nombre"]);
      $("#editarUsuario").val(respuesta["usuario"]);
      $("#editarPerfil").html(respuesta["perfil"]);
      $("#editarPerfil").val(respuesta["perfil"]);
      $("#editarDni").val(respuesta["dni"]);
      $("#editarEmail").val(respuesta["email"]);
      $("#passwordActual").val(respuesta["password"]);
      $("#fotoActual").val(respuesta["foto"]);

      if (respuesta["foto"] != "") {
        $(".previsualizar").attr("src", respuesta["foto"]);
      }
    },
  });
});
// ACTIVAR USUARIO|
$(document).on("change", "#usuarioEstado", function () {
  let idUsuario = $(this).attr("idUsuario");
  if ($(this).is(":checked")) {
    estadoUsuario = 1;
  } else {
    estadoUsuario = 0;
  }

  let datos = { activarId: idUsuario, activarUsuario: estadoUsuario };

  $.ajax({
    url: "ajax/usuarios.ajax.php",
    method: "POST",
    data: datos,
    success: function (respuesta) {
      if (window.matchMedia("(max-width:767px)").matches) {
      }
    },
  });
  if (estadoUsuario == 0) {
    $(this).removeClass("btn-success");
    $(this).addClass("btn-danger");
    $(this).html("desactivado");
    $(this).attr("estadoUsuario", 1);
  } else {
    $(this).removeClass("btn-danger");
    $(this).addClass("btn-success");
    $(this).html("Activado");
    $(this).attr("estadoUsuario", 0);
  }
});
// VALIDAR NO REPETIR USUARIO

$(document).on("change", "#nuevoUsuario", function () {
  $(".alert").remove();

  let usuario = $(this).val();
  let datos = new FormData();
  datos.append("validarUsuario", usuario);

  $.ajax({
    url: "ajax/usuarios.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      //    //("respuesta", respuesta);
      if (respuesta) {
        $("#nuevoUsuario").val("");
        $("#nuevoUsuario")
          .parent()
          .before(
            '<div class="alert alert-warning" style="display:none;">Este usuario ya existe!</div>'
          );
        $(".alert").show(500, function () {
          $(this).delay(3000).hide(500);
        });
      }
    },
  });
});
// ELIMINAR USUARIO
$(document).on("click", ".btnEliminarUsuario", function () {
  let idUsuario = $(this).attr("idUsuario");
  let fotoUsuario = $(this).attr("fotoUsuario");
  let usuario = $(this).attr("usuario");

  Swal.fire({
    title: "¿Estás seguro de eliminar este usuario?",
    text: "¡Si no lo está puede  cancelar la acción!",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Sí, eliminarlo!",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location =
        "index.php?ruta=usuarios&idUsuario=" +
        idUsuario +
        "&usuario=" +
        usuario +
        "&fotoUsuario=" +
        fotoUsuario;
      //   Swal.fire(
      //     'Deleted!',
      //     'Your file has been deleted.',
      //     'success'
      //   )
    }
  });
});
//BUSCAR DNI RENIEC
$(".form-inserta").on("change", "#nuevoDni", function (e) {
  e.preventDefault();
  let dni = $(this).val();
  let datos = { dni: dni };
  $.ajax({
    method: "POST",
    url: "ajax/usuarios.ajax.php",
    data: datos,
    dataType: "json",
    success: function (respuesta) {
      $("#nuevoDni").val(respuesta["dni"]);
      $("#nuevoNombre").val(respuesta["nombre"]);
    },
  });
});
// SOLO INGRESAR NUMEROS CAMPO DNI
$("#nuevoDni").keyup(function () {
  var ruc = $(this).val();

  //this.value = (this.value + '').replace(/[^0-9]/g, '');
  if (!$.isNumeric(ruc)) {
    //dni = dni.substr(0,(dni.length -1));
    ruc = ruc.replace(/[^0-9]/g, "");
    $("#nuevoDni").val(ruc);
  }
});
