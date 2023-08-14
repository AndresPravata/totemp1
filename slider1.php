<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0; /* Remove default margin */
      overflow: hidden; /* Hide any potential body overflow */
    }

    .background-image {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: url('veterinariaDrLuffi.png') center/cover no-repeat;
      background-color: rgba(0, 0, 0, 0.5); /* Transparent black color */
    }

    .carousel-item {
      height: 26.5rem;
      background: transparent;
      margin-top: 10.20rem;
    }

    .container {
      position: relative;
    }
  </style>
</head>
<body>
  <div class="background-image"></div>
  <div class="container">
    <div id="miCarrusel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="slider1.jpg" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="slider2.jpg" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
          <img src="..." class="d-block w-100" alt="...">
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>
