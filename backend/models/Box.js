import { DataTypes } from "sequelize";
import { sequelizeOnline } from "../config/databases";
import Turno from "./Turno";

const Box = sequelizeOnline.define("boxes", {
  N_boxes: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  estado: {
    type: DataTypes.STRING,
    allowNull: false,
  },
});

Box.hasMany(Turno, {
  foreignKey: "boxId",
  onDelete: 'CASCADE',
});

export default Box;
