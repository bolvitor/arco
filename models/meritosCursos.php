<?php

namespace Model;

class meritosCursos extends ActiveRecord {
    public static $tabla = 'cur_creditos_arco';
    public static $columnasDB = ['curso_codigo', 'meritos', 'situacion'];
    public static $idTabla = 'id_meritos';

    public $id_meritos;
    public $curso_codigo;
    public $meritos;
    public $situacion;

    public function __construct($args = []) {
        $this->id_meritos = $args['id_meritos'] ?? null;
        $this->curso_codigo = $args['curso_codigo'] ?? null;
        $this->meritos = $args['meritos'] ?? null;
        $this->situacion = $args['situacion'] ?? 1;
    }
}
