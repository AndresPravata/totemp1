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
}

if (empty($turnos_actuales['veterinaria'][2])) {
    $turnos_actuales['veterinaria'][2] = '';
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
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Visor de Turnos</title>
    <style>
       
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: lightgray;
    }

    .current-turn {
        font-size: 20px;
        font-weight: bold;
    }

    #next-turns, #box3-turns {
        display: flex;
        flex-direction: column;
    }

    </style>
</head>
<body>
    <h1>Visor de Turnos</h1>

    <table>
        <tr>
            <th>Comercial</th>
            <th>Veterinaria</th>
        </tr>
        <tr>
            <th colspan="2">Turnos actuales</th>
        </tr>
        <tr>
            <td rowspan="4">
                <!-- Mostrar el turno actual de comercial sin negrita -->
                <div id="current-turn"><?php echo $turnos_actuales['comercial']; ?></div>
            </td>
            <td>Box 1</td>
        </tr>
        <tr>
            <td id="box1-turn"><?php echo $turnos_actuales['veterinaria'][1]; ?></td>
        </tr>
        <tr>
            <td>Box 2</td>
        </tr>
        <tr>
            <td id="box2-turn"><?php echo $turnos_actuales['veterinaria'][2]; ?></td>
        </tr>
        <tr>
            <th colspan="2">Turnos siguientes</th>
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
    setTimeout(actualizarPagina, 5000);
    </script>
</body>
</html>
