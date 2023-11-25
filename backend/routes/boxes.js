import express from 'express';
import { Box } from '../models/sequelize.js';

const router = express.Router();

// Obtener todas las boxes
router.get('/', async (req, res) => {
  try {
    const boxes = await Box.findAll();
    res.json(boxes);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al obtener las boxes' });
  }
});

// Crear una nueva box
router.post('/', async (req, res) => {
  try {
    const nuevaBox = await Box.create({
      estado: req.body.estado,
    });
    res.json(nuevaBox);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al crear la box' });
  }
});

// Obtener una box por ID
router.get('/:id', async (req, res) => {
  try {
    const box = await Box.findByPk(req.params.id);
    res.json(box);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al obtener la box' });
  }
});

// Actualizar una box por ID
router.put('/:id', async (req, res) => {
  try {
    const box = await Box.findByPk(req.params.id);
    if (box) {
      await box.update({
        estado: req.body.estado,
      });
      res.json(box);
    } else {
      res.status(404).json({ error: 'Box no encontrada' });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al actualizar la box' });
  }
});

// Eliminar una box por ID
router.delete('/:id', async (req, res) => {
  try {
    const box = await Box.findByPk(req.params.id);
    if (box) {
      await box.destroy();
      res.json({ mensaje: 'Box eliminada exitosamente' });
    } else {
      res.status(404).json({ error: 'Box no encontrada' });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al eliminar la box' });
  }
});

export default router;
