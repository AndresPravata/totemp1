CREATE TABLE veterinarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  apellido VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL
);

CREATE TABLE disponibilidad (
  id INT AUTO_INCREMENT PRIMARY KEY,
  opcion VARCHAR(255) NOT NULL
);

CREATE TABLE veterinarios_elegidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  apellido VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  disponibilidad_id INT NOT NULL,
  FOREIGN KEY (disponibilidad_id) REFERENCES disponibilidad(id)
);
