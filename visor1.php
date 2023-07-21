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

// Lógica para separar los turnos actuales y siguientes por box
$turnos_actuales = array('comercial' => '', 'veterinaria' => array());
$turnos_siguientes = array();

while ($row = $result->fetch_assoc()) {
    $nombre_turno = $row["nombre_turno"];
    $numero_box = $row["numero_box"];
    $estado = $row["estado"];

    if ($numero_box === 4) {
        // Box de comercial
        if ($estado === 'actual') {
            $turnos_actuales['comercial'] = $nombre_turno;
        } else {
            $turnos_siguientes[] = $nombre_turno;
        }
    } elseif ($numero_box >= 1 && $numero_box <= 3) {
        // Box de veterinaria
        if ($estado === 'actual') {
            $turnos_actuales['veterinaria'][$numero_box] = $nombre_turno;
        } else {
            $turnos_siguientes[] = $nombre_turno;
        }
    }
}

// Retorna los datos en formato JSON para su uso en el visor
$turnos_data = array(
    "actuales" => $turnos_actuales,
    "siguientes" => $turnos_siguientes
);

echo json_encode($turnos_data);

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Visor de Turnos</title>
    <style>
        /* Agrega aquí tu CSS para personalizar la interfaz gráfica */
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
            <td>
                <div id="current-turn"></div>
            </td>
            <td>Box 1</td>
        </tr>
        <tr>
            <td id="box4-turn"></td>
            <td>Box 2</td>
        </tr>
        <tr>
            <td></td>
            <td>Box 3</td>
        </tr>
        <tr>
            <th colspan="2">Turnos siguientes</th>
        </tr>
        <tr>
            <td colspan="2">
                <div id="next-turns"></div>
            </td>
        </tr>
    </table>
    <script>
        // Agrega aquí tu JavaScript para actualizar el visor automáticamente cada cierto tiempo
        function updateVisor() {
    // Realiza una petición AJAX al archivo PHP para obtener los datos de los turnos
    // y actualiza el visor con los datos recibidos
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var data = JSON.parse(xhr.responseText);
                updateTurnosActuales(data.actuales);
                updateTurnosSiguientes(data.siguientes);
            }
        }
    };
    xhr.open("GET", "visor_turnos.php", true);
    xhr.send();
}

function updateTurnosActuales(turnosActuales) {
    var currentTurnComercial = document.getElementById("current-turn");
    currentTurnComercial.innerHTML = "<div class='current-turn'>" + turnosActuales.comercial + "</div>";

    var box1Turn = document.getElementById("box1-turn");
    box1Turn.textContent = turnosActuales.veterinaria[1] || "Sin turno";

    var box2Turn = document.getElementById("box2-turn");
    box2Turn.textContent = turnosActuales.veterinaria[2] || "Sin turno";

    var box3Turn = document.getElementById("box3-turn");
    box3Turn.textContent = turnosActuales.veterinaria[3] || "Sin turno";
}

function updateTurnosSiguientes(turnosSiguientes) {
    var nextTurns = document.getElementById("next-turns");
    nextTurns.innerHTML = "<h2>Turnos siguientes</h2>";

    for (var i = 0; i < turnosSiguientes.length; i++) {
        nextTurns.innerHTML += "<p>" + turnosSiguientes[i] + "</p>";
    }
}

// Actualizar el visor cada 5 segundos
setInterval(updateVisor, 5000);

    </script>
</body>
</html>
