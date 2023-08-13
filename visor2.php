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

// Obtener los datos de la tabla turnos ordenados por estado y nombre_turno, excluyendo los turnos con estado 'finalizado'
$sql = "SELECT nombre_turno, numero_box, estado FROM turnos WHERE estado != 'finalizado' ORDER BY estado, nombre_turno";
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

// Agregar los turnos comerciales con estado 'espera' en la sección de 'Turnos siguientes'
$sql_espera_comercial = "SELECT nombre_turno FROM turnos WHERE estado = 'espera' AND numero_box = 4 ORDER BY nombre_turno";
$result_espera_comercial = $conn->query($sql_espera_comercial);

while ($row_espera_comercial = $result_espera_comercial->fetch_assoc()) {
    $nombre_turno_comercial_espera = $row_espera_comercial["nombre_turno"];
    $turno_existente = false;

    // Verificar si el turno ya existe en $turnos_siguientes
    foreach ($turnos_siguientes as $turno) {
        if ($turno['nombre_turno'] == $nombre_turno_comercial_espera && $turno['numero_box'] == 4) {
            $turno_existente = true;
            break;
        }
    }

    if (!$turno_existente) {
        $turnos_siguientes[] = array("nombre_turno" => $nombre_turno_comercial_espera, "numero_box" => 4);
    }
}

// Ordenar los turnos en espera por orden de llegada
usort($turnos_siguientes, function ($a, $b) {
    return strcmp($a['nombre_turno'], $b['nombre_turno']);
});

// Función para buscar y asignar un turno en 'turnos actuales' si está vacío
function asignarTurnoActual(&$turnos_actuales, &$turnos_siguientes, $numero_box, $conn) {
    $turno_asignado = false;
    foreach ($turnos_siguientes as $index => $turno) {
        if ($turno['numero_box'] == $numero_box) {
            // Verificar si los boxes 1 y 2 están ocupados
            $box1_ocupado = !empty($turnos_actuales['veterinaria'][1]);
            $box2_ocupado = !empty($turnos_actuales['veterinaria'][2]);

            // Asignar el turno a 'espera' si ambos boxes 1 y 2 están ocupados
            if ($numero_box == 3 && $box1_ocupado && $box2_ocupado) {
                $conn->query("UPDATE turnos SET estado = 'espera' WHERE nombre_turno = '{$turno['nombre_turno']}' AND estado = 'actual' AND numero_box = 3 LIMIT 1");
                // Cambiar el estado del turno en 'turnos_siguientes' a 'espera'
                $turnos_siguientes[$index]['estado'] = 'espera';
                // No asignar el turno a 'turnos_actuales' en este caso
            } else {
                // Actualizar el estado del turno en la base de datos de 'espera' a 'actual'
                $nombre_turno = $turno['nombre_turno'];
                $sql_update_estado = "UPDATE turnos SET estado = 'actual' WHERE nombre_turno = '$nombre_turno' AND estado = 'espera' AND numero_box = $numero_box LIMIT 1";
                $conn->query($sql_update_estado);

                // Cambiar el estado del turno en 'turnos_siguientes' a 'actual'
                $turnos_siguientes[$index]['estado'] = 'actual';

                // Asignar el turno a 'turnos_actuales'
                $turnos_actuales['veterinaria'][$numero_box] = $turno['nombre_turno'];
            }

            // Eliminar el turno de 'turnos_siguientes'
            unset($turnos_siguientes[$index]);

            $turno_asignado = true;
            break;
        }
    }

    return $turno_asignado;
}

// Verificar si hay un turno actual para comercial y si no lo hay, se establecerá como vacío
if (empty($turnos_actuales['comercial'])) {
    $turnos_actuales['comercial'] = '';
}

// Verificar si hay turnos de espera para comercial (Box 4) y si no los hay, se establecerá el array de turnos_siguientes como vacío
if (empty($turnos_siguientes)) {
    $turnos_siguientes = array();
}

// Verificar si hay turnos en Box 1 y Box 2 de veterinaria y si no los hay, se establecerán como vacíos
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

// Verificar si hay un turno actual para comercial y si no lo hay, se establecerá como vacío
if (empty($turnos_actuales['comercial'])) {
    $turnos_actuales['comercial'] = '';
}

