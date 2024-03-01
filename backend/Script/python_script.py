from escpos import printer

port_name = "USB002"  # Replace with the correct port name
idProduct = "811e"  # Replace with your printer's idProduct


p = printer.Usb(port_name, idProduct)

p.text("¡Hola, esto es un ejemplo de impresión en la impresora térmica!\n")
p.text("Este es otro texto de ejemplo.\n")
p.cut()

p.close()
