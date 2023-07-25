<?php
$numeroTurno = 0;

// Abre el archivo "turno.txt" en modo escritura
$archivo = fopen("turno.txt", "w");

// Escribe el número de turno en el archivo
fwrite($archivo, $numeroTurno);

// Cierra el archivo
fclose($archivo);

echo "El número de turno se ha asignado correctamente a 0.";
?>