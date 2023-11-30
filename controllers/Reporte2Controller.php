<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
// require_once 'AscensoController.php';



class Reporte2Controller
{



    public static function excel(Router $router)
    {


        // Obtener datos
        $datos = self::resultado();
        $usuarios1 = $datos['usuarios1'];
        $perfilBio1 = $datos['perfilBio1'];
        $curso1 = $datos['curso1'];
        $conducta1 = $datos['conducta1'];
        $meritos1 = $datos['meritos1'];
        $pafeSQL1 = $datos['pafeSQL1'];
        $desempenio1 = $datos['desempenio1'];
        $evaDesempenio1 = $datos['evaDesempenio1'];
        $periodos = $datos['periodos'];
        $periodosz = $datos['periodosz'];
        $gradoNombre = $datos['gradoNombre'];
        $notasz1 = $datos['notasz1'];
        $notaA1 = $datos['notaA1'];

        $html = $router->load('reporte2/pdf', [
            'usuarios1' => $usuarios1,
            'perfilBio1' => $perfilBio1,
            'curso1' => $curso1,
            'conducta1' => $conducta1,
            'meritos1' => $meritos1,
            'pafeSQL1' => $pafeSQL1,
            'desempenio1' => $desempenio1,
            'evaDesempenio1' => $evaDesempenio1,
            'periodos' => $periodos,
            'periodosz' => $periodosz,
            'gradoNombre' => $gradoNombre,
            'notasz1' => $notasz1,
            'notaA1' => $notaA1,
        ]);


        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment;filename=usuarios.xls");

        echo $html;
    }


    public static function resultado()
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


        // Crear arrays para almacenar los resultados
        $usuarios1 = [];
        $perfilBio1 = [];
        $curso1 = [];
        $conducta1 = [];
        $meritos1 = [];
        $pafeSQL1 = [];
        $desempenio1 = [];
        $evaDesempenio1 = [];
        $notasz1 = [];

        //Obtener el grado actual de los oficiales//
        $gradoNombre = "SELECT 
        TRIM('/' FROM TRIM(g1.gra_desc_lg) || ' / ' || TRIM(g2.gra_desc_lg)) AS nombres_completos
        FROM 
        grados g1
        JOIN 
        grados g2 ON (g1.gra_codigo =  $grado AND g2.gra_codigo = $grado2)";

        $gradoNombre = Usuario::fetchArray($gradoNombre);

        $gradoNombre = $gradoNombre[0];

