<style>
    .table-style {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid black;
    }

    .table-style th,
    .table-style td {
        border: 1px solid black;
    }
</style>


<table class="table-style">
    <thead>
        <tr>
            <th colspan="<?= count($periodosz) + count($periodos) + 15 ?>">Actualizacion de Record para los Oficiales en el grado de: <?= implode(", ", $gradoNombre) ?></th>
        </tr>

        <tr>
            <th>NO.</th>
            <th>Catalogo</th>
            <th>Promocion</th>
            <th>Grado</th>
            <th>Nombre</th>
            <th>Ultimo Ascenso</th>


            <th>Curso Ascenso</th>
            <th>perfilBio</th>
            <th>demeritos</th>
            <th>punteo</th>
            <th>Creditos</th>

            <?php foreach ($periodos as $periodo) : ?>
                <th>Eva-<?= $periodo ?></th>
            <?php endforeach; ?>

            <th>nota</th>

            <?php foreach ($periodosz as $periodoz) : ?>
                <th>P<?= $periodoz ?></th>
            <?php endforeach; ?>
            <th>pafe Ascenso</th>
            <th>nota</th>

            <th>Punteo Final</th>
        </tr>
    </thead>
    <tbody>
        <?php $contadorFilas = 1; ?>
        <?php foreach ($usuarios1 as $index => $usuario) : ?>
            <tr>
                <td><?= $contadorFilas++ ?></td>
                <td><?= $usuario['per_catalogo'] ?></td>
                <td><?= $usuario['per_promocion'] ?></td>
                <td><?= $usuario['grado'] ?></td>
                <td><?= $usuario['nombre'] ?></td>
                <td><?= $usuario['t_ult_asc'] ?></td>

                <!-- Datos de curso Ascenso correspondientes a este usuario -->
                <td><?= $curso1[$index]['promedio_texto'] ?? 'S/R' ?></td>

                <!-- Datos de perfilBiofisico correspondientes a este usuario -->
                <td><?= $perfilBio1[$index]['puntos_texto'] ?? 'S/R' ?></td>

                <!-- Datos de conducta correspondientes a este usuario -->
                <td><?= $conducta1[$index]['demeritos'] ?? 0 ?></td>
                <td><?= $conducta1[$index]['punteo_demeritos'] ?? 30 ?></td>

                <!-- Datos de meritos correspondientes a este usuario -->
                <td><?= $meritos1[$index]['puntos_netos'] ?? 0 ?></td>



                <!-- Datos de notas pafe correspondientes a este usuario -->
                <?php $catalogo = $usuario['per_catalogo']; ?>
                <?php foreach ($evaDesempenio1[$catalogo] ?? [] as $nota) : ?>
                    <td><?= $nota ?? 'S/R' ?></td>
                <?php endforeach; ?>

                <!-- Datos de puntos Eva_desempeño correspondientes a este usuario -->
                <td><?= $desempenio1[$index]['resultado_fin'] ?? 'S/R' ?></td>

                <?php $catalogo = $usuario['per_catalogo']; ?>
                <?php foreach ($notasz1[$catalogo] ?? [] as $notaz) : ?>
                    <td><?= $notaz !== '' ? $notaz : 'S/R' ?></td>
                <?php endforeach; ?>


                <td><?= $notaA1[$index]['promedios'] ?? 'S/R' ?></td>

                <!-- Datos de puntos pafe correspondientes a este usuario -->
                <td><?= $pafeSQL1[$index]['suma_total'] ?? 'S/R' ?></td>

                <?php
                $punteoPromedio = (
                    ($curso1[$index]['promedio_texto'] == 'S/R' ? 0 : $curso1[$index]['promedio_texto']) +
                    ($perfilBio1[$index]['puntos_texto'] == 'S/R' ? 0 : $perfilBio1[$index]['puntos_texto']) +
                    ($conducta1[$index]['punteo_demeritos'] ?? 30) +
                    ($meritos1[$index]['puntos_netos'] ?? 0) +
                    ($pafeSQL1[$index]['suma_total'] == 'S/R' ? 0 : $pafeSQL1[$index]['suma_total']) +
                    ($desempenio1[$index]['resultado_fin'] == 'S/R' ? 0 : $desempenio1[$index]['resultado_fin'])
                );
                ?>
                <td><?= $punteoPromedio ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>