// Verificar si hay turnos de espera para comercial (Box 4) y si no los hay, se establecerá el array de turnos_siguientes como vacío
if (empty($turnos_siguientes)) {
    $turnos_siguientes = array();
} else {
    // Obtener el primer turno de espera de veterinaria
    $primer_turno_espera = reset($turnos_siguientes);

    // Verificar si el primer turno de espera tiene asignado el Box 3
if ($primer_turno_espera['numero_box'] == 3) {
    // Buscar un Box libre (Box 1 o Box 2) para asignar el turno
    $box_libre = 1;
    if (!empty($turnos_actuales['veterinaria'][1]) && empty($turnos_actuales['veterinaria'][2])) {
        $box_libre = 2;
    } elseif (!empty($turnos_actuales['veterinaria'][2]) && empty($turnos_actuales['veterinaria'][1])) {
        $box_libre = 1;
    } elseif (empty($turnos_actuales['veterinaria'][1]) && empty($turnos_actuales['veterinaria'][2])) {
        // Si ambos Box 1 y Box 2 están desocupados, simplemente mantenerlo en el Box 3
        $box_libre = 3;
    } else {
        // Si ambos Box 1 y Box 2 están ocupados, mantenerlo en el Box 3 en estado de espera
        $box_libre = -1;
    }

    if ($box_libre != -1) {
        // Cambiar el estado y el número de Box del primer turno de espera
        $sql_update = "UPDATE turnos SET estado = 'actual', numero_box = $box_libre WHERE nombre_turno = '{$primer_turno_espera['nombre_turno']}' AND estado = 'espera' AND numero_box = 3 LIMIT 1";
        $conn->query($sql_update);
        // Si se actualizó correctamente, eliminar el turno de espera de $turnos_siguientes
        unset($turnos_siguientes[key($turnos_siguientes)]);

        // Actualizar el array $turnos_actuales con el nuevo turno asignado
        $turnos_actuales['veterinaria'][$box_libre] = $primer_turno_espera['nombre_turno'];
    }
}

    // Buscar y asignar turnos de espera a sus respectivos box en 'turnos actuales' si están vacíos
    if (empty($turnos_actuales['veterinaria'][1])) {
        asignarTurnoActual($turnos_actuales, $turnos_siguientes, 1, $conn);
    }
    if (empty($turnos_actuales['veterinaria'][2])) {
        asignarTurnoActual($turnos_actuales, $turnos_siguientes, 2, $conn);
    }
}
// Verificar si hay un turno actual para comercial y si no lo hay, se establecerá como vacío
if (empty($turnos_actuales['comercial'])) {
    $turnos_actuales['comercial'] = '';

    // Si no hay turno actual en Box 4 (comercial), intentar asignar uno desde 'turnos_siguientes'
    asignarTurnoActual($turnos_actuales, $turnos_siguientes, 4, $conn);
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider y Visor de Turnos</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/noscript.css">
    <style>
        /* Estilos del slider */
        * {
    margin: 0;
    padding: 0;
    font-family: lato;
}

h2 {
    text-align: center;
    font-size: 2rem;
    padding-top: 50px;
}

.slider-frame {
	width: 1280px;
    height: auto;
	margin:50px auto 0;
	overflow: hidden;
}

.slider-frame ul {
	display: flex;
	padding: 0;
	width: 400%;
	
	animation: slide 20s infinite alternate ease-in-out;
}

.slider-frame li {
	width: 100%;
	list-style: none;
}

.slider-frame img {
	width: 100%;
}

@keyframes slide {
	0% {margin-left: 0;}
	20% {margin-left: 0;}
	
	25% {margin-left: -100%;}
	45% {margin-left: -100%;}
	
	50% {margin-left: -200%;}
	70% {margin-left: -200%;}
	
	75% {margin-left: -300%;}
	100% {margin-left: -300%;}
}
        /* Estilos para el visor de turnos */
        /* Estilos para la tabla */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, tr, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        /* Estilo específico para el turno actual de comercial */
        #current-turn {
            font-size: 80px;
            font-weight: bold; /* Texto en negrita */
        }

        /* Estilo para los turnos en Box 1 y Box 2 de veterinaria */
        #box1-turn, #box2-turn {
            font-weight: bold; /* Texto en negrita */
            font-size: 80px; /* Tamaño de fuente aumentado */
        }

        /* Nuevos estilos para la disposición de los turnos en la parte inferior de la pantalla */
        #turnos-container {
            position: fixed;
            bottom: 10px; /* Ajusta el margen inferior para controlar la distancia del borde inferior */
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            text-align: center; /* Centrar el contenido horizontalmente */
        }

        #comercial-container,
        #veterinaria-container {
            width: 45%;
            border: 1px solid black;
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
            width: 2px;
            height: 100%;
            background-color: black;
            margin: 0 auto; /* Centrar horizontalmente */
        }
        .box-turn {
            font-weight: bold;
            font-size: 80px; /* Tamaño de fuente reducido */
            margin: 5px 0; /* Margen superior e inferior para los turnos */
        }

        /* Nuevos estilos para el texto Comercial y Veterinaria */
        #comercial-container h2 strong,
        #veterinaria-container h2 strong {
            font-size: 24px;
            display: block;
        }

        /* Nuevos estilos para el Box 2 de Veterinaria */
        #veterinaria-container .box2 {
            display: flex; /* Mostrar los elementos internos en línea */
            justify-content: space-between; /* Espaciado uniforme entre los elementos internos */
            margin-top: 40px; /* Espacio entre Box 1 y Box 2 */
            margin-bottom: 40px; /* Espacio inferior para separar de los turnos */
        }

        /* Margen a la izquierda del primer turno en Veterinaria */
        #veterinaria-container .box2 > div:first-child {
            margin-right: 10px; /* Ajusta este valor según sea necesario */
        }

        /* Margen a la derecha del segundo turno en Veterinaria */
        #veterinaria-container .box2 > div:last-child {
            margin-left: 10px; /* Ajusta este valor según sea necesario */
        }
    </style>
