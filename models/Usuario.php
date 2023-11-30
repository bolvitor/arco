<?php

namespace Model;

class Usuario extends ActiveRecord
{
    protected static $tabla = 'mper'; 
    protected static $columnasDB = ['per_serie', 'per_grado', 'per_arma', 'per_nom1', 'per_nom2', 'per_ape1', 'per_ape2', 'per_ape3', 'per_ced_ord', 'per_ced_reg', 'per_fec_ext_ced', 'per_ext_ced_lugar', 'per_est_civil', 'per_direccion', 'per_zona', 'per_dir_lugar', 'per_telefono', 'per_sexo', 'per_fec_nac', 'per_nac_lugar', 'per_promocion', 'per_afil_ipm', 'per_sangre', 'per_antiguedad', 'per_bienal', 'per_plaza', 'per_desc_empleo', 'per_fec_nomb', 'per_ord_gral', 'per_punto_og', 'per_situacion', 'per_prima_prof', 'per_dpi'];
    protected static $idTabla = 'per_catalogo';

    public $per_catalogo;
    public $per_serie;
    public $per_grado;
    public $per_arma;
    public $per_nom1;
    public $per_nom2;
    public $per_ape1;
    public $per_ape2;
    public $per_ape3;
    public $per_ced_ord;
    public $per_ced_reg;
    public $per_fec_ext_ced;
    public $per_ext_ced_lugar;
    public $per_est_civil;
    public $per_direccion;
    public $per_zona;
    public $per_dir_lugar;
    public $per_telefono;
    public $per_sexo;
    public $per_fec_nac;
    public $per_nac_lugar;
    public $per_promocion;
    public $per_afil_ipm;
    public $per_sangre;
    public $per_antiguedad;
    public $per_bienal;
    public $per_plaza;
    public $per_desc_empleo;
    public $per_fec_nomb;
    public $per_ord_gral;
    public $per_punto_og;
    public $per_situacion;
    public $per_prima_prof;
    public $per_dpi;

    public function __construct($args = [])
    {
        $this->per_catalogo = $args['per_catalogo'] ?? null;
        $this->per_serie = $args['per_serie'] ?? '';
        $this->per_grado = $args['per_grado'] ?? null;
        $this->per_arma = $args['per_arma'] ?? null;
        $this->per_nom1 = $args['per_nom1'] ?? '';
        $this->per_nom2 = $args['per_nom2'] ?? '';
        $this->per_ape1 = $args['per_ape1'] ?? '';
        $this->per_ape2 = $args['per_ape2'] ?? '';
        $this->per_ape3 = $args['per_ape3'] ?? '';
        $this->per_ced_ord = $args['per_ced_ord'] ?? '';
        $this->per_ced_reg = $args['per_ced_reg'] ?? '';
        $this->per_fec_ext_ced = $args['per_fec_ext_ced'] ?? null;
        $this->per_ext_ced_lugar = $args['per_ext_ced_lugar'] ?? '';
        $this->per_est_civil = $args['per_est_civil'] ?? '';
        $this->per_direccion = $args['per_direccion'] ?? '';
        $this->per_zona = $args['per_zona'] ?? null;
        $this->per_dir_lugar = $args['per_dir_lugar'] ?? '';
        $this->per_telefono = $args['per_telefono'] ?? null;
        $this->per_sexo = $args['per_sexo'] ?? '';
        $this->per_fec_nac = $args['per_fec_nac'] ?? null;
        $this->per_nac_lugar = $args['per_nac_lugar'] ?? '';
        $this->per_promocion = $args['per_promocion'] ?? null;
        $this->per_afil_ipm = $args['per_afil_ipm'] ?? '';
        $this->per_sangre = $args['per_sangre'] ?? '';
        $this->per_antiguedad = $args['per_antiguedad'] ?? null;
        $this->per_bienal = $args['per_bienal'] ?? null;
        $this->per_plaza = $args['per_plaza'] ?? null;
        $this->per_desc_empleo = $args['per_desc_empleo'] ?? '';
        $this->per_fec_nomb = $args['per_fec_nomb'] ?? null;
        $this->per_ord_gral = $args['per_ord_gral'] ?? '';
        $this->per_punto_og = $args['per_punto_og'] ?? null;
        $this->per_situacion = $args['per_situacion'] ?? 1 ;
        $this->per_prima_prof = $args['per_prima_prof'] ?? null;
        $this->per_dpi = $args['per_dpi'] ?? '';
    }
}
