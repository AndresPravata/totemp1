<?php
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "turnos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

date_default_timezone_set("America/Argentina/Buenos_Aires");
$fechaActual = date("d/m/Y");

// Obtener los datos de la tabla turnos ordenados por estado y nombre_turno, excluyendo los turnos con estado 'finalizado'
$sql = "SELECT nombre_turno, numero_box, estado FROM turnos WHERE estado != 'finalizado' and date(fecha_hora_inicio) = STR_TO_DATE('$fechaActual' , '%d/%m/%Y')  ORDER BY estado, nombre_turno";
$result = $conn->query($sql);

// Lógica para separar los turnos actuales y siguientes por box
$turnos_actuales = array('comercial' => '', 'veterinaria' => array());
$turnos_siguientes = array();

while ($row = $result->fetch_assoc()) {
    $nombre_turno = $row["nombre_turno"];
    $numero_box = $row["numero_box"];
    $estado = $row["estado"];
    if ($numero_box == 4 && $estado != 'espera') {
        $turnos_actuales['comercial'] = $nombre_turno;
    } else {
        if ($estado == 'espera') {
            $turnos_siguientes[] = array("nombre_turno" => $nombre_turno, "numero_box" => $numero_box);
        } else {
            $turnos_actuales['veterinaria'][$numero_box] = $nombre_turno;
        }
    }
}

// Función para asignar turnos actuales en veterinaria
function asignarTurnoActual(&$turnos_actuales, &$turnos_siguientes, $numero_box, $conn) {
    $turno_asignado = false;
    
    foreach ($turnos_siguientes as $index => $turno) {
        if ($turno['numero_box'] == $numero_box) {
            // Verificar si el box está ocupado
            $box_ocupado = !empty($turnos_actuales['veterinaria'][$numero_box]);

            if (!$box_ocupado) {
                // Actualizar el estado del turno en la base de datos de 'espera' a 'actual'
                $nombre_turno = $turno['nombre_turno'];
                $sql_update_estado = "UPDATE turnos SET estado = 'actual' WHERE nombre_turno = '$nombre_turno' AND estado = 'espera' AND numero_box = $numero_box LIMIT 1";
                $conn->query($sql_update_estado);

                // Cambiar el estado del turno en 'turnos_siguientes' a 'actual'
                $turnos_siguientes[$index]['estado'] = 'actual';

                // Asignar el turno a 'turnos_actuales'
                $turnos_actuales['veterinaria'][$numero_box] = $turno['nombre_turno'];

                // Eliminar el turno de 'turnos_siguientes'
                unset($turnos_siguientes[$index]);

                $turno_asignado = true;
                break;
            }
        }
    }

    return $turno_asignado;
}


// Verificar si hay turnos en Box 1, Box 2 y Box 3 de veterinaria y si no los hay, se establecerán como vacíos
if (empty($turnos_actuales['veterinaria'][1])) {
    $turnos_actuales['veterinaria'][1] = '';
    // Si no hay turno actual en Box 1, intentar asignar uno desde 'turnos_siguientes'
    asignarTurnoActual($turnos_actuales, $turnos_siguientes, 1, $conn);
}
if (empty($turnos_actuales['veterinaria'][2])) {
    $turnos_actuales['veterinaria'][2] = '';
    // Si no hay turno actual en Box 2, intentar asignar uno desde 'turnos_siguientes'
    asignarTurnoActual($turnos_actuales, $turnos_siguientes, 2, $conn);
}
if (empty($turnos_actuales['veterinaria'][3])) {
    $turnos_actuales['veterinaria'][3] = '';
    // Si no hay turno actual en Box 3, intentar asignar uno desde 'turnos_siguientes'
    asignarTurnoActual($turnos_actuales, $turnos_siguientes, 3, $conn);
}


// Verificar si hay turnos de espera para comercial (Box 4) y si no los hay, se establecerá el array de turnos_siguientes como vacío
if (empty($turnos_siguientes)) {
    $turnos_siguientes = array();
} else {
    // Obtener el primer turno de espera de comercial (Box 4)
    $primer_turno_espera_comercial = reset($turnos_siguientes);
    
    // Verificar si el primer turno de espera tiene asignado el Box 4
    if ($primer_turno_espera_comercial['numero_box'] == 4) {
        // Verificar si el Box 4 está libre (vacío)
        if (empty($turnos_actuales['comercial'])) {
            // Cambiar el estado y el número de Box del primer turno de espera (Box 4)
            $sql_update_comercial = "UPDATE turnos SET estado = 'actual', numero_box = 4 WHERE nombre_turno = '{$primer_turno_espera_comercial['nombre_turno']}' AND estado = 'espera' AND numero_box = 4 LIMIT 1";
            $conn->query($sql_update_comercial);
            
            // Actualizar el array $turnos_actuales con el nuevo turno asignado (Box 4)
            $turnos_actuales['comercial'] = $primer_turno_espera_comercial['nombre_turno'];
            
            // Eliminar el turno de espera de $turnos_siguientes
            unset($turnos_siguientes[key($turnos_siguientes)]);
        }
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <title>Visor de Turnos</title>
    
    <style>
body {
    margin: 0;
    overflow: hidden;
}

.background-image {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    background: url('veterinariaDrLuffi.png') center/cover no-repeat;
    background-color: rgba(0, 0, 0, 0.5);
}

.carousel-item {
    height: 40.5rem;
    background: transparent;
    margin-top: 16.20rem;
    max-width: 100%;
}

.container {
    position: relative;
}

/* Estilos para la disposición de los turnos en la parte inferior de la pantalla */
#turnos-container {
    position: fixed;
    bottom: -2rem;
    left: 0;
    width: 100%;
    display: flex;
    justify-content: flex-end;
    text-align: center;
}

#comercial-container,
#veterinaria-container {
    width: 45%;
    border: none;
    padding: 10px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
}

