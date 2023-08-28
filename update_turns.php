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
$sql = "SELECT nombre_turno, numero_box, estado FROM turnos WHERE estado != 'finalizado' and date(fecha_hora_inicio) = STR_TO_DATE('$fechaActual' , '%d/%m/%Y')  ORDER BY estado, nombre_turno";
$result = $conn->query($sql);

$turnos = array();

while ($row = $result->fetch_assoc()) {
    $turnos[] = $row;
}

$conn->close();

// Devolver los datos como JSON
header('Content-Type: application/json');
echo json_encode($turnos);
?>
