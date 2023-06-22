<?php
// Aquí debes realizar la lógica para marcar el box como ocupado en la base de datos
// Recuerda recibir el número del box mediante la variable $_GET['box'] y actualizar el estado correspondiente en la base de datos

// Por ejemplo:
$boxNumber = $_GET['box'];

// Realizar la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "turnos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Actualizar el estado del box a ocupado en la base de datos
$sql = "UPDATE boxes SET estado = 'ocupado' WHERE numero = $boxNumber";
$result = $conn->query($sql);

$conn->close();

// Devolver una respuesta (puede ser un mensaje de éxito o cualquier otro dato necesario)
echo "Box $boxNumber marcado como ocupado";
?>
