<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Ascenso de Oficiales</title>
    <!-- Agregamos Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 1200px;
            margin: 50px auto;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #3498db;
            margin-bottom: 20px;
        }

        #card {
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            margin-bottom: 30px;
            border-radius: 10px;
            box-sizing: border-box;
            max-width: 400px;
            margin-left: 30%;
            
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            transform: translateY(-10px);
        }



        #formularioFiltros {
            text-align: center;
            margin-bottom: 20px;
            max-width: 400px;
            margin: 0 auto;
        }

        #btnBuscar {
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        #btnBuscar:hover {
            background-color: #2079b0;
        }

        #inputPromocion {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .graficas {
            margin-top: 20px;
        }
        h4{
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="reporte-titulo">ESTADISTICAS DE ASCENSO DE OFICIALES</h1>
        
        <!-- Primera fila con el formulario -->
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center" id="card">
               
                    <form id="formularioFiltros">
                        <div class="form-group">
                            <label for="inputPromocion">Promoción</label>
                            <input type="number" class="form-control" id="inputPromocion" name="btnPromocion">
                        </div>
                        <button id="btnBuscar" class="btn btn-outline-info" > Buscar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Segunda fila dividida en dos columnas para las gráficas -->
        <div class="row graficas">
            <div class="col-md-6">
                <div class="card">
                    <h4>Grado de Oficiales</h4>
                    <canvas id="chartPromocion"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h4>Oficiales Postergados</h4>
                    <canvas id="chartpostergados"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= asset('./build/js/estadisticas/index.js') ?>"></script>
</body>

</html>
