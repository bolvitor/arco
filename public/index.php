<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\UsuarioController;
use Controllers\PromocionController;
use Controllers\EscalafonController;
use Controllers\AscensoController;
use Controllers\EstadisticaController;
use Controllers\ReporteController;
use Controllers\Reporte2Controller;
use Controllers\CreditosController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

// $router->get('/pages', [UsuarioController::class,'index'] );
$router->get('/usuarios', [UsuarioController::class,'index'] );
$router->get('/API/usuarios/buscar', [UsuarioController::class, 'buscarAPI']);
$router->get('/API/usuarios/buscarOficial', [UsuarioController::class, 'buscarOficial']);


$router->get('/promociones', [PromocionController::class,'index'] );
$router->get('/API/promocion/buscar', [PromocionController::class, 'buscarAPI']);
$router->get('/API/promocion/buscarOficial', [PromocionController::class, 'buscarOficial']);
$router->get('/API/promocion/llenarFormulario', [PromocionController::class, 'llenarFormulario']);

$router->get('/escalafon', [EscalafonController::class,'index'] );
$router->get('/API/escalafon/escalafon', [EscalafonController::class, 'escalafon']);


$router->get('/ascenso', [AscensoController::class,'index'] );
$router->get('/API/ascenso/buscar', [AscensoController::class, 'buscarAPI']);
$router->get('/API/ascenso/buscarOficial', [AscensoController::class, 'buscarOficial']);
$router->get('/API/ascenso/llenarFormulario', [AscensoController::class, 'llenarFormulario']);




$router->get('/pdf', [ReporteController::class, 'pdf']);
$router->get('/resultado', [ReporteController::class, 'pdf']);


$router->get('/excel', [Reporte2Controller::class, 'excel']);
$router->get('/resultado', [Reporte2Controller::class, 'excel']);


$router->get('/estadisticas', [EstadisticaController::class,'index'] );
$router->get('/API/estadisticas/getPromocion', [EstadisticaController::class, 'graficas']);
$router->get('/API/estadisticas/getPostergados', [EstadisticaController::class, 'graficas2']);



$router->get('/creditos', [CreditosController::class,'index'] );
// $router->post('/API/creditos/guardar', [CreditosController::class,'guardarAPI'] );
$router->post('/API/creditos/modificar', [CreditosController::class,'modificarAPI'] );
$router->post('/API/creditos/eliminar', [CreditosController::class,'eliminarAPI'] );
$router->get('/API/creditos/buscar', [CreditosController::class,'buscarAPI'] );


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
