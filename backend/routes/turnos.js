import express from "express";
import { Turno } from "../models/sequelize.js";
import { Op } from "sequelize";

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
    const turnos = await Turno.findAll({
      limit: 3, // Limitar la cantidad de registros a 3
      where: {
        estado: "Espera", // LOS 3 ESTADOS POSIBLES SON: INICIADO, FINALIZADO, ESPERA
        nombre_turno: {
          [Op.like]: `%${req.params.filter}%`, // FILTROS DISPONIBLES: BOX1, BOX2, C
        },
      },
      order: [["createdAt", "DESC"]], // Ordenar por fecha de creación en orden descendente
    });

    res.json(turnos);
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
          [Op.or]: ["Iniciado", "Ventas"],
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
        ["createdAt", "DESC"],
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
    res.json(nuevoTurno);
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
