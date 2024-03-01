import express from "express";
import http from "http";
import { Server as SocketServer } from "socket.io";
import dotenv from "dotenv";
import { configureSocketIO } from "./config/socket.js";
import BoxRouter from "./routes/boxes.js";
import TurnoRouter from "./routes/turnos.js";
import VeterinarioRouter from "./routes/veterinarios.js";
import TotemRouter from "./routes/totems.js";
import swaggerUi from "swagger-ui-express";
import swaggerJSDoc from "swagger-jsdoc";
import cors from "cors";

dotenv.config();

const app = express();
const server = http.createServer(app);
const io = new SocketServer(server, {
  cors: {
    origin: "*",
  },
});

app.use(express.json());

configureSocketIO(io);

// Habilitar CORS para todas las rutas
app.use(cors());

// Rutas
app.use("/boxes", BoxRouter);
app.use("/turnos", TurnoRouter);
app.use("/veterinarios", VeterinarioRouter);
app.use("/totems", TotemRouter);

// Configuración de Swagger
const swaggerOptions = {
  definition: {
    openapi: "3.0.0",
    info: {
      title: "API Documentation",
      version: "1.0.0",
      description: "Documentation for your API",
    },
    servers: [
      {
        url: `http://localhost:${process.env.PORT_LISTEN || 5000}`,
      },
    ],
  },
  apis: ["./routes/*.js"], // Rutas a los archivos que contienen las definiciones de rutas
};

const swaggerSpec = swaggerJSDoc(swaggerOptions);

// Ruta para la documentación Swagger UI
app.use("/api-docs", swaggerUi.serve, swaggerUi.setup(swaggerSpec));

const PORT = process.env.PORT_LISTEN || 5000;
server.listen(PORT, () => {
  console.log(`Server listening on port ${PORT}`);
});
