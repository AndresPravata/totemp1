<?php
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "turnos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el estado actual del box 2
$sql = "SELECT estado FROM boxes WHERE N_boxes = 2";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$estadoBox2 = $row['estado'];

// Lógica para cambiar el estado del box 2
if (isset($_GET['estado'])) {
    $nuevoEstado = $_GET['estado'];
    $sql = "UPDATE boxes SET estado = '$nuevoEstado' WHERE N_boxes = 2";
    $conn->query($sql);
    $estadoBox2 = $nuevoEstado;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Box 2</title>
</head>
<body>
    <h1>Box 2</h1>

    <!-- Estado actual del box 2 -->
    <p>Estado: <?php echo $estadoBox2; ?></p>

    <button onclick="setBoxAvailable(2)">Desocupar</button>
    <button onclick="setBoxOccupied(2)">Ocupar</button>

    <script>
        function setBoxAvailable(boxNumber) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Actualizar la página después de marcar el box como disponible
                    location.reload();
                }
            };
            xhttp.open("GET", "?estado=disponible", true);
            xhttp.send();
        }

        function setBoxOccupied(boxNumber) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Actualizar la página después de marcar el box como ocupado
                    location.reload();
                }
            };
            xhttp.open("GET", "?estado=ocupado", true);
            xhttp.send();
        }
    </script>
</body>
</html>
