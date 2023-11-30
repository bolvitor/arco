<?php

namespace Model;

class Arma extends ActiveRecord
{
    protected static $tabla = 'armas';
    protected static $columnasDB = ['arm_desc_lg', 'arm_desc_md', 'arm_desc_ct'];
    protected static $idTabla = 'arm_codigo';

    public $arm_codigo;
    public $arm_desc_lg;
    public $arm_desc_md;
    public $arm_desc_ct;

    public function __construct($args = [])
    {
        $this->arm_codigo = $args['arm_codigo'] ?? null;
        $this->arm_desc_lg = $args['arm_desc_lg'] ?? '';
        $this->arm_desc_md = $args['arm_desc_md'] ?? '';
        $this->arm_desc_ct = $args['arm_desc_ct'] ?? '';
    }
}
