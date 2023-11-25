import express from "express";
import { Totem } from '../models/sequelize.js';

const router = express.Router();

// Obtener todos los totems
router.get("/", async (req, res) => {
  try {
    const totems = await Totem.findAll();
    res.json(totems);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener los totems" });
  }
});

// Crear un nuevo totem
router.post("/", async (req, res) => {
  try {
    const nuevoTotem = await Totem.create({
      nombre_turno: req.body.nombre_turno,
      fecha_hora_inicio: req.body.fecha_hora_inicio,
      fecha_hora_fin: req.body.fecha_hora_fin,
      estado: req.body.estado,
      veterinario: req.body.veterinario,
    });
    res.json(nuevoTotem);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al crear el totem" });
  }
});

// Obtener un totem por ID
router.get("/:id", async (req, res) => {
  try {
    const totem = await Totem.findByPk(req.params.id);
    res.json(totem);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al obtener el totem" });
  }
});

// Actualizar un totem por ID
router.put("/:id", async (req, res) => {
  try {
    const totem = await Totem.findByPk(req.params.id);
    if (totem) {
      await totem.update({
        nombre_turno: req.body.nombre_turno,
        fecha_hora_inicio: req.body.fecha_hora_inicio,
        fecha_hora_fin: req.body.fecha_hora_fin,
        estado: req.body.estado,
        veterinario: req.body.veterinario,
        // ... otros campos
      });
      res.json(totem);
    } else {
      res.status(404).json({ error: "Totem no encontrado" });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al actualizar el totem" });
  }
});

// Eliminar un totem por ID
router.delete("/:id", async (req, res) => {
  try {
    const totem = await Totem.findByPk(req.params.id);
    if (totem) {
      await totem.destroy();
      res.json({ mensaje: "Totem eliminado exitosamente" });
    } else {
      res.status(404).json({ error: "Totem no encontrado" });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: "Error al eliminar el totem" });
  }
});

export default router;
