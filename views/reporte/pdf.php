<style>
    .table-style {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid black;
    }

    .table-style th, .table-style td {
        border: 1px solid black;
    }
</style>

<link rel="stylesheet" type="text/css" href="ruta/a/estilos.css">

<table class="table-style">
    <thead>
        <tr>
             <th>NO.</th>
            <th>Catalogo</th>
            <th>Promoción</th>
            <th>Grado</th>
            <th>Nombre</th>
            <th>Último Ascenso</th>
            <th>Curso Ascenso</th>
            <th>Perfil Biofisico</th>
            <th>demeritos</th>
            <th>punteo</th>
            <th>Creditos</th>
            <th>puntos pafes</th>
            <th>puntos Eva_desempeño</th>
            <th>punteo Final</th>
        </tr>
    </thead>
    <tbody>
        <?php $contadorFilas = 1; ?>
        <?php foreach ($usuarios1 as $usuario) : ?>
            <tr>
                <td><?= $contadorFilas++ ?></td>
                <td><?= $usuario['per_catalogo'] ?></td>
                <td><?= $usuario['per_promocion'] ?></td>
                <td><?= $usuario['grado'] ?></td>
                <td><?= $usuario['nombre'] ?></td>
                <td><?= $usuario['t_ult_asc'] ?></td>
                <!-- Datos de curso Ascenso correspondientes a este usuario -->
                <td><?= $usuario['promedio_texto'] ?? '' ?></td>
                <!-- Datos de perfilBiofisico correspondientes a este usuario -->
                <td><?= $usuario['puntos_texto'] ?? '' ?></td>
                  <!-- Datos de perfilBiofisico correspondientes a este usuario -->
                <td><?= $usuario['demeritos'] ?? '' ?></td>
                <td><?= $usuario['punteo_demeritos'] ?? '' ?></td>
                    <!-- Datos de meritos correspondientes a este usuario -->
                <td><?= $usuario['puntos_netos'] ?? '' ?></td>
                    <!-- Datos de puntos pafe correspondientes a este usuario -->
                <td><?= $usuario['suma_total'] ?? '' ?></td>
                   <!-- Datos de puntos pafe correspondientes a este usuario -->
                <td><?= $usuario['resultado_fin'] ?? '' ?></td>

                <?php
                $punteoPromedio = (
                    ($usuario['promedio_texto'] == 'S/R' ? 0 : $usuario['promedio_texto']) +
                    ($usuario['puntos_texto'] == 'S/R' ? 0 : $usuario['puntos_texto']) +
                    ($usuario['punteo_demeritos'] ?? 30) +
                    ($usuario['puntos_netos'] ?? 0) +
                    ($usuario['suma_total'] == 'S/R' ? 0 : $usuario['suma_total']) +
                    ($usuario['resultado_fin'] == 'S/R' ? 0 : $usuario['resultado_fin'])
                );
                ?>
                <td><?= $punteoPromedio ?></td>
           
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
