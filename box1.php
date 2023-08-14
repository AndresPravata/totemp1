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
            <div id="box1-current-turn"></div>
    <form method="post">
        <button type="submit" name="marcar_finalizado">Finalizar Turno</button>
    </form>
        </div>

    <script>
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
