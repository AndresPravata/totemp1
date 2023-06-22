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
echo $turnosString;

$conn->close();

?>
