<?php

namespace Controllers;

use Exception;
use Model\Usuario;
use MVC\Router;


class EstadisticaController {

    public static function index(Router $router)
    {
        $router->render('estadisticas/index', []);
    }


public static function graficas()
{

    $promocionOficial = $_GET['promocion'];

  
    $sql = " SELECT 
        CASE
            WHEN per_grado IN (41, 40) THEN 'Subteniente/Alferez de Fragata'
            WHEN per_grado IN (47, 46) THEN 'Teniente/Alferez de Navio'
            WHEN per_grado IN (59, 60) THEN 'Cap.2do./Tte. de Fragata'
            WHEN per_grado IN (65, 66) THEN 'Cap.1ro./Tte. de Navio'
            WHEN per_grado IN (73, 74) THEN 'Mayor/Cap. de Corbeta'
            WHEN per_grado IN (82, 81) THEN 'Tte.Coronel/Capitan de Fragata'
            WHEN per_grado IN (89, 88) THEN 'Coronel/Capitan de Navio'
            ELSE CAST(per_grado AS VARCHAR)
        END AS nombre,
        COUNT(*) AS total,
        SUM(CASE WHEN current not between t_ult_asc and to_date(t_prox_asc, '%d/%m/%Y') THEN 1 ELSE 0 END) AS postergados,
        SUM(CASE WHEN current between t_ult_asc and to_date(t_prox_asc, '%d/%m/%Y') THEN 1 ELSE 0 END) AS ascendidos
        FROM mper 
        INNER JOIN tiempos ON t_catalogo = per_catalogo 
        INNER JOIN grados ON mper.per_grado = grados.gra_codigo
        WHERE per_promocion = '$promocionOficial'
        AND per_situacion = 11
        AND gra_clase = 1
        GROUP BY nombre ";

    try {
        $resultados = Usuario::fetchArray($sql);
        echo json_encode($resultados);
    } catch (Exception $e) {
        echo json_encode([
            'detalle' => $e->getMessage(),
            'mensaje' => 'Ocurrió un error',
            'codigo' => 0
        ]);
    }
}



public static function graficas2()
{

    $promocionOficial = $_GET['promocion'];

    $sql = " SELECT 
        CASE
            WHEN per_grado IN (41, 40) THEN 'Subteniente/Alferez de Fragata'
            WHEN per_grado IN (47, 46) THEN 'Teniente/Alferez de Navio'
            WHEN per_grado IN (59, 60) THEN 'Cap.2do./Tte. de Fragata'
            WHEN per_grado IN (65, 66) THEN 'Cap.1ro./Tte. de Navio'
            WHEN per_grado IN (73, 74) THEN 'Mayor/Cap. de Corbeta'
            WHEN per_grado IN (82, 81) THEN 'Tte.Coronel/Capitan de Fragata'
            WHEN per_grado IN (89, 88) THEN 'Coronel/Capitan de Navio'
            ELSE CAST(per_grado AS VARCHAR)
        END AS nombre,
        COUNT(*) AS total,
        SUM(CASE WHEN current not between t_ult_asc and to_date(t_prox_asc, '%d/%m/%Y') THEN 1 ELSE 0 END) AS postergados,
        SUM(CASE WHEN current between t_ult_asc and to_date(t_prox_asc, '%d/%m/%Y') THEN 1 ELSE 0 END) AS ascendidos
    FROM mper 
    INNER JOIN tiempos ON t_catalogo = per_catalogo 
    INNER JOIN grados ON mper.per_grado = grados.gra_codigo
    WHERE per_promocion = '$promocionOficial'
    AND per_situacion = 11
    AND gra_clase = 1
    GROUP BY nombre ";

    try {
        $resultados = Usuario::fetchArray($sql);
        echo json_encode($resultados);
    } catch (Exception $e) {
        echo json_encode([
            'detalle' => $e->getMessage(),
            'mensaje' => 'Ocurrió un error',
            'codigo' => 0
        ]);
    }
}
};