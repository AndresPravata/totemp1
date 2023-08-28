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
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    
    <title>Visor de Turnos</title>
    
    <style>
        body {
        background-color: transparent;
        background-color: black;
    }

    /* Estilos para la tabla */
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, tr, td {
        border: 18px solid black;
        padding: 8px;
        text-align: center;
    }

    /* Estilo específico para el turno actual de comercial */
    #current-turn {
        font-size: 100px;
        font-weight: bold; /* Texto en negrita */
    }

    /* Estilo para los turnos en Box 1 y Box 2 de veterinaria */
    #box1-turn, #box2-turn {
        font-weight: bold; /* Texto en negrita */
        font-size: 100px; /* Tamaño de fuente aumentado */
    }

    /* Nuevos estilos para la disposición de los turnos en la parte inferior de la pantalla */
    #turnos-container {
        position: fixed;
        bottom: 10px; /* Ajusta el margen inferior para controlar la distancia del borde inferior */
        left: 0;
        width: 100%;
        display: flex;
        justify-content: flex-end; /* Mover los elementos hacia la derecha */
        text-align: center; /* Centrar el contenido horizontalmente */
    }

    #comercial-container,
    #veterinaria-container {
        width: 45%;
        border: none;
        padding: 10px;
        text-align: center; /* Centrar el contenido horizontalmente */
        display: flex; /* Mostrar los elementos internos en línea */
        flex-direction: column; /* Alinear los elementos internos en columna */
        justify-content: center; /* Centrar los elementos internos verticalmente */
        align-items: center; /* Centrar los elementos internos horizontalmente */
    }

    /* Nuevos estilos para la línea vertical */
    .divider {
        position: relative;
        width: 5px;
        height: 100%;
        background-color: black;
        margin: 0 auto; /* Centrar horizontalmente */
    }

    .box-turn {
        font-weight: bold;
        font-size: 50px; /* Tamaño de fuente reducido */
        margin: 5px 0; /* Margen superior e inferior para los turnos */
    }

    /* Nuevos estilos para el Box 2 de Veterinaria */
    #veterinaria-container .box2 {
        display: flex; /* Mostrar los elementos internos en línea */
        justify-content: space-between; /* Espaciado uniforme entre los elementos internos */
        margin-top: 40px; /* Espacio entre Box 1 y Box 2 */
        margin-bottom: 40px; /* Espacio inferior para separar de los turnos */
    }

    /* Margen a la derecha del primer turno en Veterinaria */
    #veterinaria-container .box2 > div:first-child {
        margin-right: 60px; /* Ajusta este valor según sea necesario */
    }

    /* Margen a la derecha del segundo turno en Veterinaria */
    #veterinaria-container .box2 > div:last-child {
        margin-left: 50px; /* Ajusta este valor según sea necesario */
    }

    #comercial-container {
        display: flex;
        align-items: center; /* Centrar verticalmente el contenido */
        height: 235px; /* Ajusta esta altura según tus necesidades */
    }
    
</style>

</head>
<body>

    <!-- Nueva sección para mostrar los turnos en la parte inferior de la pantalla -->
    <div id="turnos-container">
        <!-- Sección de Veterinaria -->
        <div id="veterinaria-container">
            <div class="box2">
                <div>
                    <p class="box-turn" style="color: white;"><?php echo $turnos_actuales['veterinaria'][1]; ?> BOX 1</p>
                </div>
               
                <div>
                    <p class="box-turn" style="color: white;"><?php echo $turnos_actuales['veterinaria'][2]; ?> BOX 2</p>
                </div>
                <div>
                    <p class="box-turn" style="color: white;"><?php echo $turnos_actuales['veterinaria'][3]; ?> BOX 3</p>
                </div>
            </div>
        </div>

        <!-- Sección de Comercial -->
        <div id="comercial-container">
            <div class="box2">
            <p class="box-turn" style="color: white;"><?php echo $turnos_actuales['comercial']; ?> </p>
            </div>
        </div>
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
        console.log("Ejecutando actualización de turnos...");

        // Objeto XMLHttpRequest para realizar la llamada AJAX
        var xhttp = new XMLHttpRequest();

        // Configurar la llamada AJAX
        xhttp.onreadystatechange = function() {
            if (this.readyState === 4) {
                if (this.status === 200) {
                    
                    // Actualizar los datos en la página con la respuesta recibida
                    var response = JSON.parse(this.responseText);
                    console.log(JSON.stringify(response, null, 2));
                    // ... (código para actualizar los turnos en la página)
                } else {
                    console.log("Error al recibir la respuesta. Estado: " + this.status);
                }
            }
        };
        
        xhttp.open("GET", "update_turns.php", true); // Cambiar la URL a la que devuelve los datos actualizados
        xhttp.send();
    }

    // Llamar a la función cada 2 segundos
    setInterval(llamarUpdate, 20000);

 // Arreglo con los nuevos valores de los turnos A-Z y C-H
 const nuevosTurnosVeterinaria = ['Z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
    const nuevosTurnosComercial = ['H', 'A', 'B', 'C', 'D', 'E', 'F', 'G'];

    // Función para actualizar los turnos
    function actualizarTurnos() {
        // Obtener el contenedor de los turnos
        const veterinariaContainer = document.getElementById('veterinaria-container');
        const comercialContainer = document.getElementById('comercial-container');

        // Obtener el índice del turno actual
        const turnoActualVeterinaria = document.querySelector('#veterinaria-container .box2 > div:first-child p');
        const turnoActualComercial = document.querySelector('#comercial-container .box2 p');

        // Verificar si el turno actual es 'Z' o 'H'
        const indiceActualVeterinaria = nuevosTurnosVeterinaria.indexOf(turnoActualVeterinaria.textContent.trim());
        const indiceActualComercial = nuevosTurnosComercial.indexOf(turnoActualComercial.textContent.trim());

        // Calcular el próximo índice
        const proximoIndiceVeterinaria = (indiceActualVeterinaria + 1) % nuevosTurnosVeterinaria.length;
        const proximoIndiceComercial = (indiceActualComercial + 1) % nuevosTurnosComercial.length;

        // Actualizar los turnos
        turnoActualVeterinaria.textContent = nuevosTurnosVeterinaria[proximoIndiceVeterinaria];
        turnoActualComercial.textContent = nuevosTurnosComercial[proximoIndiceComercial];
    }

    // Llamar a la función cada 5 segundos
    setInterval(actualizarTurnos, 5000);


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