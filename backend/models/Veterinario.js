import { DataTypes } from "sequelize";
import { sequelizeOnline } from "../config/databases";
import Turno from "./Turno";

const Veterinario = sequelizeOnline.define("veterinarios", {
  N_Veterinario: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
});

Veterinario.hasMany(Turno, {
  foreignKey: "veterinarioId",
  onDelete: 'CASCADE',
});

export default Veterinario;
