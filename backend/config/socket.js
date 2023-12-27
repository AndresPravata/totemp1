import { obtenerActualTurno, obtenerCantidadBox, obtenerInformacionTurno, obtenerSiguienteTurno } from "../helpers/helpers.js";

let estadoVeterinario = "ausente";
let estadoVeterinario2 = "ausente";

export function configureSocketIO(io) {
  io.on("connection", (socket) => {

    socket.emit("estadoVeterinario", estadoVeterinario);
    socket.emit("estadoVeterinario2", estadoVeterinario2);

    socket.on("actualizarTurnos", async () => {
      console.log("Evento 'actualizarTurnos' recibido en el servidor");

      const informacionTurno = await obtenerInformacionTurno();
  
      io.emit("consultarTurnos", informacionTurno);
      console.log("Respuesta enviada a 'consultarTurnos'");
    });

    socket.on("actualizarBox", async ({ box }) => {
      console.log(box);
      const actual = await obtenerActualTurno(box);
      const siguiente = await obtenerSiguienteTurno(box);
      const boxNum = box[box.length - 1];
      console.log(boxNum);
      const cantidad = await obtenerCantidadBox(boxNum);
  
      io.emit("consultarBox", [actual, siguiente, cantidad, boxNum]);
      console.log("Respuesta enviada a 'consultarBox'");
    });

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
