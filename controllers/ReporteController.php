<?php

namespace Controllers;

use Mpdf\Mpdf;
use MVC\Router;
use Model\Usuario;
use Controllers\AscensoController;


class ReporteController
{

    public static function pdf(Router $router)
    {
        $datos = self::resultado();
        $usuarios1 = $datos['usuarios1'];
        $perfilBio1 = $datos['perfilBio1'];
        $curso1 = $datos['curso1'];
        $conducta1 = $datos['conducta1'];
        $meritos1 = $datos['meritos1'];
        $pafeSQL1 = $datos['pafeSQL1'];
        $desempenio1 = $datos['desempenio1'];

        $mpdf = new Mpdf([
            "orientation" => "L",
            "default_font_size" => 10,
            "default_font" => "arial",
            "format" => "Legal",
            "mode" => 'utf-8'
        ]);
        $mpdf->SetMargins(10, 10, 10);

        // Pasar los datos a la vista
        $html = $router->load('reporte/pdf', [
            'usuarios1' => $usuarios1,
            'perfilBio1' => $perfilBio1,
            'curso1' => $curso1,
            'conducta1' => $conducta1,
            'meritos1' => $meritos1,
            'pafeSQL1' => $pafeSQL1,
            'desempenio1' => $desempenio1,
        ]);

        $htmlHeader = $router->load('reporte/header');
        $htmlFooter = $router->load('reporte/footer');

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }


