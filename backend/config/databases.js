import { Sequelize } from "sequelize";
import dotenv from "dotenv";

dotenv.config();

export const sequelizeOnline = new Sequelize(
  process.env.MYSQL_ONLINE_DATABASE,
  process.env.MYSQL_ONLINE_USER,
  process.env.MYSQL_ONLINE_PASSWORD,
  {
    host: process.env.MYSQL_ONLINE_HOST,
    dialect: "mysql",
    logging: false,
    define: {
      timestamps: true,
    },
  }
);

// Verifica la conexión a la base de datos
sequelizeOnline
  .authenticate()
  .then(() => {
    console.log("Conectado a la base de datos MySQL ONLINE");
  })
  .catch((error) => {
    console.error("Error de conexión a la base de datos MySQL ONLINE:", error);
  });

export const sequelizeLocal = new Sequelize(
  process.env.MYSQL_LOCAL_DATABASE,
  process.env.MYSQL_LOCAL_USER,
  process.env.MYSQL_LOCAL_PASSWORD,
  {
    host: process.env.MYSQL_LOCAL_HOST,
    dialect: "mysql",
    logging: false,
    define: {
      timestamps: true,
    },
  }
);

// Verifica la conexión a la base de datos
sequelizeLocal
  .authenticate()
  .then(() => {
    console.log("Conectado a la base de datos MySQL LOCAL");
  })
  .catch((error) => {
    console.error("Error de conexión a la base de datos MySQL LOCAL:", error);
  });
