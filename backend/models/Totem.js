import { DataTypes } from "sequelize";
import { sequelizeLocal } from "../config/databases";

const Totem = sequelizeLocal.define("totem", {
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
  veterinario: {
    type: DataTypes.STRING,
    allowNull: false,
  },
});

export default Totem;
