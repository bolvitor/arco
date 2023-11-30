<?php

namespace Model;
class Grado extends ActiveRecord
{
    protected static $tabla = 'grados';
    protected static $columnasDB = ['gra_desc_lg', 'gra_desc_md', 'gra_desc_ct', 'gra_asc', 'gra_tiempo', 'gra_clase', 'gra_demeritos'];
    protected static $idTabla = 'gra_codigo';

    public $gra_codigo;
    public $gra_desc_lg;
    public $gra_desc_md;
    public $gra_desc_ct;
    public $gra_asc;
    public $gra_tiempo;
    public $gra_clase;
    public $gra_demeritos;

    public function __construct($args = [])
    {
        $this->gra_codigo = $args['gra_codigo'] ?? null;
        $this->gra_desc_lg = $args['gra_desc_lg'] ?? '';
        $this->gra_desc_md = $args['gra_desc_md'] ?? '';
        $this->gra_desc_ct = $args['gra_desc_ct'] ?? '';
        $this->gra_asc = $args['gra_asc'] ?? null;
        $this->gra_tiempo = $args['gra_tiempo'] ?? null;
        $this->gra_clase = $args['gra_clase'] ?? '';
        $this->gra_demeritos = $args['gra_demeritos'] ?? null;
    }
}


