let estadoVeterinario = "ausente";
let estadoVeterinario2 = "ausente";

export function configureSocketIO(io) {
  io.on("connection", (socket) => {
    // Enviar el estado actual de los veterinarios a cualquier cliente nuevo
    socket.emit("estadoVeterinario", estadoVeterinario);
    socket.emit("estadoVeterinario2", estadoVeterinario2);

    socket.on("veterinarioPresente", () => {
      estadoVeterinario = "presente";
      io.emit("estadoVeterinario", estadoVeterinario);
    });

    socket.on("veterinarioAusente", () => {
      estadoVeterinario = "ausente";
      io.emit("estadoVeterinario", estadoVeterinario);
    });

    socket.on("veterinario2Presente", () => {
      estadoVeterinario2 = "presente";
      io.emit("estadoVeterinario2", estadoVeterinario2);
    });

    socket.on("veterinario2Ausente", () => {
      estadoVeterinario2 = "ausente";
      io.emit("estadoVeterinario2", estadoVeterinario2);
    });
  });
}