    public static function resultado()
    {
        $catalogoOficial = json_decode($_GET['catalogos']);
        $grado = $_GET['grado'];
        $grado2 = $_GET['grado2'];

        $usuarios1 = [];
        $perfilBio1 = [];
        $curso1 = [];
        $conducta1 = [];
        $meritos1 = [];
        $pafeSQL1 = [];
        $desempenio1 = [];

        // Recorrer cada catálogo
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

            // Obtener datos de méritos
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
            FROM
            mper
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


            $gradosYPeriodos = [
                41 => 10, 40 => 10, 47 => 10, 46 => 10, 60 => 6, 59 => 6,
                65 => 6,  66 => 6,  74 => 8,  73 => 8,  82 => 8, 81 => 8, 89 => 10,
            ];


            $ObtenerFecha1 = "SELECT t_ult_asc FROM tiempos WHERE t_catalogo = '$catalogo'";
            $fechaBase = Usuario::fetchFirst($ObtenerFecha1);
            $fechaBase = $fechaBase['t_ult_asc'];


            $periodos = [];

            for ($i = 0; $i < $gradosYPeriodos[$grado]; $i++) {
                $periodoInicial = $fechaBase;
                $periodoFinal = date('Y-m-d', strtotime("$fechaBase +6 months"));

                $periodos[] = [$periodoInicial, $periodoFinal];

                $fechaBase = $periodoFinal; // Actualizar la fecha base para el próximo período
            }

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
            ELSE ROUND(LEAST(total_notasA + (NOTAS_Z / NULLIF({$gradosYPeriodos[$grado]}, 0)) * {$punteosZ[$grado]} / 100, {$punteopromedio[$grado]}),2)
            END AS  suma_total
            
            FROM mper
            LEFT JOIN (
            SELECT not_catalogo,
            ";

            foreach ($periodos as $periodo) {
                $periodoInicial = $periodo[0];
                $periodoFinal = $periodo[1];

                $pafeSQL .= "
                NVL(
                    MAX(CASE WHEN (opaf_notas.NOT_FECHA >= '$periodoInicial' AND opaf_notas.NOT_FECHA < '$periodoFinal') AND (opaf_notas.not_tipo = 'Z') THEN opaf_notas.NOT_PROMEDIO ELSE 0 END), 0
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


            $Obteneranio = "SELECT
            CASE
                WHEN MONTH(t_ult_asc) = 12 THEN YEAR(t_ult_asc) + 1
                ELSE YEAR(t_ult_asc)
            END AS anio,
            MONTH(t_ult_asc) AS mes
            FROM tiempos
            WHERE t_catalogo = '$catalogo'";

            $resultado = Usuario::fetchFirst($Obteneranio);

            $anioBase = $resultado['anio'];
            $mesBase = $resultado['mes'];


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


            $desempenio = "
            select per_catalogo, per_grado, 
            case when resultado_final is not null then resultado_final else 0 END AS resultado_fin
            from mper
            LEFT JOIN(
            SELECT
            sub.eva_cat1,
            CASE
                WHEN total_notas = 0 THEN 0 -- Establecer resultado_final en 0 cuando notas_r1 es 0
                ELSE ROUND(total_notas / notas_r1, 2) 
            END AS  promedio_total_notas,
            r1.notas_r1 AS recuento_eva_renglon1,
            r1.notas_r2 AS recuento_eva_renglon2,
            r1.notas_r3 AS recuento_eva_renglon3,
            CASE
                WHEN r1.notas_r1 = 0 THEN 0 -- Establecer resultado_final en 0 cuando notas_r1 es 0
                ELSE ROUND((total_notas / notas_r1 * {$promedios[$grado]} / ({$gradosPeriodos[$grado]} - (r1.notas_r2 + r1.notas_r3)) * r1.notas_r1) / 100, 2)
            END AS resultado_final
            FROM (
                SELECT
                    eva_cat1,
                    (
            ";

            // Generar las partes dinámicas de la consulta según los periodos
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
        }



        foreach ($usuarios1 as &$usuario) {
            $perfilOficial = self::encontrarPerfil($usuario['per_catalogo'], $perfilBio1);
            $cursoOficial = self::encontrarCurso($usuario['per_catalogo'], $curso1);
            $conductaOficial = self::encontrarConducta($usuario['per_catalogo'], $conducta1);
            $meritoOficial = self::encontrarMerito($usuario['per_catalogo'], $meritos1);
            $pafeOficial = self::encontrarPafe($usuario['per_catalogo'], $pafeSQL1);
            $desempenioOficial = self::encontrardesempenio($usuario['per_catalogo'], $desempenio1);

            $usuario = array_merge($usuario, $perfilOficial, $cursoOficial, $conductaOficial, $meritoOficial, $pafeOficial, $desempenioOficial);
        }

        return ['usuarios1' => $usuarios1, 'perfilBio1' => $perfilBio1, 'curso1' => $curso1, 'conducta1' => $conducta1, 'meritos1' => $meritos1, 'pafeSQL1' => $pafeSQL1, 'desempenio1' => $desempenio1];
    }

    public static function encontrarPerfil($catalogo, $perfilBio)
    {
        foreach ($perfilBio as $perfil) {
            if ($perfil['per_catalogo'] === $catalogo) {
                return $perfil;
            }
        }
        return null;
    }

    public static function encontrarCurso($catalogo, $curso)
    {
        foreach ($curso as $cursoA) {
            if ($cursoA['per_catalogo'] === $catalogo) {
                return $cursoA;
            }
        }
        return null;
    }

    public static function encontrarConducta($catalogo, $conducta)
    {
        foreach ($conducta as $conductaA) {
            if ($conductaA['per_catalogo'] === $catalogo) {
                return $conductaA;
            }
        }
        return null;
    }

    public static function encontrarMerito($catalogo, $meritos)
    {
        foreach ($meritos as $meritosA) {
            if ($meritosA['per_catalogo'] === $catalogo) {
                return $meritosA;
            }
        }
        return null;
    }



    public static function encontrarPafe($catalogo, $pafeSQL)
    {
        foreach ($pafeSQL as $pafeSQLA) {
            if ($pafeSQLA['per_catalogo'] === $catalogo) {
                return $pafeSQLA;
            }
        }
        return null;
    }

    public static function encontrardesempenio($catalogo, $desempenio)
    {
        foreach ($desempenio as $desempenioA) {
            if ($desempenioA['per_catalogo'] === $catalogo) {
                return $desempenioA;
            }
        }
        return null;
    }
};
