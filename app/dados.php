<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class dados extends Model
{
    protected $fillable = [
        'incidencia_pelvica',
        'inclinacao_pelvica',
        'angulo_lordose_lombar',
        'inclinacao_sacral',
        'raio_pelvico',
        'grau_espondilolistese',
        'diagnostico'
    ];

}
