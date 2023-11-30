<?php

namespace Controllers;

use Mpdf\Mpdf;
use Exception;
use Model\Usuario;
use Model\Grado;
use Model\Arma;
use MVC\Router;

class AscensoController
{

    public static function index(Router $router)
    {
        $grados = static::buscarGrado();
        $armas = static::buscarArma();
        $router->render('ascenso/index', [
            'grados' => $grados,
            'armas' => $armas,
        ]);
    }


    public static function buscarGrado()
    {
        //mostrar dos grados concatenados en el select//
        $sql = "SELECT g1.gra_codigo AS codigo1, g2.gra_codigo AS codigo2, 
                TRIM('/' FROM TRIM(g1.gra_desc_lg) || ' / ' || TRIM(g2.gra_desc_lg)) AS nombres_completos
                FROM grados g1
                JOIN grados g2 ON (g1.gra_codigo = 41 AND g2.gra_codigo = 40)
                OR (g1.gra_codigo = 47 AND g2.gra_codigo = 46)
                OR (g1.gra_codigo = 60 AND g2.gra_codigo = 59)
                OR (g1.gra_codigo = 66 AND g2.gra_codigo = 65)
                OR (g1.gra_codigo = 74 AND g2.gra_codigo = 73)
                OR (g1.gra_codigo = 82 AND g2.gra_codigo = 81)
                OR (g1.gra_codigo = 89 AND g2.gra_codigo = 88)
                WHERE g1.gra_clase = 1 AND g2.gra_clase = 1 ";


        try {
            $grados = Grado::fetchArray($sql);
            return $grados;
        } catch (Exception $e) {
            return [];
        }
    }


    public static function buscarArma()
    {
        $sql = "SELECT arm_desc_md, arm_codigo FROM armas";

        try {
            $armas = Arma::fetchArray($sql);
            return $armas;
        } catch (Exception $e) {
            return [];
        }
    }


