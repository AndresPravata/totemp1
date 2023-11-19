import { DataTypes } from "sequelize";
import { sequelizeOnline } from "../config/databases";
import Veterinario from "./Veterinario";
import Box from "./Box";

const Turno = sequelizeOnline.define("turnos", {
  id: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  nombre_turno: {
    type: DataTypes.STRING,
    allowNull: true,
  },
  fecha_hora_inicio: {
    type: DataTypes.DATE,
    allowNull: true,
  },
  fecha_hora_fin: {
    type: DataTypes.DATE,
    allowNull: false,
  },
  estado: {
    type: DataTypes.STRING,
    allowNull: false,
  },
});

Turno.belongsTo(Box, {
  foreignKey: "boxId",
});

Turno.belongsTo(Veterinario, {
  foreignKey: "veterinarioId",
});

export default Turno;
