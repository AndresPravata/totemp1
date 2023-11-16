import express from "express";
import http from "http";
import { Server as SocketServer } from "socket.io";

const app = express();
const server = http.createServer(app);
const io = new SocketServer(server, {
  cors: {
    origin: "http://localhost:5173",
  },
});

let estadoVeterinario = "ausente";
let estadoVeterinario2 = "ausente";

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

server.listen(5000);
console.log("Server on port", 5000);

app.get("/", (req, res) => {
  return res.json("Jackson ");
});
