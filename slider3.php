<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider</title>
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
    <div class="background-image"></div>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: lato;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            padding-top: 20px; /* Añade un espacio arriba para separar el título */
        }

        body {
            overflow: hidden; /* Oculta barras de desplazamiento en el cuerpo */
        }

        .slider-container {
            width: 70%; /* Ancho del slider */
            max-width: 1000px; /* Ancho máximo del slider */
            margin: 0 auto; /* Centra el contenedor horizontalmente */
        }

        .slider-frame {
            width: 100%; /* Ocupa el ancho completo del contenedor */
            max-height: calc(85vh); /* Altura máxima del slider (75% de la pantalla) */
            overflow: hidden; /* Oculta barras de desplazamiento en el slider */
        }

        .slider-frame ul {
            display: flex;
            padding: 0;
            width: 400%;
            animation: slide 20s infinite alternate ease-in-out;
        }

        .slider-frame li {
            width: calc(100% / 4); /* Cambiado de 100% a 25% para acomodar 4 imágenes */
            list-style: none;
        }

        .slider-frame img {
            width: 100%;
            height: 50%; /* Ajusta el alto de las imágenes al 100% */
            object-fit: cover; /* Ajusta la imagen para cubrir completamente el espacio */
        }

        @keyframes slide {
            0% {margin-left: 0;}
            20% {margin-left: 0;}
            25% {margin-left: -100%;}
            45% {margin-left: -100%;}
            50% {margin-left: -200%;}
            70% {margin-left: -200%;}
            75% {margin-left: -300%;}
            100% {margin-left: -300%;}
        }

        .visor-iframe {
            width: 100%;
            height: 15vh; /* Altura del iframe (25% de la pantalla) */
            border: 0;
        }
        .background-image {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    background: url('veterinariaDrLuffi.png') center/cover no-repeat;
}
    </style>
</head>
<body>


    <div class="slider-container">
        <div class="slider-frame">
            <ul>
                <li><img src="slider1.jpg" alt=""></li>
                <li><img src="slider2.jpg" alt=""></li>
                <li><img src="slider3.jpg" alt=""></li>
                <li><img src="slider4.jpg" alt=""></li>
            </ul>
        </div>
    </div>

    <div>
        <iframe class="visor-iframe" src="http://localhost/p1/visor1.php" frameborder="0"></iframe>
    </div>
</body>
</html>