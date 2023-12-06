<?php

namespace Model;

class meritosCond extends ActiveRecord {
    public static $tabla = 'con_creditos_arco';
    public static $columnasDB = ['condecoracion_codigo', 'meritos', 'situacion'];
    public static $idTabla = 'id_meritos';

    public $id_meritos;
    public $condecoracion_codigo;
    public $meritos;
    public $situacion;

    public function __construct($args = []) {
        $this->id_meritos = $args['id_meritos'] ?? null;
        $this->condecoracion_codigo = $args['condecoracion_codigo'] ?? null;
        $this->meritos = $args['meritos'] ?? null;
        $this->situacion = $args['situacion'] ?? 1;
    }
}
