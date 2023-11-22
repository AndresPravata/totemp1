import express from 'express';
import { Veterinario } from '../models/sequelize.js';

const router = express.Router();

// Obtener todos los veterinarios
router.get('/', async (req, res) => {
  try {
    const veterinarios = await Veterinario.findAll();
    res.json(veterinarios);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al obtener los veterinarios' });
  }
});

// Crear un nuevo veterinario
router.post('/', async (req, res) => {
  try {
    const nuevoVeterinario = await Veterinario.create();
    res.json(nuevoVeterinario);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al crear el veterinario' });
  }
});

// Obtener un veterinario por ID
router.get('/:id', async (req, res) => {
  try {
    const veterinario = await Veterinario.findByPk(req.params.id);
    res.json(veterinario);
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al obtener el veterinario' });
  }
});

// Actualizar un veterinario por ID
router.put('/:id', async (req, res) => {
  try {
    const veterinario = await Veterinario.findByPk(req.params.id);
    if (veterinario) {
      await veterinario.update();
      res.json(veterinario);
    } else {
      res.status(404).json({ error: 'Veterinario no encontrado' });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al actualizar el veterinario' });
  }
});

// Eliminar un veterinario por ID
router.delete('/:id', async (req, res) => {
  try {
    const veterinario = await Veterinario.findByPk(req.params.id);
    if (veterinario) {
      await veterinario.destroy();
      res.json({ mensaje: 'Veterinario eliminado exitosamente' });
    } else {
      res.status(404).json({ error: 'Veterinario no encontrado' });
    }
  } catch (error) {
    console.error(error);
    res.status(500).json({ error: 'Error al eliminar el veterinario' });
  }
});

export default router;
