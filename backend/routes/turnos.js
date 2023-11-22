import express from "express";
import { Turno } from '../models/sequelize.js';

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

// Crear un nuevo turno
router.post("/", async (req, res) => {
  try {
    const nuevoTurno = await Turno.create({
      nombre_turno: req.body.nombre_turno,
      fecha_hora_inicio: req.body.fecha_hora_inicio,
      fecha_hora_fin: req.body.fecha_hora_fin,
      estado: req.body.estado,
      boxId: req.body.boxId,
      veterinarioId: req.body.veterinarioId,
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
