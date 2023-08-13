<?php
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "turnos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Código para obtener y actualizar los turnos, similar a tu código original

// Generar el contenido actualizado para las secciones de veterinaria y comercial
ob_start(); // Captura el resultado de la salida
?>
<!-- Sección de Veterinaria -->
<div id="veterinaria-container">
    <h2><strong>Veterinaria</strong></h2>
    <div class="box2">
        <div>
            <p class="box-turn"><?php echo $turnos_actuales['veterinaria'][1]; ?> BOX 1</p>
        </div>
        <div class="divider"></div> <!-- Línea vertical -->
        <div>
            <p class="box-turn"><?php echo $turnos_actuales['veterinaria'][2]; ?> BOX 2</p>
        </div>
    </div>
</div>

<!-- Sección de Comercial -->
<div id="comercial-container">
    <h2><strong>Comercial</strong></h2>
    <p class="box-turn"><?php echo $turnos_actuales['comercial']; ?></p>
</div>
<?php
$output = ob_get_clean(); // Obtén el contenido capturado y limpia el buffer
echo $output; // Imprime el contenido actualizado
$conn->close();
?>
