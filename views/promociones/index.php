<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu Aplicación</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        img {
            border: 2px solid #ccc;
            max-width: 150%;
            height: auto;
        }

        h2 {
            text-align: center;
            color: black;

        }


        .form-container label {
            font-weight: bold;
            border-color: #C3C3CC;
            margin: auto;


        }


        .custom-button {
            border: 3px solid #9E9EAB;
            border-radius: 8px;
            width: 80px;

            height: 60px;

            margin-top: 20px;
        }

        .custom-button img {
            width: 100%;
            height: 100%;
            border: none;
        }

        .button-with-shadow {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }



        .transparent-background {
            background-color: transparent;

            padding: 10px;

        }



        .bordered-container {
            box-shadow: 0px 8px 8px rgba(102, 140, 179, 0.3);
            border: 4px solid #C3C3CC;

            border-radius: 10px;

            padding: 20px;

        }

        .transparent-background {
            background-color: transparent;

            padding: 10px;

        }

        #cont-principal {
            margin: 30px;

            background-color: white;
            padding: 20px;

        }

        body {
            background: radial-gradient(circle, #515155, #668cb3 40%, #515155 70%);

            color: #515155;

        }

        #cont-formulario {
            max-width: 800px;
        }

        .modal-open {
            overflow: hidden;
        }

        .modal-open::after {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);

            z-index: 999;

        }


        #det-imagen {
            max-width: 100%;
        }
    </style>
</head>

