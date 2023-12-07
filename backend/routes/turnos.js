import express from "express";
import { Turno } from "../models/sequelize.js";
import { Op } from "sequelize";
import { spawn } from "child_process";

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
    const siguiente = await Turno.findOne({
      where: {
        estado: "Espera", // LOS 3 ESTADOS POSIBLES SON: FINALIZADO, ACTUAL, ESPERA
        nombre_turno: {
          [Op.like]: `%${req.params.filter}%`, // FILTROS DISPONIBLES: BOX1, BOX2, C
        },
      },
      order: [["createdAt", "ASC"]], // Ordenar por fecha de creación en orden descendente
    });

    const actual = await Turno.findOne({
      where: {
        estado: "Actual", // LOS 3 ESTADOS POSIBLES SON: FINALIZADO, ACTUAL, ESPERA
        nombre_turno: {
          [Op.like]: `%${req.params.filter}%`, // FILTROS DISPONIBLES: BOX1, BOX2, C
        },
      },
      order: [["createdAt", "ASC"]], // Ordenar por fecha de creación en orden descendente
    });

    res.json([actual, siguiente]);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener los turnos" });
  }
});

router.get("/turnosVisor", async (req, res) => {
  try {
    const turnos = await Turno.findAll({
      limit: 3, // Limitar la cantidad de registros a 3
      where: {
        estado: {
          [Op.or]: ["Actual", "Ventas"],
        },
        [Op.or]: [
          {
            nombre_turno: {
              [Op.like]: "%BOX%", // FILTROS DISPONIBLES: BOX1, BOX2
            },
          },
          {
            nombre_turno: "C",
          },
        ],
      },
      order: [
        ["nombre_turno", "ASC"],
        ["createdAt", "ASC"],
      ], // Ordenar por fecha de creación en orden descendente
    });

    const result = {
      Box1: turnos[0],
      Box2: turnos[1],
      Ventas: turnos[2],
    };

    res.json(result);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener los turnos" });
  }
});

router.get("/cantidadTurnos/:num", async (req, res) => {
  try {
    const count = await Turno.count({
      where: {
        estado: "Espera",
        nombre_turno: {
          [Op.like]: `%BOX%${req.params.num}`,
        },
      },
    });

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

    // Define los argumentos que deseas pasar al script de Python
    const turno = req.body.nombre_turno;

    // Comando para ejecutar el script de Python con argumentos
    const pythonScriptPath = "./Script/print_script.py";
    const pythonProcess = spawn("python", [pythonScriptPath, turno]);

    pythonProcess.stdout.on("data", (data) => {
      console.log(`Resultado del script: ${data}`);
      res.status(200).json({ resultado: data.toString() });
    });

    pythonProcess.stderr.on("data", (data) => {
      console.error(`Error en la salida estándar de error: ${data}`);
      res.status(500).json({ error: data.toString() });
    });

    pythonProcess.on("close", (code) => {
      console.log(`Proceso de Python cerrado con código de salida ${code}`);
    });

    res.status(200).json({ nuevoTurno });
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
