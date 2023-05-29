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
$nombreTurno = "C" . (intval(file_get_contents("turnoC.txt")) + 1); // Obtener el número de turno de turnoC.txt y sumarle 1
$fechaHoraInicio = date('Y-m-d H:i:s'); // Obtener la fecha y hora actual
$numeroBox = 3; // Asignar el número de box 3

$sql = "INSERT INTO Turnos (fecha_hora_inicio, nombre_turno, numero_box) VALUES ('$fechaHoraInicio', '$nombreTurno', $numeroBox)";

if ($conn->query($sql) === TRUE) {
    echo 1; // Éxito
} else {
    echo 0; // Error
}

$conn->close();
?>