        foreach ($catalogoOficial as $catalogo) {

            $sqlUsuarios = "SELECT 
                                mper.per_catalogo,
                                mper.per_promocion,
                                trim(gra_desc_ct) || ' DE ' || trim(arm_desc_md) as grado,
                                trim(per_ape1) || ' ' || trim(per_ape2) || ', ' || trim(per_nom1)  || ', ' || trim(per_nom2) as nombre,
                                t_ult_asc
                            FROM mper 
                            INNER JOIN grados ON mper.per_grado = grados.gra_codigo
                            INNER JOIN armas ON mper.per_arma = armas.arm_codigo
                            INNER JOIN tiempos ON mper.per_catalogo = tiempos.t_catalogo
                            WHERE grados.gra_clase = 1 
                            AND mper.per_catalogo = '$catalogo'
                            AND mper.per_situacion = 11
                            ORDER BY mper.per_catalogo ASC";


            $usuarios = Usuario::fetchArray($sqlUsuarios);
            if (!empty($usuarios)) {
                $usuarios1[] = $usuarios[0];
            }


            $sqlperfilBio = "SELECT
                                per_catalogo,
                                per_grado,
                                puntos,
                                CASE
                                    WHEN puntos IS NOT NULL AND puntos != 0 THEN CAST(puntos AS VARCHAR(255))
                                    ELSE 'S/R'
                                END AS puntos_texto
                            FROM mper
                            LEFT JOIN (
                                SELECT e_catalogo, e_fecha, e_resultado, e_puntuacion as puntos
                                    FROM evaluaciones
                                    WHERE e_numero = 1
                                    AND (e_grado = '$grado' OR e_grado = '$grado2')
                                    AND e_catalogo = '$catalogo'
                                    AND e_fecha = (SELECT MAX(e_fecha) FROM evaluaciones WHERE e_numero = 1 AND (e_grado = '$grado' OR e_grado = '$grado2') AND e_catalogo = '$catalogo')
                                    ) ON per_catalogo = e_catalogo
                                WHERE per_catalogo = '$catalogo'";


            $perfilBio = Usuario::fetchArray($sqlperfilBio);
            if (!empty($perfilBio)) {
                $perfilBio1[] = $perfilBio[0];
            }


            $sqlCurso = "SELECT
                            per_catalogo,
                            per_grado,
                            CASE
                            WHEN promedio IS NOT NULL THEN CAST(ROUND(promedio, 2) AS VARCHAR(255))
                            ELSE 'S/R'
                        END AS promedio_texto
                        FROM mper
                        LEFT JOIN (
                            SELECT 
                            cur_catalogo,
                            cur_punteo,
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
                            INNER JOIN cursos ON dcur.cur_curso = cursos.cur_codigo
                            WHERE dcur.cur_curso IN (3200, 750, 751, 752, 753, 754, 755, 756, 777, 778, 781, 783, 1020, 1099, 3201, 784, 710, 43)
                            AND (cur_grado = '$grado' OR cur_grado = '$grado2')
                            AND cur_catalogo = '$catalogo'
                            AND cur_fec_fin = (SELECT MAX(cur_fec_fin) FROM dcur WHERE cur_catalogo = '$catalogo')
                            ) ON per_catalogo = cur_catalogo
                            WHERE per_catalogo = '$catalogo'";

            $curso = Usuario::fetchArray($sqlCurso);
            if (!empty($curso)) {
                $curso1[] = $curso[0];
            }


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
                                            WHEN mper.per_grado IN (41, 40, 47, 46, 60, 59, 65, 66) THEN ROUND((30 - (demeritos * 30 / 150)), 2)
                                            WHEN mper.per_grado IN (74, 73, 82, 81) THEN ROUND((25 - (demeritos * 25 / 100)), 2)
                                            WHEN mper.per_grado IN (89, 88) THEN ROUND((20 - (demeritos * 20 / 100)), 2)
                                        END
                                    ELSE
                                        CASE
                                            WHEN mper.per_grado IN (41, 40, 47, 46, 60, 59, 65, 66) THEN 30
                                            WHEN mper.per_grado IN (74, 73, 82, 81) THEN 25
                                            WHEN mper.per_grado IN (89, 88) THEN 20
                                        END
                                END AS punteo_demeritos
                            FROM mper
                                LEFT JOIN (
                                SELECT
                                    est_catalogo,
                                    est_demeritos AS demeritos
                                FROM
                                    psan_estadistica
                                    WHERE (est_grado = '$grado' OR est_grado = '$grado2')
                                ) ON mper.per_catalogo = est_catalogo
                                INNER JOIN grados ON mper.per_grado = grados.gra_codigo
                                WHERE grados.gra_clase = 1
                                AND mper.per_catalogo = '$catalogo'
                                AND mper.per_situacion = 11
                                AND (mper.per_grado = '$grado' OR mper.per_grado = '$grado2')";


            $conducta = Usuario::fetchArray($sqlconducta);
            if (!empty($conducta)) {
                $conducta1[] = $conducta[0];
            }



            $meritos = "SELECT
                            mper.per_catalogo,
                            CASE WHEN SUM(total_meritos) IS NULL THEN 0 ELSE SUM(total_meritos) END AS total_meritos,
                            (CASE WHEN SUM(total_meritos) IS NULL THEN 0 ELSE SUM(total_meritos) END / 100 * 5) AS puntos_netos
                        FROM mper
                        LEFT JOIN (
                            SELECT
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
                                ) AS total_meritos
                            FROM dcur
                            INNER JOIN cursos ON dcur.cur_curso = cursos.cur_codigo
                            WHERE cur_grado = '$grado'
                            GROUP BY dcur.cur_catalogo
                            UNION ALL
                            SELECT
                                dcon.con_catalogo AS catalogo,
                                SUM(
                                    CASE
                                        WHEN dcon.con_condecoracion IN (21293, 21487) THEN 3
                                        WHEN LOWER(cond.con_desc_lg) LIKE '%cruz de%' THEN 2
                                        WHEN LOWER(cond.con_desc_lg) LIKE '%medalla%' THEN 1
                                        WHEN LOWER(cond.con_desc_lg) LIKE '%citacion%' THEN 1
                                        WHEN LOWER(cond.con_desc_lg) LIKE '%alas%' OR LOWER(cond.con_desc_lg) LIKE '%instructor%' THEN 0.5
                                        WHEN LOWER(cond.con_desc_lg) LIKE '%placa de combatiente%' THEN 0.5
                                        WHEN LOWER(cond.con_desc_lg) LIKE '%roble de oro%' THEN 0.5
                                        ELSE 0
                                    END
                                ) AS total_meritos
                            FROM dcon
                            INNER JOIN cond ON dcon.con_condecoracion = cond.con_codigo
                            WHERE (con_grado = '$grado' OR con_grado = '$grado2')
                            GROUP BY dcon.con_catalogo
                        ) AS subquery
                        ON mper.per_catalogo = subquery.catalogo
                        WHERE (mper.per_grado = '$grado' OR mper.per_grado = '$grado2')
                        AND mper.per_catalogo = '$catalogo'
                        GROUP BY mper.per_catalogo ";

