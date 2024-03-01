<?php
require __DIR__ . '/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;


$turno = $_SERVER["argv"][1];
$turno = substr($turno, -4) === "BOX4" ? substr($turno, 0, -4) . "VENTAS" : $turno;
$nombre_impresora = "80mm Series Printer";
$connector = new WindowsPrintConnector($nombre_impresora);
$printer = new Printer($connector);

# Vamos a alinear al centro lo próximo que imprimamos
$printer->setJustification(Printer::JUSTIFY_CENTER);

# Importamos la imagen
$logo = EscposImage::load("./Script/img/300.png");

// Imprime la imagen con la posición y tamaño ajustados
$printer->bitImage($logo);

$printer->setEmphasis(true); // Resalta el texto
$printer->setTextSize(2, 2); // Aumenta el tamaño del texto a 2 veces el tamaño normal
$printer->text("\n" . "Veterinaria Dr. Luffi" . "\n");
$printer->setEmphasis(false); // Desactiva el resaltado del texto
$printer->setTextSize(1, 1); // Restablece el tamaño del texto a su valor predeterminado
$printer->text("\n");
$printer->text("Gracias por confiar en Luffi" . "\n");
#$printer->text("Direccion: Cnel. Suarez 451" . "\n");
#$printer->text("Tel: 0260 459-9286" . "\n");
$printer->text("\n");
# La fecha también
date_default_timezone_set("America/Argentina/Buenos_Aires");
$printer->text(date("Y-m-d H:i:s") . "\n");
$printer->text("-----------------------------" . "\n");

#$printer->text("Turno N°:\n");
$printer->setEmphasis(true); // Resalta el texto
$printer->setTextSize(4, 4); // Aumenta el tamaño del texto a 2 veces el tamaño normal
# Imprimir el turno siguiente
$printer->text($turno . "\n");
$printer->setEmphasis(false); // Desactiva el resaltado del texto
$printer->setTextSize(1, 1); // Restablece el tamaño del texto a su valor predeterminado

$printer->feed(3);
$printer->cut();
$printer->pulse();
$printer->close();
?>