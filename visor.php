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

// Obtener los datos de la tabla turnos ordenados por estado y nombre_turno
$sql = "SELECT nombre_turno, numero_box, estado FROM turnos WHERE estado <> 'finalizado' ORDER BY estado, nombre_turno";
$result = $conn->query($sql);

$currentTurns = array();
$nextTurns = array();
$box1Turn = null;
$box2Turn = null;
$box3Turn = null;

while ($row = $result->fetch_assoc()) {
    if ($row['estado'] === 'actual') {
        $currentTurns[] = $row;
        if ($row['numero_box'] == 1) {
            $box1Turn = $row;
        } elseif ($row['numero_box'] == 2) {
            $box2Turn = $row;
        } elseif ($row['numero_box'] == 3) {
            $box3Turn = $row;
        }
    } else {
        $nextTurns[] = $row;
    }
}

// Verificar si no hay un turno actual en el Box 1
if ($box1Turn === null) {
    foreach ($nextTurns as $index => $turn) {
        if ($turn['estado'] === 'espera' && $turn['numero_box'] == 1) {
            $box1Turn = $turn;
            $box1Turn['estado'] = 'actual';
            unset($nextTurns[$index]);

            // Actualizar el turno en la base de datos
            $sql = "UPDATE turnos SET estado = 'actual', numero_box = 1 WHERE nombre_turno = '{$box1Turn['nombre_turno']}'";
            $conn->query($sql);

            break;
        }
    }
}

// Verificar si no hay un turno actual en el Box 2
if ($box2Turn === null) {
    foreach ($nextTurns as $index => $turn) {
        if ($turn['estado'] === 'espera' && $turn['numero_box'] == 2) {
            $box2Turn = $turn;
            $box2Turn['estado'] = 'actual';
            unset($nextTurns[$index]);

            // Actualizar el turno en la base de datos
            $sql = "UPDATE turnos SET estado = 'actual', numero_box = 2 WHERE nombre_turno = '{$box2Turn['nombre_turno']}'";
            $conn->query($sql);

            break;
        }
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
