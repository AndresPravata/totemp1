from datetime import datetime
import sys
import win32print
import win32con
from PIL import Image

def imprimir_template_pos58(impresora_nombre, turno_actual):
    try:
        # Abrir la impresora
        hPrinter = win32print.OpenPrinter(impresora_nombre)

        # Iniciar el documento
        win32print.StartDocPrinter(hPrinter, 1, ('Documento de prueba', None, 'RAW'))

        # Iniciar la página
        win32print.StartPagePrinter(hPrinter)

        # Encabezado
        header_text = "\nVeterinaria Luffi\n"
        win32print.WritePrinter(hPrinter, header_text.encode('utf-8'))

        # Dirección y Teléfono
        address_text = "Direccion: Cnel. Suarez 451\nTel: 0260 459-9286\n\n"
        win32print.WritePrinter(hPrinter, address_text.encode('utf-8'))

        # Turno
        turno_string = "Turno:\n" + str(turno_actual) + "\n"
        win32print.WritePrinter(hPrinter, turno_string.encode('utf-8'))

        # Fecha
        date_string = "Fecha: " + datetime.now().strftime("%Y-%m-%d %H:%M:%S") + "\n"
        win32print.WritePrinter(hPrinter, date_string.encode('utf-8'))
        win32print.WritePrinter(hPrinter, "\n\n\n".encode('utf-8'))

        # Finalizar la página y el documento
        win32print.EndPagePrinter(hPrinter)
        win32print.EndDocPrinter(hPrinter)

        # Cerrar la impresora
        win32print.ClosePrinter(hPrinter)
    except Exception as e:
        print(f"Error al imprimir: {e}")

def listar_impresoras():
    try:
        # Obtener la lista de impresoras instaladas en el sistema
        impresoras_instaladas = [printer[2] for printer in win32print.EnumPrinters(2, None, 1)]

        # Filtrar e imprimir solo las impresoras que comienzan con "POS"
        print("Impresoras POS disponibles:")
        impresora_seleccionada = ""
        for impresora in impresoras_instaladas:
            if impresora.upper().startswith("POS"):
                impresora_seleccionada = impresora
                print(impresora)
                break
        return impresora_seleccionada
    except Exception as e:
        print(f"Error al listar impresoras: {e}")

if __name__ == "__main__":
    turno = sys.argv[1] if len(sys.argv) > 1 else "valor_predeterminado"

    imprimir_template_pos58(listar_impresoras(), turno)
