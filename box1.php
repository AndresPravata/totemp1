<?php
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "turnos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
date_default_timezone_set("America/Argentina/Buenos_Aires");

// Función para marcar el turno del Box 1 como finalizado
function marcarTurnoFinalizado() {
    global $conn;
    
    // Obtener el turno actual del Box 1
    $sql = "SELECT nombre_turno FROM turnos WHERE numero_box = 1 AND estado = 'actual' LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombreTurno = $row['nombre_turno'];
        
        // Actualizar el estado del turno a 'finalizado'
        $sql = "UPDATE turnos SET estado = 'finalizado' WHERE nombre_turno = '$nombreTurno'";
        $conn->query($sql);
        
       
    }
}

// Verificar si se ha enviado la solicitud para marcar el turno como finalizado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_finalizado'])) {
    marcarTurnoFinalizado();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    <title>BOX 1</title>
    <style>
 /* Estilos para el contenedor de botones y el círculo de estado */
.container {
  display: flex; /* Usa flexbox para alinear los elementos horizontalmente */
  align-items: center; /* Centra verticalmente los elementos */
  justify-content: flex-start; /* Alinea los elementos a la izquierda */
}

.button-container {
  display: flex;
  gap: 10px; /* Espacio entre los botones */
}

/* Estilos para los botones (sin cambios) */
.button {
  display: inline-block;
  padding: 10px 20px;
  background-color: transparent;
  color: #fff;
  border: none;
  cursor: pointer;
}

/* Estilos para el círculo de estado (sin cambios) */
.status-container {
  display: inline-block;
  margin-left: 10px;
}

.status-circle {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: inline-block;
  transition: background-color 0.3s ease;
}

.green {
  background-color: #4caf50;
}

.red {
  background-color: #f44336;
}

    </style>
</head>
<body>
   <!-- Wrapper -->
   <div id="wrapper">

<!-- Header -->
<header id="header">
    <div class="logo logo-container">
        <img src="images/logo.png" alt="Logo de Totem Veterinaria">
    </div>
        <div class="content">
            <div class="inner">
                <h1>Veterinaria Dr.Luffi</h1>
            <!-- <p>A fully responsive site template designed by <a href="https://html5up.net">HTML5 UP</a> and released<br />
                for free under the <a href="https://html5up.net/license">Creative Commons</a> license.</p> -->
            </div>
           

<body>
<div class="container">
  <div class="button-container">
    <button class="button" id="presentButton">Presente</button>
    <button class="button" id="absentButton">Ausente</button>
  </div>
  <div class="status-container">
    <div class="status-circle" id="statusCircle"></div>
  </div>
</div>
            <div id="box1-current-turn"></div>
    <form method="post">
        <button type="submit" name="marcar_finalizado">Finalizar Turno</button>
    </form>
        </div>

    <script>

// JavaScript para cambiar el color del círculo de estado
const presentButton = document.getElementById("presentButton");
  const absentButton = document.getElementById("absentButton");
  const statusCircle = document.getElementById("statusCircle");

  presentButton.addEventListener("click", () => {
    statusCircle.classList.remove("red");
    statusCircle.classList.add("green");
  });

  absentButton.addEventListener("click", () => {
    statusCircle.classList.remove("green");
    statusCircle.classList.add("red");
  });
 function actualizarVeterinario() {
    // Coloca aquí el código que deseas ejecutar cuando se cambie el estado del checkbox
}


        var box1CurrentTurn = <?php echo json_encode($box1CurrentTurn); ?>;

        function updateVisor() {
            // Limpiar el turno actual del box 1
            document.getElementById("box1-current-turn").innerText = "";

            // Mostrar el turno actual del box 1
            if (box1CurrentTurn) {
                document.getElementById("box1-current-turn").innerText = box1CurrentTurn.nombre_turno;
            }
        }

        function updateTurns() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    box1CurrentTurn = response.box1CurrentTurn;
                    updateVisor();
                }
            };
            xmlhttp.open("GET", "update_turns.php", true);
            xmlhttp.send();
        }

     
    </script>
</body>
</html>
