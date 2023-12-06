<?php

namespace Controllers;

use Exception;
use Model\meritosCond;
use Model\meritosCursos;
use MVC\Router;



class CreditosController
{

    public static function index(Router $router)
    {
        $router->render('creditos/index', []);
    }



    public static function modificarAPI() {
        try {
          
            $id = $_POST['id'];
            $cursos = meritosCursos::find($id);
            $cursos->meritos = $_POST['Meritos_Curso'];
            $resultado = $cursos->actualizar();
            
    
            if ($resultado['resultado'] == 1) {
                echo json_encode([
                    'mensaje' => 'Registro modificado correctamente',
                    'codigo' => 1
                ]);
            } else {
                echo json_encode([
                    'mensaje' => 'No se encontraron registros para actualizar',
                    'codigo' => 0
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'detalle' => $e->getMessage(),
                'mensaje' => 'Error al realizar la operación',
                'codigo' => 0
            ]);
        }
    }


    public static function eliminarAPI(){

        try {
            $id_meritos = $_POST['id_meritos'];
   
            $meritos = meritosCursos::find($id_meritos);
            $meritos->situacion = 0;
            $resultado = $meritos->actualizar();
    
            if ($resultado['resultado'] == 1) {
                echo json_encode([
                    'mensaje' => 'Registro eliminado correctamente',
                    'codigo' => 1
                ]);
            } else {
                echo json_encode([
                    'mensaje' => 'Ocurrió un error',
                    'codigo' => 0
                ]);
            }
            // echo json_encode($resultado);
        } catch (Exception $e) {
            echo json_encode([
                'detalle' => $e->getMessage(),
                'mensaje' => 'Ocurrió un error',
                'codigo' => 0
            ]);
        }
      }

   

    public static function buscarCursos()
    {
        $sql = "SELECT 
        id_meritos,
        curso_codigo AS codigo,
        cur_desc_lg AS descripcion,
        meritos AS meritos
        FROM cur_creditos_arco
        LEFT JOIN cursos ON curso_Codigo = cur_codigo
        WHERE situacion = 1";

        try {
            $armas = meritosCursos::fetchArray($sql);
            
            return $armas;
        } catch (Exception $e) {
            return [];
        }
    }



    public static function buscarCond()
    {
        $sql = "SELECT 
                id_meritos,
                condecoracion_codigo AS codigo,
                con_desc_lg AS descripcion,
                meritos AS meritos
                FROM con_creditos_arco 
                LEFT JOIN cond ON Condecoracion_Codigo = con_codigo
                WHERE situacion = 1";

        try {
            $armas = meritosCond::fetchArray($sql);
            return $armas;
        } catch (Exception $e) {
            return [];
        }
    }


    public static function buscarAPI()
    {
        $tipoOpcion = $_GET['tipo_opcion'] ?? '';
    
        if ($tipoOpcion === 'cursos') {
            $resultados = self::buscarCursos();
        } elseif ($tipoOpcion === 'condecoraciones') {
            $resultados = self::buscarCond();
        } else {
            echo json_encode([
                'detalle' => 'Tipo de opción no válida',
                'mensaje' => 'Ocurrió un error',
                'codigo' => 0
            ]);
            return;
        }

        echo json_encode($resultados);
    }
}