<body>
    <div class="row justify-content-center" id="cont-principal">
        <div id="cont-formulario" style=" padding:10px; box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
            <h2 class=" transparent-background">Informacion Militar</h2>

            <form class="form-container" id="formularioPromociones">
                <div class="row">
                    <div class="col-md-3">
                        <img src="./images/ep.jpg" id="foto" alt="Fotografía" class="img-fluid">
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="per_promocion"><i class="fas fa-graduation-cap"></i> Número de Promoción:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="per_promocion" name="per_promocion">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="per_arma"><i class="bi bi-bullseye"></i> Arma:</label>
                            <select name="per_arma" id="per_arma" class="form-control">
                                <option value="">SELECCIONE...</option>
                                <?php foreach ($armas as $arma) : ?>
                                    <option value="<?= $arma['arm_codigo'] ?>">
                                        <?= $arma['arm_desc_md'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="form-group">

                            <button type="button" id="btnBuscar" class="btn btn-outline-dark custom-button">
                                <img src="./images/buscar.png" alt="Buscar">
                            </button>

                            <button type="reset" id="btnLimpiar" class="btn btn-outline-dark custom-button button-with-shadow">
                                <img src="./images/actualizar.png" alt="Actualizar">
                            </button>
                        </div>
                    </div>
            </form>
        </div>
    </div>




    <div class="row justify-content-center">
        <div class="col table-responsive" style="max-width: 90%; padding: 40px; background-color: #f8f9fa; box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
            <table id="tablaPromociones" class="container p-4 shadow-lg style, table table-bordered table-hover" style="width: 100%; border-collapse: collapse;">
                <!-- Contenido de la tabla aquí -->
            </table>
        </div>
    </div>







    <div class="modal fade" id="verExistencias" name="verExistencias" tabindex="-1" role="dialog" aria-labelledby="verExistenciasLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verExistenciasLabel"></h5>
                    <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">Salir de esta ventana</span>
                    </button>
                </div>


                <div class="container mt-4">
                    <h2>Información Militar</h2>

                    <form class="form-container" id="formularioPromocion">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="foto-container">
                                    <img src="./images/foto.jpg" id="foto" alt="Fotografía" class="img-fluid">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-row">

                                    <label for="per_catalogo"><i class="bi bi-person-vcard-fill"></i> Número de Catálogo:</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="per_catalogo" name="per_catalogo">
                                    </div>


                                    <label for="nombre"><i class="bi bi-person-fill"></i> Nombres:</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Primer Nombre">
                                </div>


                                <div class="form-row">

                                    <label for="grado"><i class="bi bi-shield-shaded"></i> Grado:</label>
                                    <input type="text" class="form-control" id="grado" name="grado" placeholder="Grado">


                                    <label for="per_promocion"><i class="bi bi-people-fill"></i> Promoción:</label>
                                    <input type="number" class="form-control" id="per_promocion" name="per_promocion">

                                </div>
                            </div>
                            <div class="col-md-4">

                                <div class="form-row">

                                    <label for="dependencia"><i class="bi bi-buildings-fill"></i> Dependencia:</label>
                                    <input type="text" class="form-control" id="dependencia" name="dependencia" placeholder="Dependencia">


                                    <label for="puesto"><i class="bi bi-house-gear-fill"></i> Puesto:</label>
                                    <input type="text" class="form-control" id="puesto" name="puesto" placeholder="Puesto">
                                </div>


                                <div class="form-row">

                                    <label for="t_prox_asc"><i class="bi bi-calendar-check-fill"></i> Fecha Ascenso:</label>
                                    <input type="text" class="form-control" id="t_prox_asc" name="t_prox_asc" placeholder="Fecha Ascenso">

                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="modal-body" id="verExistenciasBody">
                    <div class="row mb-2 justify-content-center text-center" id="cardInfo">
                        <h3 id="tituloNombre"></h3>
                        <div class="col-md-12">
                            <div class="card text-center">
                                <div class="card-header">
                                    <ul class="nav nav-tabs card-header-tabs">

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="cursos-tab" data-bs-toggle="tab" data-bs-target="#cursos" type="button" role="tab" aria-controls="cursos" aria-selected="true">Curso Ascenso</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="desempenio-tab" data-bs-toggle="tab" data-bs-target="#desempenio" type="button" role="tab" aria-controls="desempenio" aria-selected="false">Eva_desempeño</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="sanciones-tab" data-bs-toggle="tab" data-bs-target="#sanciones" type="button" role="tab" aria-controls="sanciones" aria-selected="true">Conducta Militar</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="pafes-tab" data-bs-toggle="tab" data-bs-target="#pafes" type="button" role="tab" aria-controls="pafes" aria-selected="false">Aptitud Fisica</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="perfil-tab" data-bs-toggle="tab" data-bs-target="#perfil" type="button" role="tab" aria-controls="perfil" aria-selected="false">Perfil Biosifico</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="puestos-tab" data-bs-toggle="tab" data-bs-target="#puestos" type="button" role="tab" aria-controls="puestos" aria-selected="false">Creditos</button>
                                        </li>

                                    </ul>
                                </div>

                                <div class="card-body tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="sanciones" role="tabpanel" aria-labelledby="sanciones-tab">
                                        <div class="row justify-content-center" id="divTabla">
                                            <div class="col-lg-10 text-center">

                                                <div style="padding: 10px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                                                    <div class="row mt-4">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/soldado.png" alt="Foto Oficial Subalterno">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Subalternos: 30%</h5>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/militar.png" alt="Foto Oficial Superior">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Superiores: 25% <br>Grado de Coronel: 20%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row justify-content-center">
                                                    <div class="col table-responsive" style="padding: 20px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 10px;">
                                                        <table id="tablademeritos" class="table table-bordered table-hover w-100 ">

                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="tab-pane fade" id="cursos" role="tabpanel" aria-labelledby="cursos-tab">
                                        <div class="row justify-content-center" id="divTabla">
                                            <div class="col-lg-10 text-center">

                                                <div style="padding: 10px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                                                    <div class="row mt-4">
                                                        <div class="col-md-6">

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/soldado.png" alt="Foto Oficial Subalterno">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Subalternos: 20%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/militar.png" alt="Foto Oficial Superior">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Superiores: 20% <br> Grado de Coronel: 15%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row justify-content-center">
                                                    <div class="col table-responsive" style="padding: 20px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 10px;">
                                                        <table id="tablacursoAscenso" class="table table-bordered table-hover w-100">
                                                            <tbody>
                                                                <!-- Contenido de la tabla -->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                    <div class="tab-pane fade" id="puestos" role="tabpanel" aria-labelledby="puestos-tab">
                                        <div class="row justify-content-center" id="divTabla">
                                            <div class="col-lg-10 text-center">
                                                <div style="padding: 10px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                                                    <div class="row mt-4">
                                                        <div class="col-md-6">

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/soldado.png" alt="Foto Oficial Subalterno">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Subalternos: 5%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/militar.png" alt="Foto Oficial Superior">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Superiores: 5% <br> Grado de Coronel: 15%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center">
                                                    <div class="col table-responsive" style="padding: 20px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 10px;">
                                                        <table id="tablameritos" class="table table-bordered table-hover w-100 ">

                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="pafes" role="tabpanel" aria-labelledby="pafes-tab">
                                        <div class="row justify-content-center" id="divTabla">
                                            <div class="col-lg-10 text-center">
                                                <div style="padding: 10px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                                                    <div class="row mt-4">
                                                        <div class="col-md-6">

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/soldado.png" alt="Foto Oficial Subalterno">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Subalternos: 20%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/militar.png" alt="Foto Oficial Superior">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Superiores: 15%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center">
                                                    <div class="col table-responsive" style="padding: 20px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 10px;">
                                                        <table id="tablapafes" class="table table-bordered table-hover w-100 ">

                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="perfil" role="tabpanel" aria-labelledby="perfil-tab">
                                        <div class="row justify-content-center" id="divTabla">
                                            <div class="col-lg-10 text-center">
                                                <div style="padding: 10px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                                                    <div class="row mt-4">
                                                        <div class="col-md-6">

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/soldado.png" alt="Foto Oficial Subalterno">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Subalternos: 5%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/militar.png" alt="Foto Oficial Superior">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Superiores: 5%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center">
                                                    <div class="col table-responsive" style="padding: 20px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 10px;">
                                                        <table id="tablaperfilBio" class="table table-bordered table-hover w-100 ">

                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="desempenio" role="tabpanel" aria-labelledby="desempenio-tab">
                                        <div class="row justify-content-center" id="divTabla">
                                            <div class="col-lg-10 text-center">
                                                <div style="padding: 10px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                                                    <div class="row mt-4">
                                                        <div class="col-md-6">

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/soldado.png" alt="Foto Oficial Subalterno">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Subalternos: 20%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <img id="det-imagen" src="./images/militar.png" alt="Foto Oficial Superior">
                                                                </div>
                                                                <div class="col-md-8">
                                                                    <h5 class="mt-2">Oficiales Superiores: 30%</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center">
                                                    <div class="col table-responsive" style="padding: 20px;  box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 10px;">
                                                        <table id="tabladesempenio" class="table table-bordered table-hover w-100 ">

                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="<?= asset('./build/js/promociones/index.js') ?>"></script>
</body>

</html>