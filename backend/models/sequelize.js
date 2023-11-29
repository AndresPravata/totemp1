import { DataTypes, NOW } from "sequelize";
import { sequelizeOnline } from "../config/databases.js";
import { sequelizeLocal } from "../config/databases.js";

export const Box = sequelizeOnline.define(
  "boxes",
  {
    N_boxes: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    estado: {
      type: DataTypes.STRING,
      allowNull: false,
    },
  },
  {
    timestamps: false, // Agrega esta opción para incluir las marcas de tiempo predeterminadas
  }
);

export const Totem = sequelizeLocal.define(
  "totem",
  {
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
  },
  {
    timestamps: false, // Agrega esta opción para incluir las marcas de tiempo predeterminadas
  }
);

export const Turno = sequelizeOnline.define(
  "turnos",
  {
    id: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    nombre_turno: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    createdAt: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: DataTypes.NOW,
    },
    fecha_hora_inicio: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    fecha_hora_fin: {
      type: DataTypes.DATE,
      allowNull: true,
    },
    estado: {
      type: DataTypes.STRING,
      allowNull: true,
      defaultValue: "Espera",
    },
  },
  {
    timestamps: false, // Agrega esta opción para incluir las marcas de tiempo predeterminadas
  }
);

export const Veterinario = sequelizeOnline.define(
  "veterinarios",
  {
    N_Veterinario: {
      type: DataTypes.INTEGER,
      primaryKey: true,
      autoIncrement: true,
    },
    nombre: {
      type: DataTypes.STRING,
    },
  },
  {
    timestamps: false, // Agrega esta opción para incluir las marcas de tiempo predeterminadas
  }
);

Box.hasMany(Turno, {
  foreignKey: "numero_box",
  onDelete: "CASCADE",
});

Turno.belongsTo(Box, {
  foreignKey: "numero_box",
});

Turno.belongsTo(Veterinario, {
  foreignKey: "veterinario_id",
});

Veterinario.hasMany(Turno, {
  foreignKey: "veterinario_id",
  onDelete: "CASCADE",
});

await sequelizeOnline.sync({ alter: true });
