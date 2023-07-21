<?php
$numeroTurno = 0; // Número de turno a establecer (0 en este caso)


$archivo = "turnoC.txt"; // Ruta al archivo de texto
// Abrir el archivo en modo escritura
$file = fopen($archivo, "w");

if ($file) {
    // Escribir el número de turno en el archivo
    fwrite($file, $numeroTurno);

    // Cerrar el archivo
    fclose($file);

    echo "El número de turno se ha reseteado a 0 correctamente.";
} else {
    echo "No se pudo abrir el archivo para escribir.";
}
?>
