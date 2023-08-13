<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slider</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    
    <div class="slider-frame">
        <ul>
            <li><img src="slider1.jpg" alt=""></li>
            <li><img src="slider2.jpg" alt=""></li>
            <li><img src="slider3.jpg" alt=""></li>
            <li><img src="slider4.jpg" alt=""></li>
        </ul>
    </div>
    <style>
        * {
    margin: 0;
    padding: 0;
    font-family: lato;
}

h2 {
    text-align: center;
    font-size: 2rem;
    padding-top: 50px;
}

.slider-frame {
	width: 1280px;
    height: auto;
	margin:50px auto 0;
	overflow: hidden;
}

.slider-frame ul {
	display: flex;
	padding: 0;
	width: 400%;
	
	animation: slide 20s infinite alternate ease-in-out;
}

.slider-frame li {
	width: 100%;
	list-style: none;
}

.slider-frame img {
	width: 100%;
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

    </style>
</body>
</html>
