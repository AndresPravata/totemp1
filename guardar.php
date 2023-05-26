<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "turnos";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener los datos del formulario
$nombreTurno = $_POST['nombre_turno'];
$fechaHoraInicio = $_POST['fecha_hora_inicio'];
$fechaHoraFin = $_POST['fecha_hora_fin'];
$veterinarioId = $_POST['veterinario_id'];
$numeroBox = $_POST['numero_box'];

// Preparar la consulta SQL
$sql = "INSERT INTO turnos (nombre_turno, fecha_hora_inicio, fecha_hora_fin, veterinario_id, numero_box)
        VALUES ('$nombreTurno', '$fechaHoraInicio', '$fechaHoraFin', $veterinarioId, $numeroBox)";

// Ejecutar la consulta y verificar el resultado
if ($conn->query($sql) === TRUE) {
    echo "Turno creado correctamente";
} else {
    echo "Error al crear el turno: " . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>