    public static function buscarAPI()
    {

        $catalogoOficial = json_decode($_GET['catalogos']);

        $grado = $_GET['grado'];
        $grado2 = $_GET['grado2'];

        $fecha = $_GET['fecha'];


        if ($grado == 41 || $grado == 47) {
            $fechaBase = date('Y-m-d', strtotime($fecha . ' -5 years'));
        } elseif ($grado == 60 || $grado == 59) {
            $fechaBase = date('Y-m-d', strtotime($fecha . ' -4 years'));
        } elseif ($grado == 66 || $grado == 65) {
            $fechaBase = date('Y-m-d', strtotime($fecha . ' -3 years'));
        } elseif ($grado == 74 || $grado == 73) {
            $fechaBase = date('Y-m-d', strtotime($fecha . ' -3 years'));
        } elseif ($grado == 89 || $grado == 88) {
            $fechaBase = "SELECT t_ult_asc FROM tiempos WHERE t_catalogo = '$catalogoOficial'";
        }

        $partesFecha = explode("-", $fechaBase);


        // Obtiene el año y el mes
        $anioBase = $partesFecha[0];
        $mesBase = $partesFecha[1];



        $usuarios1 = [];

        // Recorrer cada catálogo con el foreach//
        foreach ($catalogoOficial as $catalogoOficiales) {

            //Obtener los datos de un oficial//    
            $sql = "SELECT 
            per_catalogo,
            per_promocion,
            per_grado,
            trim(gra_desc_ct) || ' DE ' || trim(arm_desc_md) as grado,
            trim(per_ape1) || ' ' || trim(per_ape2) || ', ' || trim(per_nom1)  || ', ' || trim(per_nom2) as nombre,
            per_arma,
            t_prox_asc,
            t_ult_asc
            FROM  mper
            INNER JOIN grados ON per_grado = gra_codigo
            INNER JOIN armas ON per_arma = arm_codigo
            INNER JOIN tiempos ON per_catalogo = t_catalogo
            WHERE gra_clase = 1 
            AND per_catalogo = '$catalogoOficiales'
            AND per_situacion = 11
            ORDER BY per_catalogo ASC";


            $usuarios = Usuario::fetchArray($sql);


            if (!empty($usuarios)) {
                $usuarios1[] = $usuarios[0];
            }
        }


        // Obtener los meritos de un oficial//
        $meritos1 = [];
        foreach ($catalogoOficial as $catalogoOficiales) {


            $meritos = "SELECT
            per_catalogo,
            CASE WHEN SUM(total_meritos) IS NULL THEN 0 ELSE SUM(total_meritos) END AS total_meritos,
            (CASE WHEN SUM(total_meritos) IS NULL THEN 0 ELSE SUM(total_meritos) END / 100 * 5) AS puntos_netos
            FROM mper
            LEFT JOIN (   
            SELECT
                cur_catalogo AS catalogo,
                SUM(
                    CASE
                        WHEN cursos.cur_equi = 2064 THEN 5
                        WHEN cursos.cur_equi IN (25, 2107) THEN 3
                        WHEN cursos.cur_equi IN (775, 772, 905, 157, 1018, 1064, 21, 721, 675, 378) THEN 2
                        WHEN cursos.cur_equi = 2826 THEN 1
                        WHEN LOWER(cur_desc_lg) LIKE '%taller%' OR LOWER(cur_desc_lg) LIKE '%seminario%' THEN 1
                        WHEN LOWER(cur_desc_lg) LIKE '%diplomado%' THEN 2
                        WHEN LOWER(cur_desc_lg) LIKE '%doctor%' THEN 10
                        WHEN LOWER(cur_desc_lg) LIKE '%licenciado%' OR LOWER(cur_desc_lg) LIKE '%licenciatura%'
                        OR LOWER(cur_desc_lg) LIKE '%maestria%' THEN 4
                        WHEN LOWER(cur_desc_lg) LIKE '%tecnico_universitario%' THEN 2
                        WHEN LOWER(cur_desc_lg) LIKE '%idioma%' OR LOWER(cur_desc_lg) LIKE '%ingles%' 
                        OR LOWER(cur_desc_lg) LIKE '%frances%' THEN 5
                        ELSE 0
                    END
                ) AS total_meritos
            FROM dcur
            INNER JOIN cursos ON cur_curso = cur_codigo
            WHERE cur_grado = '$grado'
            GROUP BY cur_catalogo
            UNION ALL
            SELECT
            con_catalogo AS catalogo,
                SUM(
                    CASE
                        WHEN con_condecoracion IN (21293, 21487) THEN 3
                        WHEN LOWER(con_desc_lg) LIKE '%cruz de%' THEN 2
                        WHEN LOWER(con_desc_lg) LIKE '%medalla%' THEN 1
                        WHEN LOWER(con_desc_lg) LIKE '%citacion%' THEN 1
                        WHEN LOWER(con_desc_lg) LIKE '%alas%' OR LOWER(con_desc_lg) LIKE '%instructor%' THEN 0.5
                        WHEN LOWER(con_desc_lg) LIKE '%placa de combatiente%' THEN 0.5
                        WHEN LOWER(con_desc_lg) LIKE '%roble de oro%' THEN 0.5
                        WHEN LOWER(con_desc_lg) LIKE '%distintivo%' THEN 1
                        ELSE 0
                    END
                ) AS total_meritos
            FROM dcon
            INNER JOIN cond ON con_condecoracion = con_codigo
            WHERE (con_grado = '$grado' OR con_grado = '$grado2')
            GROUP BY con_catalogo
            ) AS subquery
            ON per_catalogo = subquery.catalogo
            WHERE (per_grado = '$grado' OR per_grado = '$grado2')
            AND per_catalogo = '$catalogoOficiales'
            GROUP BY per_catalogo ";


            $meritos = Usuario::fetchArray($meritos);

            if (!empty($meritos)) {
                $meritos1[] = $meritos[0];
            }
        }


        // Obtener el punto del curso de ascenso//
        $cursoAscenso1 = [];
        foreach ($catalogoOficial as $catalogoOficiales) {

            $cursoAscenso = "SELECT
            per_catalogo,
            promedio
            FROM mper
            LEFT JOIN (
            SELECT 
                cur_catalogo,
                CASE
                    WHEN cur_curso = 3200 THEN cur_punteo * 20 / 100
                    WHEN cur_curso IN (750, 751, 752, 753, 754, 755, 756, 777, 778, 781, 1020, 1099) THEN cur_punteo * 20 / 100
                    WHEN cur_curso = 3201 THEN cur_punteo * 20 / 100
                    WHEN cur_curso = 784 THEN cur_punteo * 20 / 100
                    WHEN cur_curso = 710 THEN cur_punteo * 20 / 100
                    WHEN cur_curso = 43 THEN cur_punteo * 15 / 100
                    ELSE NULL
                END AS promedio
            FROM dcur
            INNER JOIN cursos ON cur_curso = cursos.cur_codigo
            WHERE cur_curso IN (3200, 750, 751, 752, 753, 754, 755, 756, 777, 778, 781, 783, 1020, 1099, 3201, 784, 710, 43)
            AND (cur_grado = '$grado' OR cur_grado = '$grado2')
            AND cur_fec_fin = (SELECT MAX(cur_fec_fin) FROM dcur WHERE cur_catalogo = '$catalogoOficiales')
            ) ON per_catalogo = cur_catalogo
            INNER JOIN grados ON per_grado = gra_codigo
            WHERE gra_clase = 1 
            AND per_situacion = 11
            AND (per_grado = '$grado' OR per_grado = '$grado2')
            AND per_catalogo = '$catalogoOficiales'";

            $cursoAscenso = Usuario::fetchArray($cursoAscenso);


            if (!empty($cursoAscenso)) {
                $cursoAscenso1[] = $cursoAscenso[0];
            }
        }



        //Obtener nota del perfil biofisico de ascenso//
        $perfilBio1 = [];
        foreach ($catalogoOficial as $catalogoOficiales) {

            $perfilBio = "SELECT per_catalogo, per_grado, perfil_biofisico, e_resultado, e_fecha
            FROM mper
            LEFT JOIN (
            SELECT e_catalogo, e_resultado, e_puntuacion as perfil_biofisico, e_grado, e_fecha
            FROM evaluaciones
            WHERE e_numero = 1
            AND e_catalogo = '$catalogoOficiales'
            AND (e_grado = '$grado' OR e_grado = '$grado2')
            AND e_fecha = (SELECT MAX(e_fecha) FROM evaluaciones WHERE e_numero = 1 AND e_catalogo = '$catalogoOficiales' AND (e_grado = '$grado' OR e_grado = '$grado2'))
            )ON per_catalogo = e_catalogo
            INNER JOIN grados ON per_grado = gra_codigo
            WHERE per_catalogo = '$catalogoOficiales'
            AND per_situacion  = 11
            AND gra_clase = 1
            AND (per_grado = '$grado' OR per_grado = '$grado2') ";


            $perfilBio = Usuario::fetchArray($perfilBio);

            if (!empty($perfilBio)) {
                $perfilBio1[] = $perfilBio[0];
            }
        }



        //Obtener los demeritos y punteo del Oficial//
        $demeritos1 = [];
        foreach ($catalogoOficial as $catalogoOficiales) {

            $demeritos = "SELECT
            per_catalogo,
            per_grado,
            CASE
                    WHEN demeritos IS NOT NULL THEN demeritos
                    ELSE 0
                END AS demeritos,
            est_catalogo,
            CASE
                WHEN demeritos IS NOT NULL THEN
                    CASE
                        WHEN per_grado IN (41, 40, 47, 46, 60, 59, 65, 66) THEN ROUND((30 - (demeritos * 30 / 150)), 2)
                        WHEN per_grado IN (74, 73, 82, 81) THEN ROUND((25 - (demeritos * 25 / 100)), 2)
                        WHEN per_grado IN (89, 88) THEN ROUND((20 - (demeritos * 20 / 100)), 2)
                    END
                ELSE
                    CASE
                        WHEN per_grado IN (41, 40, 47, 46, 60, 59, 65, 66) THEN 30
                        WHEN per_grado IN (74, 73, 82, 81) THEN 25
                        WHEN per_grado IN (89, 88) THEN 20
                    END
            END AS punteo_demeritos
            FROM
            mper
            LEFT JOIN (
            SELECT
                est_catalogo,
                est_demeritos AS demeritos
            FROM psan_estadistica
            WHERE (est_grado = '$grado' OR est_grado = '$grado2')
            ) ON per_catalogo = est_catalogo
            INNER JOIN grados ON per_grado = gra_codigo
            WHERE gra_clase = 1
            AND per_catalogo = '$catalogoOficiales'
            AND per_situacion = 11
            AND (per_grado = '$grado' OR per_grado = '$grado2') ";


            $demeritos = Usuario::fetchArray($demeritos);


            if (!empty($demeritos)) {
                $demeritos1[] = $demeritos[0];
            }
        }



        //Obtener la nota promedio de pafes del oficial//

        $pafeSQL1 = [];
        foreach ($catalogoOficial as $catalogoOficiales) {

            //Cantidad de periodos a generar por grado
            $gradosPeriodos = [
                41 => 10, 40 => 10, 47 => 10, 46 => 10, 60 => 6, 59 => 6,
                65 => 6,  66 => 6,  74 => 8,  73 => 8,  82 => 8, 81 => 8, 89 => 10, 88 => 10
            ];


            $periodosGeneradosz = [];
            $periodosEnAnioBase = 2; // Siempre dos períodos por año

            if ($mesBase >= 5 && $mesBase <= 8) {
                // Si el mes base está entre mayo y agosto, inicia desde el segundo período
                $periodoBase = 2;
            } else {
                // De lo contrario, inicia desde el primer período
                $periodoBase = 1;
            }

            for ($i = 0; $i < $gradosPeriodos[$grado]; $i++) {
                $anio = $anioBase + floor(($i + $periodoBase - 1) / $periodosEnAnioBase);
                $mes = $mesBase + ($i + $periodoBase - 1) * 6; // Avanza 6 meses por período
                if ($mes > 12) {

                    $mes -= 12;
                }
                $periodo = (($i + $periodoBase - 1) % $periodosEnAnioBase + 1) . '-' . $anio;
                $periodosGeneradosz[$grado][] = $periodo;
            }

            // punteo de las notas  de pafes de zona por grado//
            $punteosZ = [
                41 => 8, 40 => 8, 47 => 8, 46 => 8, 60 => 8, 59 => 8, 65 => 8,
                66 => 8, 74 => 6, 73 => 6, 82 => 6, 81 => 6, 89 => 6, 88 => 6,
            ];

            // punteo de la pafe de ascenso por grado//
            $punteosA = [
                41 => 12, 40 => 12, 47 => 12, 46 => 12, 60 => 12, 59 => 12, 65 => 12,
                66 => 12, 74 => 9,  73 => 9,  82 => 9,  81 => 9,  89 => 9,  88 => 9,
            ];

            //punteo promedio por grado//
            $punteopromedio = [
                41 => 20, 40 => 20, 47 => 20, 46 => 20, 60 => 20, 59 => 20, 65 => 20,
                66 => 20, 74 => 15,  73 => 15,  82 => 15,  81 => 15,  89 => 15,  88 => 15,
            ];


            // Definir la consulta SQL
            $pafeSQL = "SELECT
            per_catalogo,
            per_grado,
            total_notasA,
            cantidad_notas_z,
            NOTAS_Z,
            CASE
            WHEN NOTAS_Z = 0 THEN 0 
            ELSE ROUND(LEAST(total_notasA + (NOTAS_Z / NULLIF({$gradosPeriodos[$grado]}, 0)) * {$punteosZ[$grado]} / 100, {$punteopromedio[$grado]}),2)
            END AS  suma_total
            
            FROM mper
            LEFT JOIN (
            SELECT not_catalogo, ";


            foreach ($periodosGeneradosz[$grado] as $periodospafe) {

                $pafeSQL .= "
                NVL(
                    MAX(CASE WHEN opaf_notas.not_periodo LIKE '%$periodospafe %' AND (opaf_notas.not_tipo = 'Z') THEN opaf_notas.NOT_PROMEDIO ELSE 0 END), 0
                ) + ";
            }

            $pafeSQL = rtrim($pafeSQL, ' + ');

            $pafeSQL .= "
                AS NOTAS_Z,
                LEAST(
                MAX(CASE WHEN (opaf_notas.not_tipo = 'A') THEN opaf_notas.NOT_PROMEDIO ELSE 0 END) * {$punteosA[$grado]} / 100, {$punteosA[$grado]}) AS total_notasA,
                    
                COUNT(CASE WHEN opaf_notas.not_tipo = 'Z' THEN 1 END) AS cantidad_notas_z
                FROM opaf_notas
                WHERE (not_grado = '$grado' OR not_grado = '$grado2')
                GROUP BY not_catalogo
                ) ON per_catalogo = not_catalogo
                INNER JOIN grados ON per_grado = gra_codigo
                WHERE (per_grado = '$grado' OR per_grado = '$grado2')
                AND gra_clase = 1 
                AND per_catalogo = '$catalogoOficiales'
                AND per_situacion = 11 ";


            $pafeSQL = Usuario::fetchArray($pafeSQL);

            if (!empty($pafeSQL)) {
                $pafeSQL1[] = $pafeSQL[0];
            }
        }



        //Obtener nota promedio de las evaluaciones de desempeño//
        foreach ($catalogoOficial as $catalogoOficiales) {



            //cantidad de periodos de las evaluaciones de desempeño por grado//
            $gradosPeriodos = [
                41 => 9, 40 => 9, 47 => 9, 46 => 9, 60 => 5, 59 => 5,
                65 => 5,  66 => 5,  74 => 7,  73 => 7,  82 => 7, 81 => 7, 89 => 9, 88 => 9
            ];


            $periodosGenerados = [];

            $periodosEnAnioBase = 2; // Siempre dos períodos por año

            if ($mesBase >= 5 && $mesBase <= 8) {
                // Si el mes base está entre mayo y agosto, inicia desde el segundo período
                $periodoBase = 2;
            } else {
                // De lo contrario, inicia desde el primer período
                $periodoBase = 1;
            }

            for ($i = 0; $i < $gradosPeriodos[$grado]; $i++) {
                $anio = $anioBase + floor(($i + $periodoBase - 1) / $periodosEnAnioBase);
                $mes = $mesBase + ($i + $periodoBase - 1) * 6; // Avanza 6 meses por período
                if ($mes > 12) {

                    $mes -= 12;
                }
                $periodo = (($i + $periodoBase - 1) % $periodosEnAnioBase + 1) . ' - ' . $anio;
                $periodosGenerados[$grado][] = $periodo;
            }


            // array asociativo que contiene los punteos para cada grado//
            $promedios = [
                41 => 20, 40 => 20, 47 => 20, 46 => 20, 60 => 20, 59 => 20, 65 => 20,
                66 => 20, 74 => 30, 73 => 30, 82 => 30, 81 => 30, 89 => 30, 88 => 30
            ];

            $desempenio = " SELECT
            per_catalogo, per_grado, resultado_final
            FROM mper
            LEFT JOIN(
            SELECT
            sub.eva_cat1,
            CASE
                WHEN total_notas = 0 THEN 0 
                ELSE ROUND(total_notas / notas_r1, 2) 
            END AS  promedio_total_notas,
            r1.notas_r1 AS recuento_eva_renglon1,
            r1.notas_r2 AS recuento_eva_renglon2,
            r1.notas_r3 AS recuento_eva_renglon3,
            CASE
                WHEN r1.notas_r1 = 0 THEN 0
                ELSE ROUND((total_notas / notas_r1 * {$promedios[$grado]} / ({$gradosPeriodos[$grado]} - (r1.notas_r2 + r1.notas_r3)) * r1.notas_r1) / 100, 2)
            END AS resultado_final
            FROM (
                SELECT
                    eva_cat1,
                    (
            ";

            // Generar las partes dinámicas de la consulta según los periodos
            $periodosGenerados[$grado] = array_values($periodosGenerados[$grado]);

            $desempenio .= implode(" +\n", array_map(function ($periodo) {
                return "(SUM(CASE WHEN eva_periodo LIKE '%$periodo%' AND eva_renglon = 1 THEN eva_notas.not_nota ELSE 0 END) / 3)";
            }, $periodosGenerados[$grado]));


            $desempenio .= "
            )AS total_notas
            FROM eva_evaluacion
            INNER JOIN eva_notas ON not_evaluacion = eva_id
            WHERE eva_situacion <> 9
            AND (eva_grado1 = '$grado' OR eva_grado1 = '$grado2')
            AND eva_cat1 = '$catalogoOficiales'
            GROUP BY eva_cat1
            ) AS sub
            LEFT JOIN (
                SELECT
                    eva_cat1,
                    SUM(CASE WHEN eva_renglon = 1 THEN 1 ELSE 0 END) AS notas_r1,
                    SUM(CASE WHEN eva_renglon = 2 THEN 1 ELSE 0 END) AS notas_r2,
                    SUM(CASE WHEN eva_renglon = 3 THEN 1 ELSE 0 END) AS notas_r3
                FROM eva_evaluacion
                WHERE eva_situacion <> 9
                AND (eva_grado1 = '$grado' OR eva_grado1 = '$grado2')
                AND eva_cat1 = '$catalogoOficiales'
                AND (eva_periodo LIKE '%" . implode("%' OR eva_periodo LIKE '%", $periodosGenerados[$grado]) . "%')
                GROUP BY eva_cat1
            ) AS r1
            ON sub.eva_cat1 = r1.eva_cat1
            ) ON per_catalogo = eva_cat1
            WHERE (per_grado = '$grado' OR per_grado = '$grado2')
            AND per_situacion = 11
            AND per_catalogo = '$catalogoOficiales'";

            $desempenio = Usuario::fetchArray($desempenio);

            if (!empty($desempenio)) {
                $desempenio1[] = $desempenio[0];
            }
        }

        try {

            $responseData = [
                'usuarios1' => $usuarios1,
                'perfilBio1' => $perfilBio1,
                'cursoAscenso1' => $cursoAscenso1,
                'meritos1' => $meritos1,
                'demeritos1' => $demeritos1,
                'pafeSQL1' => $pafeSQL1,
                'desempenio1' => $desempenio1,
            ];

            echo json_encode($responseData);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'detalle' => $e->getMessage(),
                'mensaje' => 'Ocurrió un error',
                'codigo' => 0
            ]);
        }
    }





    //Funcion para buscar los detalles de los campos a evaluar para el ascenso del oficial//
    public static function buscarOficial()

    {

        $catalogoOficial = $_GET['per_catalogo'];

        $ObtenerGrado = "SELECT per_grado FROM mper WHERE per_catalogo = '$catalogoOficial'";
        $grado = Usuario::fetchFirst($ObtenerGrado);

        $grado = $grado['per_grado'];


        $fecha = $_GET['fecha'];

        if ($grado == 41 || $grado == 47) {
            $fechaBase = date('Y-m-d', strtotime($fecha . ' -5 years'));
        } elseif ($grado == 60 || $grado == 59) {
            $fechaBase = date('Y-m-d', strtotime($fecha . ' -4 years'));
        } elseif ($grado == 66 || $grado == 65) {
            $fechaBase = date('Y-m-d', strtotime($fecha . ' -3 years'));
        } elseif ($grado == 74 || $grado == 73) {
            $fechaBase = date('Y-m-d', strtotime($fecha . ' -3 years'));
        } elseif ($grado == 89 || $grado == 88) {
            $fechaBase = "SELECT t_ult_asc FROM tiempos WHERE t_catalogo = '$catalogoOficial'";
        }

        $partesFecha = explode("-", $fechaBase);


        // Obtiene el año y el mes
        $anioBase = $partesFecha[0];
        $mesBase = $partesFecha[1];


        //Obtener total demeritos y punteo promedio//
        $sqlconducta = "SELECT
        per_catalogo,
        per_grado,
        CASE
                WHEN demeritos IS NOT NULL THEN demeritos
                ELSE 0
            END AS demeritos,
        est_catalogo,
        CASE
            WHEN demeritos IS NOT NULL THEN
                CASE
                    WHEN per_grado IN (41, 40, 47, 46, 60, 59, 65, 66) THEN ROUND((30 - (demeritos * 30 / 150)), 2)
                    WHEN per_grado IN (74, 73, 82, 81) THEN ROUND((25 - (demeritos * 25 / 100)), 2)
                    WHEN per_grado IN (89, 88) THEN ROUND((20 - (demeritos * 20 / 100)), 2)
                END
            ELSE
                CASE
                    WHEN per_grado IN (41, 40, 47, 46, 60, 59, 65, 66) THEN 30
                    WHEN per_grado IN (74, 73, 82, 81) THEN 25
                    WHEN per_grado IN (89, 88) THEN 20
                END
        END AS punteo
        FROM mper
        LEFT JOIN (
            SELECT
                est_catalogo,
                est_demeritos AS demeritos
            FROM
                psan_estadistica
                WHERE est_grado = '$grado'
            ) ON per_catalogo = est_catalogo
        INNER JOIN grados ON per_grado = gra_codigo
        WHERE gra_clase = 1
        AND per_catalogo = '$catalogoOficial'
        AND per_situacion = 11
        AND per_grado = '$grado'";


        $conducta = Usuario::fetchArray($sqlconducta);




        $sql1 = " SELECT det_catalogo, gra_desc_md AS grado, det_fecha as fecha, san_descripcion as descripcion,
        CASE
            WHEN san_tipo = 'H' THEN 'HORAS'
            WHEN san_tipo = 'D' THEN 'DEMERITOS'
            ELSE san_tipo
        END AS tipo,
        san_cantidad as cantidad
        FROM psan_detalle
        INNER JOIN grados ON gra_codigo = det_grado
        INNER JOIN psan_sanciones ON san_codigo = det_sancion       
        WHERE det_catalogo =  '$catalogoOficial'
        AND det_grado = '$grado'
        AND det_status = 0
        ORDER BY det_fecha ASC";

        $demeritos = Usuario::fetchArray($sql1);


        $sql2 = "  SELECT 
        cur_catalogo,
        cur_desc_lg AS descripcion,
        cur_punteo,
        cur_fec_fin,
        CASE
            WHEN cur_curso = 3200 THEN cur_punteo * 20 / 100
            WHEN cur_curso IN (750, 751, 752, 753, 754, 755, 756, 777, 778, 781, 1020, 1099) THEN cur_punteo * 20 / 100
            WHEN cur_curso = 3201 THEN cur_punteo * 20 / 100
            WHEN cur_curso = 784 THEN cur_punteo * 20 / 100
            WHEN cur_curso = 710 THEN cur_punteo * 20 / 100
            WHEN cur_curso = 43 THEN cur_punteo * 15 / 100
            ELSE NULL
        END AS promedio
        FROM dcur
        INNER JOIN cursos ON cur_curso = cursos.cur_codigo
        WHERE cur_curso IN (3200, 750, 751, 752, 753, 754, 755, 756, 777, 778, 781, 783, 1020, 1099, 3201, 784, 710, 43)
        AND cur_grado = '$grado'
        AND cur_catalogo = '$catalogoOficial'
        AND cur_fec_fin = (SELECT MAX(cur_fec_fin) FROM dcur WHERE cur_catalogo = '$catalogoOficial')";

        $cursoAscenso = Usuario::fetchArray($sql2);
      


        $sql3 = "SELECT
        'CURSO' AS tipo,
        cur_desc_lg AS descripcion,
        dcur.cur_catalogo AS catalogo,
        SUM(
            CASE
                WHEN dcur.cur_equi = 2064 THEN 5
                WHEN dcur.cur_equi IN (25, 2107) THEN 3
                WHEN dcur.cur_equi IN (775, 772, 905, 157, 1018, 1064, 21, 721, 675, 378) THEN 2
                WHEN dcur.cur_equi = 2826 THEN 1
                WHEN LOWER(cursos.cur_desc_lg) LIKE '%taller%' OR LOWER(cursos.cur_desc_lg) LIKE '%seminario%' THEN 1
                WHEN LOWER(cursos.cur_desc_lg) LIKE '%diplomado%' THEN 2
                WHEN LOWER(cursos.cur_desc_lg) LIKE '%doctor%' THEN 10
                WHEN LOWER(cursos.cur_desc_lg) LIKE '%licenciado%' OR LOWER(cursos.cur_desc_lg) LIKE '%licenciatura%'
                    OR LOWER(cursos.cur_desc_lg) LIKE '%maestria%' THEN 4
                WHEN LOWER(cursos.cur_desc_lg) LIKE '%tecnico_universitario%' THEN 2
                WHEN LOWER(cursos.cur_desc_lg) LIKE '%idioma%' OR LOWER(cursos.cur_desc_lg) LIKE '%ingles%' 
                    OR LOWER(cursos.cur_desc_lg) LIKE '%frances%' THEN 5
                ELSE 0
            END
        ) AS meritos
        FROM dcur
        LEFT JOIN cursos ON dcur.cur_equi = cursos.cur_codigo
        WHERE cur_grado = '$grado'
        AND cur_catalogo = '$catalogoOficial'
        GROUP BY dcur.cur_catalogo, cur_desc_lg
        
        UNION ALL
        
        SELECT
        'CONDECORACION' AS tipo,
        con_desc_lg AS descripcion,
        dcon.con_catalogo AS catalogo,
        SUM(
            CASE
                WHEN dcon.con_condecoracion IN (21293, 21487) THEN 3
                WHEN LOWER(cond.con_desc_lg) LIKE '%cruz de%' THEN 2
                WHEN LOWER(cond.con_desc_lg) LIKE '%medalla%' OR LOWER(cond.con_desc_lg) LIKE '%med%' THEN 1
                WHEN LOWER(cond.con_desc_lg) LIKE '%citacion%' THEN 1
                WHEN LOWER(cond.con_desc_lg) LIKE '%alas%' OR LOWER(cond.con_desc_lg) LIKE '%instructor%' THEN 0.5
                WHEN LOWER(cond.con_desc_lg) LIKE '%placa de combatiente%' THEN 0.5
                WHEN LOWER(cond.con_desc_lg) LIKE '%roble de oro%' THEN 0.5
                WHEN LOWER(cond.con_desc_lg) LIKE '%distintivo%' THEN 0.5
                ELSE 0
            END
        ) AS meritos
        FROM dcon
        LEFT JOIN cond ON dcon.con_condecoracion = cond.con_codigo
        WHERE con_grado = '$grado'
        AND con_catalogo = '$catalogoOficial'
        GROUP BY dcon.con_catalogo, con_desc_lg ";

$meritos = Usuario::fetchArray($sql3);



        $sql5 = " SELECT e_catalogo,
        e_fecha,
         e_resultado,
         CASE
        WHEN e_diagnost = 1 THEN 'DEFICIT'
        WHEN e_diagnost = 2 THEN 'NORMAL'
        WHEN e_diagnost = 3 THEN 'SOBREPESO'
        WHEN e_diagnost = 4 THEN 'OBESIDAD 1'
        WHEN e_diagnost = 5 THEN 'OBESIDAD 2'

               ELSE 'NINGUNO'
           END AS diagnostico,
          e_puntuacion as puntos
       FROM evaluaciones
        WHERE e_numero = 1
        AND e_grado = '$grado'
        AND e_catalogo = '$catalogoOficial'
        AND e_fecha = (SELECT MAX(e_fecha) FROM evaluaciones WHERE e_numero = 1 AND e_grado = '$grado' AND e_catalogo = '$catalogoOficial')";

        $perfilBio = Usuario::fetchArray($sql5);



        //Periodos para las evaluaciones de desempeño
        $gradosPeriodos = [
            41 => 10, 40 => 10, 47 => 10, 46 => 10, 60 => 6, 59 => 6,
            65 => 6,  66 => 6,  74 => 8,  73 => 8,  82 => 8, 81 => 8, 89 => 10, 88 => 10
        ];


        $periodosGeneradosz = [];

        $periodosEnAnioBase = 2; // Siempre dos períodos por año

        if ($mesBase >= 5 && $mesBase <= 7) {
            // Si el mes base está entre mayo y julio, inicia desde el segundo período
            $periodoBase = 2;
        } else {
            // De lo contrario, inicia desde el primer período
            $periodoBase = 1;
        }

        for ($i = 0; $i < $gradosPeriodos[$grado]; $i++) {
            $anio = $anioBase + floor(($i + $periodoBase - 1) / $periodosEnAnioBase);
            $mes = $mesBase + ($i + $periodoBase - 1) * 6; // Avanza 6 meses por período
            if ($mes > 12) {

                $mes -= 12;
            }
            $periodo = (($i + $periodoBase - 1) % $periodosEnAnioBase + 1) . '-' . $anio;
            $periodosGeneradosz[$grado][] = $periodo;
        }

        foreach ($periodosGeneradosz[$grado] as $periodos) {

            $sql5 = " SELECT 
            per_catalogo as not_catalogo,
            gra_desc_lg as grado,
            not_fecha,
            'Z' as not_tipo,
            '$periodos' as not_periodo,
            promedio
            FROM mper
            LEFT JOIN (
                SELECT 
                    not_catalogo,
                    not_fecha,
                    not_tipo,
                    not_periodo,
                    MAX(CASE WHEN not_periodo LIKE '$periodos' AND (not_tipo = 'Z') THEN NOT_PROMEDIO ELSE null END) as promedio
                FROM opaf_notas
                WHERE not_grado = '$grado'
                AND not_catalogo = '$catalogoOficial'
                AND not_periodo LIKE '$periodos'
                GROUP BY not_fecha, not_tipo, not_catalogo, not_periodo
            ) AS opaf_notas ON per_catalogo = not_catalogo
            INNER JOIN grados ON per_grado = gra_codigo
            WHERE per_grado = '$grado'
            AND per_catalogo = '$catalogoOficial'
            AND per_situacion = 11 ";

            $sql5 = Usuario::fetchArray($sql5);

            if (!empty($sql5)) {
                $pafez[] = $sql5[0];
            }
        };


        $notaA = " SELECT
        per_grado,
        per_catalogo AS not_catalogo,
        gra_desc_lg AS grado,
        not_fecha,
        'A' as not_tipo,
        not_periodo,
        promedio
        FROM
        mper
        LEFT JOIN(
        SELECT not_catalogo, not_periodo, not_fecha, MAX(not_promedio) AS promedio
        FROM opaf_notas
        WHERE not_catalogo = '$catalogoOficial'
        and not_grado = '$grado'
        AND not_tipo = 'A'
        group by not_catalogo, not_fecha, not_periodo
        ) opaf_notas ON per_catalogo = not_catalogo
        INNER JOIN grados ON per_grado = gra_codigo
        WHERE  per_catalogo = '$catalogoOficial'
        AND per_grado = '$grado' ";

        $notaA = Usuario::fetchArray($notaA);

        if (!empty($notaA)) {
            $pafea[] = $notaA[0];
        }

        $pafes = array_merge($pafez, $pafea);




        //Periodos para la evaluaciones de desempeño por grados//
        $gradosPeriodos = [
            41 => 9, 40 => 9, 47 => 9, 46 => 9, 60 => 5, 59 => 5,
            65 => 5,  66 => 5,  74 => 7,  73 => 7,  82 => 7, 81 => 7, 89 => 9, 88 => 9
        ];


        $periodosGenerados = [];

        $periodosEnAnioBase = 2; // Siempre dos períodos por año

        if ($mesBase >= 5 && $mesBase <= 8) {
            // Si el mes base está entre mayo y julio, inicia desde el segundo período
            $periodoBase = 2;
        } else {
            // De lo contrario, inicia desde el primer período
            $periodoBase = 1;
        }

        for ($i = 0; $i < $gradosPeriodos[$grado]; $i++) {
            $anio = $anioBase + floor(($i + $periodoBase - 1) / $periodosEnAnioBase);
            $mes = $mesBase + ($i + $periodoBase - 1) * 6; // Avanza 6 meses por período
            if ($mes > 12) {

                $mes -= 12;
            }
            $periodo = (($i + $periodoBase - 1) % $periodosEnAnioBase + 1) . ' - ' . $anio;
            $periodosGenerados[$grado][] = $periodo;
        }



        foreach ($periodosGenerados[$grado] as $periodos) {

            $sql6 = "SELECT
            per_catalogo,
            eva_dest_actual,
            '$periodos' as eva_periodo,
            notas
            FROM
            mper
            LEFT JOIN (
            SELECT
                eva_cat1,
                eva_periodo,
                eva_dest_actual,
                CASE
                     WHEN eva_renglon = 1 THEN
                        CAST(CAST(SUM(not_nota) / 3 AS DECIMAL(10, 2)) AS VARCHAR(15))
                    WHEN eva_renglon = 2 THEN 'ART-9'
                    WHEN eva_renglon = 3 THEN 'ART-10'
                END AS notas
            FROM
                eva_evaluacion
            INNER JOIN eva_notas ON not_evaluacion = eva_id
            WHERE
                eva_situacion <> 9
                AND eva_grado1 = '$grado'
                AND eva_cat1 = '$catalogoOficial'
                AND eva_periodo LIKE '$periodos'
            GROUP BY
                eva_periodo,
                eva_renglon,
                eva_cat1,
                eva_dest_actual
            ) ON per_catalogo = eva_cat1
            
            WHERE
            per_catalogo = '$catalogoOficial'
            AND per_grado = '$grado'
            GROUP BY
            per_catalogo,
            eva_dest_actual,
            eva_cat1,
            notas ";


            $sql6 = Usuario::fetchArray($sql6);
            if (!empty($sql6)) {
                $desempenio[] = $sql6[0];
            }
        }


        try {

            $responseData = [
                'demeritos' => $demeritos,
                'cursoAscenso' => $cursoAscenso,
                'meritos' => $meritos,
                'pafes' => $pafes,
                'perfilBio' => $perfilBio,
                'desempenio' => $desempenio,
                'conducta' => $conducta
            ];

            echo json_encode($responseData);
        } catch (Exception $e) {
            echo json_encode([
                'detalle' => $e->getMessage(),
                'mensaje' => 'Ocurrió un error',
                'codigo' => 0
            ]);
        }
    }




    public static function llenarFormulario()
    {
       
        $catalogoOficial = $_GET['per_catalogo'];

        $ObtenerGrado = "SELECT per_grado FROM mper WHERE per_catalogo = '$catalogoOficial'";
        $grado = Usuario::fetchFirst($ObtenerGrado);

        $grado = $grado['per_grado'];


        $sql = " SELECT 
        per_catalogo,
        per_serie,
        trim(gra_desc_ct) || ' DE ' || trim(arm_desc_md) as grado,
        trim(per_ape1) || ' ' || trim(per_ape2) || ', ' || trim(per_nom1)  || ' ' || trim(per_nom2) as nombre,
        per_promocion,
        gra_desc_md,
        per_grado,
        arm_desc_md,
        per_arma,
        dep_desc_md as dependencia,
        meom_desc_lg as puesto,
        t_prox_asc
        FROM mper
        INNER JOIN grados ON per_grado = gra_codigo
        INNER JOIN armas ON per_arma = arm_codigo
        INNER JOIN tiempos ON per_catalogo = t_catalogo
        INNER JOIN morg ON per_plaza = ORG_PLAZA
        INNER JOIN meom on org_ceom = meom_ceom
        INNER JOIN mdep on dep_llave = org_dependencia
        WHERE per_grado = ($grado)
        AND gra_clase = 1 
        AND per_catalogo = '$catalogoOficial'
        AND per_situacion = 11";


        try {

            $llenarFormulario = Usuario::fetchFirst($sql);

            // Convertir el arreglo asociativo en JSON
            echo json_encode($llenarFormulario);
        } catch (Exception $e) {
            echo json_encode([
                'detalle' => $e->getMessage(),
                'mensaje' => 'Ocurrió un error',
                'codigo' => 0
            ]);
        }
    }
}
