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
$sql = "SELECT nombre_turno, numero_box, estado FROM turnos WHERE estado <> 'finalizado' ORDER BY estado, nombre_turno";
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
function actualizarTurnos($conn)
{
    // Obtener el turno actual de Comercial (box 4)
    $sql_comercial_actual = "SELECT nombre_turno FROM turnos WHERE estado = 'actual' AND numero_box = 4";
    $result_comercial_actual = $conn->query($sql_comercial_actual);
    $turno_actual_comercial = $result_comercial_actual->fetch_assoc();

    if ($turno_actual_comercial) {
        $nombre_turno_actual = $turno_actual_comercial['nombre_turno'];
        
        // Cambiar el estado del turno actual de Comercial a 'finalizado'
        $sql_finalizar_turno_actual = "UPDATE turnos SET estado = 'finalizado' WHERE nombre_turno = '$nombre_turno_actual'";
        $conn->query($sql_finalizar_turno_actual);
    }

    // Buscar el siguiente turno en espera para Comercial (box 4)
    $sql_comercial_siguiente = "SELECT nombre_turno FROM turnos WHERE estado = 'espera' AND numero_box = 4 ORDER BY id ASC LIMIT 1";
    $result_comercial_siguiente = $conn->query($sql_comercial_siguiente);
    $turno_siguiente_comercial = $result_comercial_siguiente->fetch_assoc();

    if ($turno_siguiente_comercial) {
        $nombre_turno_siguiente = $turno_siguiente_comercial['nombre_turno'];

        // Cambiar el estado del siguiente turno en espera a 'actual'
        $sql_actualizar_turno_siguiente = "UPDATE turnos SET estado = 'actual' WHERE nombre_turno = '$nombre_turno_siguiente'";
        $conn->query($sql_actualizar_turno_siguiente);
    }
}
// Lógica para separar los turnos actuales y siguientes por box
$turnos_actuales = array('comercial' => '', 'veterinaria' => array());
$turnos_siguientes = array();

while ($row = $result->fetch_assoc()) {
    // Resto del código anterior...
}

// Llamamos a la función para actualizar los turnos
actualizarTurnos($conn);

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
                <div id="current-turn"></div>
            </td>
            <td>Box 1</td>
        </tr>
        <tr>
            <td id="box1-turn"><?php echo $box1Turn !== null ? $box1Turn['nombre_turno'] : ''; ?></td>
        </tr>
        <tr>
            <td>Box 2</td>
        </tr>
        <tr>
            <td id="box2-turn"><?php echo $box2Turn !== null ? $box2Turn['nombre_turno'] : ''; ?></td>
        </tr>
        <tr>
            <th colspan="2">Turnos siguientes</th>
        </tr>
        <tr>
            <td colspan="2">
                <div id="next-turns">
                    <?php foreach ($nextTurns as $turn): ?>
                        <div><?php echo $turn['nombre_turno']; ?></div>
                    <?php endforeach; ?>
                </div>
            </td>
        </tr>
    </table>
    <script>
        function updateTurns() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    var currentTurns = response.currentTurns;
                    var nextTurns = response.nextTurns;

                    // Actualizar los turnos actuales en el visor
                    var currentTurnHtml = '';
                    for (var i = 0; i < currentTurns.length; i++) {
                        var nombreTurno = currentTurns[i].nombre_turno;

                        if (nombreTurno.charAt(0) === "A") {
                            currentTurnHtml += '<div class="current-turn">' + nombreTurno + '</div>';
                        } else {
                            currentTurnHtml += '<div>' + nombreTurno + '</div>';
                        }
                    }
                    document.getElementById("current-turn").innerHTML = currentTurnHtml;

                    // Actualizar los turnos siguientes en el visor
                    var nextTurnsHtml = '';
                    for (var i = 0; i < nextTurns.length; i++) {
                        var nombreTurno = nextTurns[i].nombre_turno;
                        nextTurnsHtml += '<div>' + nombreTurno + '</div>';
                    }
                    document.getElementById("next-turns").innerHTML = nextTurnsHtml;

                    // Verificar si hay que mostrar o ocultar los botones de finalización
                    var finalizarButton = document.getElementById("finalizar-button");
                    if (currentTurns.length > 0) {
                        finalizarButton.style.display = "block";
                    } else {
                        finalizarButton.style.display = "none";
                    }
                }
            };
            xmlhttp.open("GET", "update_turns.php", true);
            xmlhttp.send();
        }

        function finalizarTurno() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("GET", "finalizar_turno.php", true);
            xmlhttp.send();
        }

        // Llama a la función updateTurns() cuando se carga la página
        window.onload = function() {
            updateTurns();
            setInterval(updateTurns, 5000); // Actualizar cada 5 segundos (ajustar según tus necesidades)
        };
    </script>
</body>
</html>