            $meritos = Usuario::fetchArray($meritos);

            if (!empty($meritos)) {
                $meritos1[] = $meritos[0];
            }



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



            // --------------------OBTENER PERIODOS PARA LAS NOTAS DE PAFES ---------------------------------------------

            // Cantidad de periodos a generar por grado

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



            // Definir un array asociativo que contiene los punteos para cada grado
            $punteosZ = [
                41 => 8, 40 => 8, 47 => 8, 46 => 8, 60 => 8, 59 => 8, 65 => 8,
                66 => 8, 74 => 6, 73 => 6, 82 => 6, 81 => 6, 89 => 6, 88 => 6,
            ];

            $punteosA = [
                41 => 12, 40 => 12, 47 => 12, 46 => 12, 60 => 12, 59 => 12, 65 => 12,
                66 => 12, 74 => 9,  73 => 9,  82 => 9,  81 => 9,  89 => 9,  88 => 9,
            ];

            $punteopromedio = [
                41 => 20, 40 => 20, 47 => 20, 46 => 20, 60 => 20, 59 => 20, 65 => 20,
                66 => 20, 74 => 15,  73 => 15,  82 => 15,  81 => 15,  89 => 15,  88 => 15,
            ];



            $pafeSQL = "SELECT
                        mper.per_catalogo,
                        mper.per_grado,
                        total_notasA,
                        cantidad_notas_z,
                        NOTAS_Z,
                        CASE
                        WHEN NOTAS_Z IS NULL THEN 0 -- Establecer resultado_final en 0 cuando notas_r1 es 0
                        ELSE ROUND(LEAST(total_notasA + (NOTAS_Z / NULLIF({$gradosPeriodos[$grado]}, 0)) * {$punteosZ[$grado]} / 100, {$punteopromedio[$grado]}),2)
                        END AS  suma_total
                        
