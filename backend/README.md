# Nombre de tu Proyecto

Bienvenido a [Nombre de tu Proyecto]. Este proyecto utiliza Express para construir un backend que gestiona [descripción breve de la funcionalidad].

## Documentación de la API

La documentación completa de la API se encuentra disponible a través de Swagger. Puedes explorar y probar los endpoints directamente desde la interfaz de Swagger.

**Enlace a la documentación de Swagger:** [http://localhost:5000/api-docs](http://localhost:5000/api-docs)

## Configuración del Proyecto

### Requisitos

- Node.js
- npm

### Instalación

1. Clona el repositorio:

```bash
git clone [URL del repositorio]
cd nombre-del-proyecto
```

2. Instala las dependencias:

```bash
npm install
```

3. Completa los datos del .env como se muestra en .env.example:

```
ORIGIN=

MYSQL_LOCAL_HOST=
MYSQL_LOCAL_USER=
MYSQL_LOCAL_PASSWORD=
MYSQL_LOCAL_DATABASE=

MYSQL_ONLINE_HOST=
MYSQL_ONLINE_USER=
MYSQL_ONLINE_PASSWORD=
MYSQL_ONLINE_DATABASE=

PORT_LISTEN=
```

4. Instala las dependencias del Script en python (debes estar situado en el directorio "Scripts"):

```python
pip install -r requirements.txt
```

4. Ejecuta el proyecto en la carpeta raiz:

```bash
npm start
```

### Estructura del Proyecto:

- /config: Configuración del servidor y otras configuraciones.
- /models: Modelos de base de datos utilizando Sequelize.
- /routes: Rutas de la API.
- /docs: Documentación y comentarios.
- /index.js: Punto de entrada de la aplicación.