</head>
<body>
    <!-- Sección del Slider -->
    <h2>Slider automático con HTML & CSS</h2>
    <div class="slider-frame">
        <ul>
            <li><img src="slider1.jpg" alt=""></li>
            <li><img src="slider2.jpg" alt=""></li>
            <li><img src="slider3.jpg" alt=""></li>
            <li><img src="slider4.jpg" alt=""></li>
        </ul>
    </div>

    <!-- Sección del Visor de Turnos -->
    <div id="turnos-container">
          
    <!-- Nueva sección para mostrar los turnos en la parte inferior de la pantalla -->
    <div id="turnos-container">
        <!-- Sección de Veterinaria -->
        <div id="veterinaria-container">
            <h2 ><strong>Veterinaria</strong></h2>
            <div class="box2">
                <div>
                    <p class="box-turn"><?php echo $turnos_actuales['veterinaria'][1]; ?> BOX 1</p>
                </div>
                <div class="divider"></div> <!-- Línea vertical -->
                <div>
                    <p class="box-turn"><?php echo $turnos_actuales['veterinaria'][2]; ?> BOX 2</p>
                </div>
            </div>
        </div>

        <!-- Sección de Comercial -->
        <div id="comercial-container">
            <h2><strong>Comercial</strong></h2>
            <p class="box-turn"><?php echo $turnos_actuales['comercial']; ?></p>
        </div>
    </div>
    </div>

    <!-- JavaScript para actualizar la página -->
    <script>
   // Obtén la referencia al elemento ul del slider
   const sliderUl = document.querySelector('.slider-frame ul');

// Obtén la posición actual del slider antes de la actualización
const currentSliderPosition = Math.abs(parseInt(sliderUl.style.marginLeft) / 100);

// Función para actualizar la página cada 5 segundos
function actualizarPagina() {
    // Guarda la posición actual del slider en el Local Storage
    localStorage.setItem('sliderPosition', currentSliderPosition);
    location.reload();
}

// Configura la posición del slider al valor guardado en el Local Storage
const savedSliderPosition = localStorage.getItem('sliderPosition');
if (savedSliderPosition) {
    sliderUl.style.marginLeft = `-${savedSliderPosition * 100}%`;
}

// Actualizar la página cada 5 segundos
setTimeout(actualizarPagina, 5000);
</body>
</html>
