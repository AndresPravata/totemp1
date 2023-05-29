$(document).ready(function() {
    // Manejar el evento de envío del formulario
    $("#formulario-turno").submit(function(event) {
      // Evitar que se envíe el formulario de forma predeterminada
      event.preventDefault();
      
      // Obtener los valores de los campos del formulario
      var nombreTurno = $("#nombre_turno").val();
      var fechaHoraInicio = $("#fecha_hora_inicio").val();
      var fechaHoraFin = $("#fecha_hora_fin").val();
      var veterinarioId = $("#veterinario_id").val();
      var numeroBox = $("#numero_box").val();
      
      // Crear el objeto de datos a enviar
      var datos = {
        nombre_turno: nombreTurno,
        fecha_hora_inicio: fechaHoraInicio,
        fecha_hora_fin: fechaHoraFin,
        veterinario_id: veterinarioId,
        numero_box: numeroBox
      };
      
      // Realizar la solicitud AJAX
      $.ajax({
        url: "guardar.php",
        type: "POST",
        data: datos,
        success: function(response) {
          // Mostrar la respuesta del servidor
          alert(response);
        },
        error: function(xhr, status, error) {
          // Mostrar el mensaje de error en caso de falla
          console.error(error);
        }
      });
    });
  });
  