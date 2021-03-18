<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NutritionalInfoStatic extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'code'; // or null
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'nutritional_info_static';
    protected $fillable = [
              'name',
              'code',
              'energy_kcal',
              'energy_kJ',
              'water_g',
              'protein_g',
              'fats_g',
              'carbo_hidrats_g',
              'fiber_g',
              'colesterol_mg',
              'vitA_mg',
              'vitD_pg',
              'tiamina_mg',
              'riboflavina_mg',
              'niacina_mg',
              'vitB6_mg',
              'vit_B12_pg',
              'vitC_mg',
              'na_mg',
              'k_mg',
              'ca_mg',
              'p_mg',
              'mg_mg',
              'fe_mg',
              'zn_mg'
    ];
}
