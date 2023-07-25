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
$veterinarioId = $_POST['veterinario_id'];
$nombreTurno = "A" . intval(file_get_contents("turno.txt")); // Obtener el número de turno de turno.txt sin incrementar
$fechaHoraInicio = $_POST['fecha_hora_inicio'];
$numeroBox = $_POST['numero_box'];
$estado = "espera"; // Establecer el estado como "espera"
$sql = "INSERT INTO Turnos (veterinario_id, fecha_hora_inicio, nombre_turno, numero_box, estado) VALUES ($veterinarioId, '$fechaHoraInicio', '$nombreTurno', $numeroBox, '$estado')";

if ($conn->query($sql) === TRUE) {
    // Incrementar el número del turno en el archivo "turno.txt" después de guardar el turno en la base de datos
    file_put_contents("turno.txt", intval(file_get_contents("turno.txt")) + 0);
    echo 1; // Éxito
} else {
    echo 0; // Error
}

$conn->close();
?>