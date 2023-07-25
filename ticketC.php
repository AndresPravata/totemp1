<?php
require __DIR__ . '/ticket/autoload.php'; //Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$nombre_impresora = "POS-58"; 
$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);

# Vamos a alinear al centro lo próximo que imprimamos
$printer->setJustification(Printer::JUSTIFY_CENTER);

/*
    Intentaremos cargar e imprimir
    el logo
*/
/*
    Ahora vamos a imprimir un encabezado
*/
$printer->setEmphasis(true); // Resalta el texto
$printer->setTextSize(1, 1); // Aumenta el tamaño del texto a 2 veces el tamaño normal
$printer->text("Veterinaria Luffi" . "\n");
$printer->setEmphasis(false); // Desactiva el resaltado del texto
$printer->setTextSize(1, 1); // Restablece el tamaño del texto a su valor predeterminado

$printer->text("Direccion: Cnel. Suarez 451" . "\n");
$printer->text("Tel: 0260 459-9286" . "\n");
$printer->text("\n");
# La fecha también
date_default_timezone_set("America/Argentina/Buenos_Aires");
$printer->text(date("Y-m-d H:i:s") . "\n");
$printer->text("-----------------------------" . "\n");
// Espacios vacíos debajo del número de turno
$printer->text("\n\n");
# Obtener el último turno impreso
$turno_actual = obtener_turno_actual();

# Incrementar el turno actual
$turno_siguiente = incrementar_turno($turno_actual);

$printer->text("Turno N°:\n");
$printer->setEmphasis(true); // Resalta el texto
$printer->setTextSize(4, 4); // Aumenta el tamaño del texto a 2 veces el tamaño normal
# Imprimir el turno siguiente
$printer->text("C" . $turno_siguiente . "\n");
$printer->setEmphasis(false); // Desactiva el resaltado del texto
$printer->setTextSize(1, 1); // Restablece el tamaño del texto a su valor predeterminado
# Actualizar el turno actual en el archivo
file_put_contents("turnoC.txt", $turno_siguiente);
// Espacios vacíos debajo del número de turno
$printer->text("\n\n\n\n\n\n\n\n\n\n");
/*
    Ahora vamos a imprimir los
    productos
*/


$printer->feed(3);
$printer->cut();
$printer->pulse();
$printer->close();

# Función para obtener el último turno impreso
function obtener_turno_actual() {
    $turno_actual = file_get_contents("turnoC.txt");
    if (!is_numeric($turno_actual)) {
        $turno_actual = 0;
    }
    return $turno_actual;
}

# Función para incrementar el turno actual
function incrementar_turno($turno_actual) {
    $turno_siguiente = $turno_actual + 1;
    return $turno_siguiente;
}
?>
