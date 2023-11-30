<?php

namespace Controllers;

use Exception;
use Model\Usuario;
use MVC\Router;


class EscalafonController
{

    public static function index(Router $router) {
        $router->render('escalafon/index', [
        ]);
    }

    public static function escalafon()
{
   

    $sql = " SELECT 
    mper.per_catalogo,
    trim(gra_desc_ct) || ' DE ' || trim(arm_desc_md) as grado,
    trim(per_ape1) || ' ' || trim(per_ape2) || ', ' || trim(per_nom1)  || ' ' || trim(per_nom2) as nombre,
    mper.per_promocion,
    grados.gra_desc_md,
    mper.per_grado,
    armas.arm_desc_md,
    mper.per_arma,
    dep_desc_md as dependencia,
    meom_desc_lg as puesto,
    t_prox_asc
    FROM mper
    INNER JOIN grados ON mper.per_grado = grados.gra_codigo
    INNER JOIN armas ON mper.per_arma = armas.arm_codigo
    INNER JOIN tiempos ON mper.per_catalogo = tiempos.t_catalogo
    INNER JOIN morg ON per_plaza = ORG_PLAZA
    INNER JOIN meom on org_ceom = meom_ceom
    INNER JOIN mdep on dep_llave = org_dependencia
    AND grados.gra_clase = 1 
    AND mper.per_situacion = 11
    ORDER BY per_grado desc";


    try {
        
        $escalafon = Usuario::fetchArray($sql);
       
        echo json_encode($escalafon);
    } catch (Exception $e) {
        echo json_encode([
            'detalle' => $e->getMessage(),
            'mensaje' => 'Ocurrió un error',
            'codigo' => 0
        ]);
    }
}

}


