import { Turno } from "../models/sequelize.js";
import { Op } from "sequelize";

export const obtenerInformacionTurno = async () => {
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
            nombre_turno: "C%",
          },
        ],
      },
      order: [
        ["nombre_turno", "ASC"],
        ["createdAt", "ASC"],
      ], // Ordenar por fecha de creación en orden descendente
    });

    const result = {
      Box1: turnos[0]?.nombre_turno.startsWith("C") ? null : turnos[0],
      Box2: turnos[1]?.nombre_turno.startsWith("C") ? null : turnos[1],
      Ventas: turnos[2]?.nombre_turno.startsWith("C") ? turnos[2] : null,
    };

    return result;
  } catch (error) {
    console.error(error);
    throw new Error("Error al obtener la información de los turnos");
  }
};

export const obtenerSiguienteTurno = async (filter) => {
  try {
    const siguiente = await Turno.findOne({
      where: {
        estado: "Espera", // LOS 3 ESTADOS POSIBLES SON: FINALIZADO, ACTUAL, ESPERA
        nombre_turno: {
          [Op.like]: `%${filter}%`, // FILTROS DISPONIBLES: BOX1, BOX2, C
        },
      },
      order: [["createdAt", "ASC"]], // Ordenar por fecha de creación en orden descendente
    });

    return siguiente;
  } catch (error) {
    console.error(error);
    throw new Error("Error al obtener la información de los turnos");
  }
};

export const obtenerActualTurno = async (filter) => {
  try {
    const actual = await Turno.findOne({
      where: {
        estado: "Actual", // LOS 3 ESTADOS POSIBLES SON: FINALIZADO, ACTUAL, ESPERA
        nombre_turno: {
          [Op.like]: `%${filter}%`, // FILTROS DISPONIBLES: BOX1, BOX2, C
        },
      },
      order: [["createdAt", "ASC"]], // Ordenar por fecha de creación en orden descendente
    });

    return actual;
  } catch (error) {
    console.error(error);
    throw new Error("Error al obtener la información de los turnos");
  }
};

export const obtenerCantidadBox = async (num) => {
  try {
    const count = await Turno.count({
      where: {
        estado: "Espera",
        nombre_turno: {
          [Op.like]: `%BOX%${num}`,
        },
      },
    });

    return count;
  } catch (error) {
    console.error(error);
    throw new Error("Error al obtener la información de los turnos");
  }
};