                        FROM mper
                        LEFT JOIN (
                        SELECT not_catalogo,
                        ";


            foreach ($periodosGeneradosz[$grado] as $periodospafe) {

                $pafeSQL .= "
                        NVL(
                            MAX(CASE WHEN opaf_notas.not_periodo LIKE '%$periodospafe %' AND (opaf_notas.not_tipo = 'Z') THEN opaf_notas.NOT_PROMEDIO ELSE 0 END), 0
                        ) + ";
            }

            $pafeSQL = rtrim($pafeSQL, ' + '); // Eliminar el espacio antes de AS
            $pafeSQL .= "
                        AS NOTAS_Z,
                        LEAST(
                        MAX(CASE WHEN (opaf_notas.not_tipo = 'A') THEN opaf_notas.NOT_PROMEDIO ELSE 0 END) * {$punteosA[$grado]} / 100, {$punteosA[$grado]}) AS total_notasA,
                        
                        COUNT(CASE WHEN opaf_notas.not_tipo = 'Z' THEN 1 END) AS cantidad_notas_z
                        FROM opaf_notas
                        WHERE (not_grado = '$grado' OR not_grado = '$grado2')
                        GROUP BY not_catalogo
                        ) ON mper.per_catalogo = not_catalogo
                        INNER JOIN grados ON mper.per_grado = grados.gra_codigo
                        WHERE (mper.per_grado = '$grado' OR mper.per_grado = '$grado2')
                        AND grados.gra_clase = 1 
                        AND mper.per_catalogo = '$catalogo'
                        AND mper.per_situacion = 11
                        ";


            $pafeSQL = Usuario::fetchArray($pafeSQL);


            if (!empty($pafeSQL)) {
                $pafeSQL1[] = $pafeSQL[0];
            }


            foreach ($periodosGeneradosz[$grado] as $periodos) {

                $notasz = " SELECT 
                per_catalogo,
                per_grado,
                notaz
                FROM mper
                LEFT JOIN (
                SELECT not_catalogo,    
                MAX(CASE WHEN opaf_notas.not_periodo LIKE '%$periodos%' AND (opaf_notas.not_tipo = 'Z') THEN opaf_notas.NOT_PROMEDIO ELSE null END)
                as notaz
                FROM opaf_notas
                WHERE (not_grado = '$grado' OR not_grado = '$grado2')
                and not_catalogo = '$catalogo'
                GROUP BY not_catalogo
                ) ON mper.per_catalogo = not_catalogo
                WHERE (per_grado = '$grado' OR per_grado = '$grado2')
                AND per_catalogo = '$catalogo'
                AND per_situacion = 11 ";


                $notasz = Usuario::fetchArray($notasz);


                if (!empty($notasz)) {
             
                    $notasz1[$notasz[0]['per_catalogo']][] = $notasz[0]['notaz'];
                }
            };

     



            //---------------------------------------------------------------------------------------------------------------------------------------------------------------------------

            // Definir un array que contiene los grados y la cantidad de períodos que necesitas, son dos periodos por año 1-$anio y 2-$anio
            $gradosPeriodos = [
                41 => 9, 40 => 9, 47 => 9, 46 => 9, 60 => 5, 59 => 5,
                65 => 5,  66 => 5,  74 => 7,  73 => 7,  82 => 7, 81 => 7, 89 => 9, 88 => 9
            ];


