<?php
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "turnos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los nuevos datos de la tabla turnos ordenados por estado y nombre_turno, excluyendo los turnos finalizados
$sql = "SELECT nombre_turno, numero_box, estado FROM turnos WHERE estado <> 'finalizado' ORDER BY estado, nombre_turno";
$result = $conn->query($sql);

$currentTurns = array();
$nextTurns = array();
while ($row = $result->fetch_assoc()) {
    if ($row['estado'] === 'actual') {
        $currentTurns[] = $row;
    } else {
        $nextTurns[] = $row;
    }
}

$conn->close();

$response = array(
    'currentTurns' => $currentTurns,
    'nextTurns' => $nextTurns
);

echo json_encode($response);

?>
