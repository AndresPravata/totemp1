""" from escpos.printer import Usb
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
 """


import win32print
from escpos.printer import Usb, Network
from escpos.exceptions import USBNotFoundError

def listar_impresoras_usb():
    try:
        # Obtener la lista de impresoras instaladas en el sistema
        impresoras_instaladas = [printer[2] for printer in win32print.EnumPrinters(2)]

        # Filtrar y mostrar solo las impresoras USB
        print("Impresoras USB disponibles:")
        for impresora in impresoras_instaladas:
            if "USB" in impresora.upper():
                print(impresora)
    except Exception as e:
        print(f"Error al listar impresoras: {e}")

def imprimir_texto(impresora_seleccionada):
    try:
        # Conectar a la impresora seleccionada
        if impresora_seleccionada.upper().startswith("USB:"):
            # Obtener el nombre de la impresora USB
            usb_printer_name = impresora_seleccionada.split("USB:")[1].strip()
            p = Usb(0x0416, 0x5011, 0, 0x81, 0x03, profile=usb_printer_name)
        else:
            # Aquí podrías ajustar según tu impresora de red
            p = Network(impresora_seleccionada)

        # Enviar comandos ESC/POS para imprimir texto
        p.text("Hola, esta es una prueba de impresión en una impresora POS.\n")
        p.text("¡Gracias por utilizar nuestro servicio!\n")

        # Cortar el papel (esto puede variar según la impresora)
        p.cut()

        # Cerrar la conexión con la impresora
        p.close()
    except USBNotFoundError as e:
        print(f"Error al imprimir: {e}")
    except Exception as e:
        print(f"Error al imprimir: {e}")

if __name__ == "__main__":
    # Listar impresoras USB disponibles en Windows
    listar_impresoras_usb()

    # Solicitar al usuario que ingrese el nombre de la impresora
    impresora_seleccionada = input("Ingrese el nombre completo de la impresora que desea utilizar: ")

    # Imprimir texto en la impresora seleccionada
    imprimir_texto(impresora_seleccionada)
