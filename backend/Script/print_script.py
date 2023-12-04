from escpos.printer import Usb
import sys

def imprimir_texto(vendor, product, turno):
    # Conectarse a la impresora POS a través de USB
    p = Usb(vendor, product, 0)

    # Enviar comandos ESC/POS para imprimir texto
    p.text(f"Este es el turno: {turno}\n")

    # Cortar el papel (esto puede variar según la impresora)
    p.cut()

    # Cerrar la conexión con la impresora
    p.close()

if __name__ == "__main__":
    # Obtener argumentos de la línea de comandos
    if len(sys.argv) != 4:
        print("Por favor, proporciona tres argumentos.")
        sys.exit(1)

    vendor = sys.argv[1]
    product = sys.argv[2]
    turno = sys.argv[3]

    # Llamar a la función con los argumentos proporcionados
    imprimir_texto(vendor, product, turno)
