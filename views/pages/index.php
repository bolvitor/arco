<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ARCO</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: radial-gradient(circle, #515155, #668cb3 40%, #515155 70%);
      color: #ffffff;
      overflow: hidden;
    }

    #background-image {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -1;
      opacity: 0.7;
   
    }

    #overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);

      z-index: -1;
    }

    #content-container {
      margin-top: 15%;
      text-align: center;
      z-index: 1;
    }

    #app-name {
      margin: auto;
      font-size: 5em;

      margin-top: 20px;

    }
  </style>
</head>

<body>
  <img id="background-image" src="./images/arco.jpeg" alt="ARCO Background Image">
  <div id="overlay"></div>
  <div id="content-container"> 
    <div class="row mb-3">
      <div class="col text-center">
        <h1 id="app-name">ARCO</h1>
        <h3>Actualización del Record de Carrera del Oficial del Ejército de Guatemala</h2>
      </div>
    </div>
  </div>
  <script src="build/js/inicio.js"></script>
</body>

</html>