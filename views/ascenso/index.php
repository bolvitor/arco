<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARCO</title>

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

        .custom-button {
            border: 3px solid #9E9EAB;
            border-radius: 8px;
            width: 80px;
            height: 60px;
            margin-top: 30px;
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
        <h2 class=" transparent-background">Informacion Militar</h2>
        <div id="cont-formulario" class="container p-4 shadow-lg style" , style=" border-collapse: collapse;" style="max-width: 80%; padding: 20px; background-color: #f8f9fa; box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
            <form class="form-container" id="formularioAscensos">
                <div class="row">
                    <!-- Primera Columna - Imagen -->
                    <div class="col-md-3">
                        <img src="./images/fondo.jpg" id="foto" alt="Fotografía" class="img-fluid">
                    </div>

                    <!-- Segunda Columna - Input y Select -->
                    <div class="col-md-6">
                        <!-- Primera Fila -->
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="exel-input" class="mt-2"><i class="bi bi-file-earmark-excel-fill"></i> Ingrese un archivo Excel:</label>
                                <input type="file" class="form-control" id="exel-input">
                            </div>
                        </div>

                        <!-- Segunda Fila -->
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="per_grado"><i class="bi bi-shield-shaded"></i> Ingrese el Grado Actual:</label>
                                <select name="per_grado" id="per_grado" class="form-control">
                                    <option value="">SELECCIONE...</option>
                                    <?php foreach ($grados as $grado) : ?>
                                        <option value="<?= $grado['codigo1'] ?>" data-codigo2="<?= $grado['codigo2'] ?>">
                                            <?= $grado['nombres_completos'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="fecha" class="mt-2"><i class="bi bi-calendar2-month-fill"></i> Fecha de Ascenso:</label>
                                <input type="date" class="form-control" id="fecha">
                            </div>
                        </div>
                    </div>

                    <!-- Tercera Columna - Botones -->
                    <div class="col-md-3">
                        <!-- Primera Fila -->
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <button type="button" id="btnBuscar" class="btn btn-outline-dark custom-button">
                                    <img src="./images/buscar.png" alt="Buscar">
                                </button>
                                <button type="reset" id="btnLimpiar" class="btn btn-outline-dark custom-button button-with-shadow">
                                    <img src="./images/actualizar.png" alt="Actualizar">
                                </button>
                            </div>
                        </div>

                        <!-- Segunda Fila -->
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <button type="button" id="btnPdf" class="btn btn-outline-dark custom-button button-with-shadow" style="display: none;">
                                    <img src="./images/pdf.png" alt="pdf">
                                </button>
                                <button type="button" id="btnexcel" class="btn btn-outline-dark custom-button button-with-shadow" style="display: none;">
                                    <img src="./images/excel.png" alt="excel">
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>








        <div class="row justify-content-center">
            <div class="col table-responsive" style="max-width: 90%; padding: 40px; background-color: #f8f9fa; box-shadow: 0 4px 8px rgba(0, 128, 255, 0.3), 0 6px 20px rgba(0, 0, 0, 0.1); margin-top: 20px;">
                <table id="tablaAscensos" class="container p-4 shadow-lg style, table table-bordered table-hover" style="width: 100%; border-collapse: collapse;">

                </table>
            </div>
        </div>


        <!-- <-- modal para mostrar la Informacion militar -->
        <div class="modal fade" id="verExistencias" name="verExistencias" tabindex="-1" role="dialog" aria-labelledby="verExistenciasLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verExistenciasLabel"></h5>
                        <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Cerrar</span>
                        </button>
                    </div>


                    <div class="container mt-4">
                        <h2>Información Militar</h2>

                        <form class="form-container" id="formularioAscenso">
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
                                                            <table id="tablatotaldemeritos" class="table table-bordered table-hover w-100">
                                                                <tbody>
                                                                    <tr>
                                                                        <td><br>Total Deméritos<b></td>
                                                                        
                                                                        <td id="td_demeritos"></td>
                                                                        <td><br>Punteo<br></td>
                                                                        <td id="td_punteo"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
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

        <script src="<?= asset('./build/js/ascenso/index.js') ?>"></script>
</body>

</html>