            $periodosGenerados = [];

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
                $periodo = (($i + $periodoBase - 1) % $periodosEnAnioBase + 1) . ' - ' . $anio;
                $periodosGenerados[$grado][] = $periodo;
            }



            // Definir un array asociativo que contiene los punteos para cada grado
            $promedios = [
                41 => 20, 40 => 20, 47 => 20, 46 => 20, 60 => 20, 59 => 20, 65 => 20,
                66 => 20, 74 => 30, 73 => 30, 82 => 30, 81 => 30, 89 => 30, 88 => 30
            ];


            $desempenio = " SELECT per_catalogo, per_grado, 
            case when resultado_final is not null then resultado_final else 0 END AS resultado_fin
            from mper
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

            $periodosGenerados[$grado] = array_values($periodosGenerados[$grado]); // Reindexa el array

            $desempenio .= implode(" +\n", array_map(function ($periodo) {
                return "(SUM(CASE WHEN eva_periodo LIKE '%$periodo%' AND eva_renglon = 1 THEN eva_notas.not_nota ELSE 0 END) / 3)";
            }, $periodosGenerados[$grado]));


            $desempenio .= "
                )AS total_notas
            FROM eva_evaluacion
                INNER JOIN eva_notas ON not_evaluacion = eva_id
                WHERE eva_situacion <> 9
                AND (eva_grado1 = '$grado' OR eva_grado1 = '$grado2')
                AND eva_cat1 = '$catalogo'
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
                AND eva_cat1 = '$catalogo'
                AND (eva_periodo LIKE '%" . implode("%' OR eva_periodo LIKE '%", $periodosGenerados[$grado]) . "%')
                GROUP BY eva_cat1
            ) AS r1
            ON sub.eva_cat1 = r1.eva_cat1
            ) ON per_catalogo = eva_cat1

            where (per_grado = '$grado' OR per_grado = '$grado2')
            AND per_situacion = 11
            AND per_catalogo = '$catalogo'";


            $desempenio = Usuario::fetchArray($desempenio);


            if (!empty($desempenio)) {
                $desempenio1[] = $desempenio[0];
            }

            //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------          



            foreach ($periodosGenerados[$grado] as $periodos) {

                $evaDesempenio = "SELECT
                per_catalogo,
                notas_pafe
                FROM
                mper
                LEFT JOIN (
                SELECT
                    eva_cat1,
                    eva_periodo,
                    CASE
                         WHEN eva_renglon = 1 THEN
                            CAST(CAST(SUM(not_nota) / 3 AS DECIMAL(10, 2)) AS VARCHAR(15))
                        WHEN eva_renglon = 2 THEN 'ART-9'
                        WHEN eva_renglon = 3 THEN 'ART-10'
                    END AS notas_pafe
                FROM
                    eva_evaluacion
                INNER JOIN eva_notas ON not_evaluacion = eva_id
                WHERE
                    eva_situacion <> 9
                    AND (eva_grado1 = '$grado' OR eva_grado1 = '$grado2')
                    AND eva_cat1 = '$catalogo'
                    AND eva_periodo LIKE '$periodos'
                GROUP BY
                    eva_periodo,
                    eva_renglon,
                    eva_cat1
                ) ON per_catalogo = eva_cat1
                
                WHERE
                per_catalogo = '$catalogo'
                AND (per_grado = '$grado' OR per_grado = '$grado2')
                GROUP BY
                eva_periodo,
                per_catalogo,
                per_grado,
                eva_periodo,
                notas_pafe ";

                $evaDesempenio = Usuario::fetchArray($evaDesempenio);

                if (!empty($evaDesempenio)) {
  
                    $evaDesempenio1[$evaDesempenio[0]['per_catalogo']][] = $evaDesempenio[0]['notas_pafe'];
                }
            }


            $notaA = "SELECT per_catalogo, max(not_promedio) as promedios
                            FROM mper
                            LEFT JOIN (
                            SELECT not_promedio, not_catalogo
                            from opaf_notas
                            WHERE not_catalogo = '$catalogo'
                            and not_tipo = 'A'
                            and (not_grado = '$grado' OR not_grado = '$grado2')
                            ) ON per_catalogo = not_catalogo
                            WHERE per_catalogo = '$catalogo'
                            and (per_grado = '$grado' OR per_grado = '$grado2')
                            GROUP BY per_catalogo
            ";

            $notaA = Usuario::fetchArray($notaA);


            // Almacenar los resultados en el array
            if (!empty($notaA)) {
                $notaA1[] = $notaA[0];
            }
        }




        return [
            'usuarios1' => $usuarios1, 'perfilBio1' => $perfilBio1, 'curso1' => $curso1,
            'conducta1' => $conducta1, 'meritos1' => $meritos1, 'pafeSQL1' => $pafeSQL1,
            'desempenio1' => $desempenio1, 'evaDesempenio1' => $evaDesempenio1,
            'periodos' => $periodosGenerados[$grado], 'gradoNombre' => $gradoNombre,
            'notasz1' => $notasz1, 'periodosz' => $periodosGeneradosz[$grado], 'notaA1' => $notaA1
        ];
    }
};
