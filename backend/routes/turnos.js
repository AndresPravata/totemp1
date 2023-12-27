import express from "express";
import { Turno } from "../models/sequelize.js";
import { spawn } from "child_process";
import {
  obtenerActualTurno,
  obtenerCantidadBox,
  obtenerInformacionTurno,
  obtenerSiguienteTurno,
} from "../helpers/helpers.js";

const router = express.Router();

// Obtener todos los turnos
router.get("/", async (req, res) => {
  try {
    const turnos = await Turno.findAll();
    res.json(turnos);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener los turnos" });
  }
});

router.get("/turnosBox/:filter", async (req, res) => {
  try {
    const filter = req.params.filter;

    const siguiente = await obtenerSiguienteTurno(filter);

    const actual = await obtenerActualTurno(filter);

    res.json([actual, siguiente]);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener los turnos" });
  }
});

router.get("/turnosVisor", async (req, res) => {
  try {
    const informacionTurno = await obtenerInformacionTurno();

    res.json(informacionTurno);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener los turnos" });
  }
});

router.get("/cantidadTurnos/:num", async (req, res) => {
  try {
    const num = req.params.num;

    const count = await obtenerCantidadBox(num);

    res.json(count);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener los turnos" });
  }
});

// Crear un nuevo turno
router.post("/", async (req, res) => {
  try {
    const nuevoTurno = await Turno.create({
      nombre_turno: req.body.nombre_turno,
      fecha_hora_inicio: req.body.fecha_hora_inicio,
      fecha_hora_fin: req.body.fecha_hora_fin,
      estado: req.body.estado,
      numero_box: req.body.numero_box,
      veterinario_id: req.body.veterinario_id,
    });
    /*
    // Define los argumentos que deseas pasar al script de Python
    const turno = req.body.nombre_turno;
    
    // Comando para ejecutar el script de Python con argumentos
    const phpScriptPath = "./Script/php_script.php";
    const phpProcess = spawn("php", [phpScriptPath, turno]);
    
    let resultadoPhp = "";
    let errorPhp = "";
    
    phpProcess.stdout.on("data", (data) => {
      resultadoPhp = data.toString();
      console.log(`Resultado del script: ${resultadoPhp}`);
    });
    
    phpProcess.stderr.on("data", (data) => {
      errorPhp = data.toString();
      console.error(`Error en la salida estándar de error: ${errorPhp}`);
    });
    
    phpProcess.on("close", (code) => {
      console.log(`Proceso de Php cerrado con código de salida ${code}`);
    
      if (errorPhp) {
        res.status(500).json({ error: errorPhp });
      } else {
        res.status(200).json({ resultado: resultadoPhp, turno });
      }
    });
    */
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al crear el turno" });
  }
});

// Obtener un turno por ID
router.get("/:id", async (req, res) => {
  try {
    const turno = await Turno.findByPk(req.params.id);
    res.json(turno);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener el turno" });
  }
});

// Actualizar un turno por ID
router.put("/:id", async (req, res) => {
  try {
    const turno = await Turno.findByPk(req.params.id);
    if (turno) {
      await turno.update({
        nombre_turno: req.body.nombre_turno,
        fecha_hora_inicio: req.body.fecha_hora_inicio,
        fecha_hora_fin: req.body.fecha_hora_fin,
        estado: req.body.estado,
      });
      res.json(turno);
    } else {
      res.status(404).json({ error: "Turno no encontrado" });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al actualizar el turno" });
  }
});

// Eliminar un turno por ID
router.delete("/:id", async (req, res) => {
  try {
    const turno = await Turno.findByPk(req.params.id);
    if (turno) {
      await turno.destroy();
      res.json({ mensaje: "Turno eliminado exitosamente" });
    } else {
      res.status(404).json({ error: "Turno no encontrado" });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al eliminar el turno" });
  }
});

export default router;