.box-turn {
    font-weight: bold;
    font-size: 40px;
    margin: 5px 0;
}

/* Estilos para el Box 2 de Veterinaria */
#veterinaria-container .box2 {
    display: flex;
    margin-top: 40px;
    margin-bottom: 40px;
}

/* Estilo para cada turno individual en #veterinaria-container */
#veterinaria-container .box2 > div {
    display: flex;
    margin-left: 1px !important; /* Ajusta el valor según sea necesario para cada elemento */
}

/* Estilos para los elementos individuales en #veterinaria-container */
#veterinaria-container #box1-turn {
    position: absolute;
  top: 10px;
  right: 10px;
}

#veterinaria-container #box2-turn {
    position: absolute;
  top: 10px;
  right: 10px;
}



/* Estilo para comercial-turn en #comercial-container */
#comercial-container #comercial-turn {
    position: absolute;
  top: 10px;
  right: 10px;
}
</style>

</head>
<body>
<div class="background-image"></div>
  <div class="container">
    <div id="miCarrusel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="slider1.jpg" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="slider2.jpg" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="slider3.jpg" class="d-block w-100" alt="...">
        </div>
      </div>
    </div>
  </div>

    <!-- Nueva sección para mostrar los turnos en la parte inferior de la pantalla -->
    <div id="turnos-container">
        <!-- Sección de Veterinaria -->
        <div id="veterinaria-container">
    <div class="box2">
        <div>
            <p class="box-turn" style="color: white;" id="box1-turn"> </p>
        </div>

        <div>
            <p class="box-turn" style="color: white;" id="box2-turn"> </p>
        </div>
       
        <div>
            <p class="box-turn" style="color: white;" id="box3-turn"> </p>
        </div>
    </div>
</div>

<!-- Sección de Comercial -->
<div id="comercial-container">
    <div class="box2">
        <p class="box-turn" style="color: white;" id="comercial-turn"> </p>
    </div>
    
    <audio id="sound-box1">
    <source src="turno.mp3" type="audio/mp3">
</audio>
<audio id="sound-box2">
    <source src="turno.mp3" type="audio/mp3">
</audio>
<audio id="sound-box3">
    <source src="turno.mp3" type="audio/mp3">
</audio>
<audio id="sound-box4">
    <source src="turno.mp3" type="audio/mp3">
</audio>
<!-- Repite esto para todos los boxes -->

    <script>
        
    function llamarUpdate() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var turnosActuales = JSON.parse(xhr.responseText);
                // Actualiza los elementos en el DOM con los valores de turnosActuales
                actualizarElementos(turnosActuales);
                ejecutarSonido(turnosActuales);
            } else {
                console.log('Error al obtener los turnos actuales');
            }
        }
    };
    xhr.open('GET', 'update_turns.php', true);
    xhr.send();
}

function actualizarElementos(turnosActuales) {
   
    // Actualiza los elementos en el DOM con los valores de turnosActuales
    document.getElementById('box1-turn').textContent = turnosActuales['veterinaria'][1] || '';
    document.getElementById('box2-turn').textContent = turnosActuales['veterinaria'][2] || '';
    document.getElementById('box3-turn').textContent = turnosActuales['veterinaria'][3] || '';
    document.getElementById('comercial-turn').textContent = turnosActuales['comercial'] || '';
}

// Llamar a la función cada 2 segundos
setInterval(llamarUpdate, 2000);
var turnosViejos; // Al inicio, los turnos viejos son nulos

function ejecutarSonido(turnosActuales) {
    if ((JSON.stringify(turnosViejos))!==(JSON.stringify(turnosActuales))) {
        // Los turnos actuales son diferentes de los turnos viejos
        var sound = new Audio('http://localhost/p1/turno.mp3');
        sound.play();
        console.log("Entre en el if")
    }
    else{
        
    }
    // Actualizar los turnos viejos con los nuevos turnos actuales
    turnosViejos = turnosActuales;
    console.log("Viejos "+JSON.stringify(turnosViejos) + " Actuales "+ JSON.stringify(turnosActuales))

}


        var fechaHoraActual = new Date();


        var hora = fechaHoraActual.getHours();
        if (hora >= 22) {
            function resetNumeroTurnoV() {
      // Objeto XMLHttpRequest para realizar la llamada AJAX
      var xhttp = new XMLHttpRequest();

      // Configurar la llamada AJAX
      xhttp.open("POST", "guardar_numero_turnoV.php", true);

      // Definir el encabezado para indicar que se enviará un formulario
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      // Preparar los datos a enviar
      var datos = "numeroTurno=1"; // Establecer el número de turno en 0

      // Enviar la llamada AJAX
      xhttp.send(datos);

    }
    resetNumeroTurnoV();
            function resetNumeroTurnoC() {
      // Objeto XMLHttpRequest para realizar la llamada AJAX
      var xhttp = new XMLHttpRequest();

      // Configurar la llamada AJAX
      xhttp.open("POST", "guardar_numero_turno.php", true);

      // Definir el encabezado para indicar que se enviará un formulario
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

      // Preparar los datos a enviar
      var datos = "numeroTurno=0"; // Establecer el número de turno en 0

      // Enviar la llamada AJAX
      xhttp.send(datos);

     
    }
    resetNumeroTurnoC();

    
        console.log("La hora es mayor a 22. ");
        }
        

     
</script>
</body>
</html>