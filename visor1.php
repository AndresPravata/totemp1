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
        if (!empty($turnos_actuales['veterinaria'][1])) {
            $box_libre = 2;
        }

        // Cambiar el estado y el número de Box del primer turno de espera
        $sql_update = "UPDATE turnos SET estado = 'actual', numero_box = $box_libre WHERE nombre_turno = '{$primer_turno_espera['nombre_turno']}' AND estado = 'espera' AND numero_box = 3 LIMIT 1";
        $conn->query($sql_update);
        // Si se actualizó correctamente, eliminar el turno de espera de $turnos_siguientes
        unset($turnos_siguientes[key($turnos_siguientes)]);

        // Actualizar el array $turnos_actuales con el nuevo turno asignado
        $turnos_actuales['veterinaria'][$box_libre] = $primer_turno_espera['nombre_turno'];
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
<html>
<head>
<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    <title>Visor de Turnos</title>
    
    <style>
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
    font-size: 50px;
    font-weight: bold; /* Texto en negrita */
    display: flex;
    align-items: center; /* Centrar verticalmente */
    justify-content: center; /* Centrar horizontalmente */
}
#current-turn {
    vertical-align: middle;
}
/* Estilos para los textos en las celdas de Box 3 de comercial */
#box3-turns p {
    text-align: center;
    font-weight: bold; /* Texto en negrita */
    display: flex;
    align-items: center; /* Centrar verticalmente */
    justify-content: center; /* Centrar horizontalmente */
}

/* Estilo para los turnos siguientes */
#next-turns p {
    font-size: 30px; /* Tamaño de fuente personalizado */
    font-weight: bold; /* Texto en negrita */
}
/* Estilo para los turnos siguientes */
#next-turns p {
    font-size: 30px; /* Tamaño de fuente personalizado */
    font-weight: bold; /* Texto en negrita */
}

/* Estilo para los turnos en Box 1 y Box 2 de veterinaria */
#box1-turn, #box2-turn {
    font-weight: bold; /* Texto en negrita */
    font-size: 40px;

}

    </style>
</head>
<body>
    <h1 style="text-align: center";>Visor de Turnos</h1>

    <table style="text-align: center";>
        <tr>
            <th style="text-align: center";>Comercial</th>
            <th style="text-align: center";>Veterinaria</th>
        </tr>
        <tr>
            <th style="text-align: center"; colspan="2">Turnos actuales</th>
        </tr>
        <tr>
        <td rowspan="4" style="vertical-align: middle; text-align: center;">
    <!-- Mostrar el turno actual de comercial en el centro -->
    <div id="current-turn"><strong><?php echo $turnos_actuales['comercial']; ?></strong></div>
</td>

            <td style="text-align: center";>Box 1</td>
        </tr>
        <tr>
            <td id="box1-turn"><?php echo $turnos_actuales['veterinaria'][1]; ?></td>
        </tr>
        <tr>
            <td style="text-align: center";>Box 2</td>
        </tr>
        <tr>
            <td id="box2-turn"><?php echo $turnos_actuales['veterinaria'][2]; ?></td>
        </tr>
        <tr>
            <th style="text-align: center"; colspan="2">Turnos siguientes</th>
        </tr>
        <tr>
            <td colspan="2">
                <div id="next-turns">
                    <?php
                    // Mostrar los turnos siguientes sin los paréntesis de box
                    foreach ($turnos_siguientes as $turno) {
                        echo "<p>{$turno['nombre_turno']}</p>";
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="box3-turns">
                    <?php
                    // Mostrar los turnos del box 3 sin los paréntesis de box
                    foreach ($turnos_actuales['veterinaria'] as $numero_box => $nombre_turno) {
                        if ($numero_box > 2) {
                            echo "<p>{$nombre_turno}</p>";
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
    </table>
    <script>
        // Aquí vendría el código JavaScript que actualiza los datos del visor y los muestra en pantalla
        // Función para actualizar la página cada 5 segundos
    function actualizarPagina() {
        location.reload();
    }

    // Actualizar la página cada 5 segundos
    setTimeout(actualizarPagina, 1000);
    </script>
</body>
</html>
