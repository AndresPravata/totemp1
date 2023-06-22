<!DOCTYPE HTML>
<html>
<head>
	<title>Totem Veterinaria</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="assets/css/main copy.css" />
	<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
</head>
<body class="is-preload">

<!-- Wrapper -->
<div id="wrapper">

	<!-- Header -->
	<header id="header">
		<div class="logo logo-container">
			<img src="images/logo.png" alt="Logo de Totem Veterinaria">
		</div>
		<div class="content">
			<div class="inner">
				<style>
					#rectangulo {
						width: 200px;
						height: 80px;
						border: 2px solid black;
						text-align: center;
						line-height: 80px;
						font-size: 20px;
						position: absolute;
						top: 50%;
						left: 50%;
						transform: translate(-50%, -50%);
					}

					@media (max-width: 600px) {
						#rectangulo {
							width: 80%;
						}
					}

					.buttons-container {
						position: absolute;
						top: calc(50% + 100px);
						left: 50%;
						transform: translate(-50%, -50%);
					}

					.buttons-container button {
						margin: 5px;
					}
				</style>
				<div id="rectangulo">
					<?php
					// Realizar la conexión a la base de datos
					$servername = "localhost";
					$username = "root";
					$password = "123";
					$dbname = "turnos";

					$conn = new mysqli($servername, $username, $password, $dbname);

					if ($conn->connect_error) {
						die("Error de conexión: " . $conn->connect_error);
					}

					// Obtener el estado del box 1 desde la base de datos
					$sql = "SELECT estado FROM boxes WHERE N_boxes = 1";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						$row = $result->fetch_assoc();
						$estado = $row["estado"];

						// Mostrar el estado en el rectángulo
						echo $estado;
					} else {
						echo "Desconocido";
					}

					$conn->close();
					?>
				</div>

				<div class="buttons-container">
					<button onclick="setBoxOccupied(1)">Ocupado</button>
					<button onclick="setBoxAvailable(1)">Desocupado</button>
				</div>

				<script>
					function setBoxOccupied(boxNumber) {
						var rectangulo = document.getElementById('rectangulo');
						rectangulo.innerHTML = 'Ocupado';
						rectangulo.style.backgroundColor = 'red';

						// Realizar una solicitud AJAX para marcar el box como ocupado en la base de datos
						var xhttp = new XMLHttpRequest();
						xhttp.open("GET", "marcar_box_ocupado.php?box=" + boxNumber, true);
						xhttp.send();
					}

					function setBoxAvailable(boxNumber) {
						var rectangulo = document.getElementById('rectangulo');
						rectangulo.innerHTML = 'Desocupado';
						rectangulo.style.backgroundColor = 'green';

						// Realizar una solicitud AJAX para marcar el box como disponible en la base de datos
						var xhttp = new XMLHttpRequest();
						xhttp.open("GET", "marcar_box_disponible.php?box=" + boxNumber, true);
						xhttp.send();
					}
				</script>
			</div>
			<nav>
				<ul>
				</ul>
			</nav>
		</div>
	</header>

	<!-- Main -->
	<div id="main">
		<!-- Contenido principal -->
	</div>

	<!-- Footer -->
	<footer id="footer">
		<p>&copy;AndesTech</p>
	</footer>

</div>

<!-- BG -->
<div id="bg"></div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/browser.min.js"></script>
<script src="assets/js/breakpoints.min.js"></script>
<script src="assets/js/util.js"></script>
<script src="assets/js/main.js"></script>

</body>
</html>
