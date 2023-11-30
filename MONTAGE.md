1. Ingresa a la siguiente ruta: C:\Windows\System32\drivers\etc

   - Una vez alli, editar el archivo "hosts" agregando las siguientes lineas:
     ```
     # My Application
     127.0.0.1    veterinaria.local
     127.0.0.1    api.veterinaria.local
     ```

2. Ingresar al archivo "httpd.conf" del servicio de apache dentro de xampp

   - Una vez alli, localizar la seccion "Virtual hosts" y agregar las siguientes lineas:

     ```
     <VirtualHost *:80>
         ServerName veterinaria.local
         DocumentRoot "C:/xampp/htdocs/dist"
         <Directory "C:/xampp/htdocs/dist">
             Options Indexes FollowSymLinks
             AllowOverride All
             Require all granted

         Header always set Access-Control-Allow-Origin "*"
         Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
         Header always set Access-Control-Allow-Headers "Origin, X-Requested-With, Content-Type, Accept"
         </Directory>
     </VirtualHost>

     <VirtualHost *:80>
         ServerName api.veterinaria.local
         ProxyPass / http://localhost:5000
         ProxyPassReverse / http://localhost:5000
     </VirtualHost>
     ```

3. Ingresar dentro de la carpeta "backend" y ejecutar el siguiente comando:

   ```bash
   pm2 start index.js
   ```

   (Recuerde leer el archivo README.md dentro de la carpeta backend)

4. Ingresar dentro de la carpeta "frontend" y ejecutar el siguiente comando:

   ```bash
   npm run build
   ```

5. Copiar la carpeta "dist" que se creo con el comando anterior en la siguiente ruta: "C:\xampp\htdocs"

6. Finalmente iniciar los servicios de apache y mysql en xampp. (la url de la pagina es "veterinaria.local")
