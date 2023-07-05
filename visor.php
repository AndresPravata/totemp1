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

// Obtener los datos de la tabla turnos
$sql = "SELECT nombre_turno, numero_box FROM turnos ORDER BY nombre_turno";
$result = $conn->query($sql);

$turnosSelect = array();
while ($row = $result->fetch_assoc()) {
    $turnosSelect[] = $row;
}

$turnosString = json_encode($turnosSelect);

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
            <td id="box1-turn"></td>
        </tr>
        <tr>
            <td>Box 2</td>
        </tr>
        <tr>
            <td id="box2-turn"></td>
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
        var currentTurns = <?php echo $turnosString; ?>;
        var nextTurns = [];

        function setBoxOccupied(boxNumber) {
            // Aquí puedes implementar la lógica para marcar el box como ocupado
            // por ejemplo, cambiar el color de fondo del cuadro o mostrar un mensaje

            // Después de marcar el box como ocupado, puedes actualizar el visor llamando a la función updateVisor()
            updateVisor();
        }

        function setBoxAvailable(boxNumber) {
            // Aquí puedes implementar la lógica para marcar el box como disponible
            // por ejemplo, cambiar el color de fondo del cuadro o mostrar un mensaje

            // Después de marcar el box como disponible, puedes actualizar el visor llamando a la función updateVisor()
            updateVisor();
        }

        function updateVisor() {
            // Limpiar los turnos actuales y siguientes
            document.getElementById("current-turn").innerText = "";
            document.getElementById("box1-turn").innerText = "";
            document.getElementById("box2-turn").innerText = "";
            document.getElementById("next-turns").innerHTML = "";

            // Mostrar los turnos actuales
            for (var i = 0; i < currentTurns.length; i++) {
                var nombreTurno = currentTurns[i].nombre_turno;
                var numeroBox = currentTurns[i].numero_box;

                if (numeroBox == 1) {
                    document.getElementById("box1-turn").innerText = nombreTurno;
                } else if (numeroBox == 2) {
                    document.getElementById("box2-turn").innerText = nombreTurno;
                }

                if (nombreTurno.charAt(0) === "C") {
                    document.getElementById("current-turn").innerText = nombreTurno;
                }
            }

            // Mostrar los turnos siguientes
            for (var i = 0; i < nextTurns.length; i++) {
                var nombreTurno = nextTurns[i].nombre_turno;
                var turnoHTML = document.createElement("div");
                turnoHTML.innerText = nombreTurno;
                document.getElementById("next-turns").appendChild(turnoHTML);
            }
        }

        // Llama a la función updateVisor() cuando se carga la página
        window.onload = updateVisor;
    </script>
</body>
</html>
