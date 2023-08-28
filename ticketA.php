<?php
require __DIR__ . '/ticket/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$nombre_impresora = "Veterinaria"; 
$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);

$printer->setJustification(Printer::JUSTIFY_CENTER);

try {
    $logo = EscposImage::load("prueba.jpg", false);
    $printer->bitImage($logo);
} catch (Exception $e) {
    // No hacemos nada si hay error
}

$printer->setEmphasis(true);
$printer->setTextSize(1, 1);
$printer->text("\n" . "Veterinaria Dr.Luffi" . "\n");
$printer->setEmphasis(false);
$printer->setTextSize(1, 1);

$printer->text("Direccion: Cnel. Suarez 451" . "\n");
$printer->text("Tel: 0260 459-9286" . "\n");
$printer->text("\n");

date_default_timezone_set("America/Argentina/Buenos_Aires");
$printer->text(date("Y-m-d H:i:s") . "\n");
$printer->text("-----------------------------" . "\n");

$turno_actual = obtener_turno_actual();

$printer->text("Turno N°:\n");
$printer->setEmphasis(true);
$printer->setTextSize(5, 5);
$printer->text("A" . $turno_actual . "\n");
$printer->setEmphasis(false);
$printer->setTextSize(1, 1);

$turno_siguiente = incrementar_turno($turno_actual);
file_put_contents("turno.txt", $turno_siguiente);

$printer->text("\n\n\n\n\n\n\n\n\n\n");

$printer->feed(3);
$printer->cut();
$printer->pulse();
$printer->close();

function obtener_turno_actual() {
    $turno_actual = file_get_contents("turno.txt");
    if (!is_numeric($turno_actual)) {
        $turno_actual = 0;
    }
    return $turno_actual;
}

function incrementar_turno($turno_actual) {
    $turno_siguiente = $turno_actual + 1;
    return $turno_siguiente;
}
?>
