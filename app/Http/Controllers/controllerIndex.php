<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RedeNeural;
use App\dados;
class controllerIndex extends Controller
{
    public function index(){
        $DB2 = dados::where([['exame_id', '>=', 1],['exame_id','<=',36]]);
        $DB1 = dados::where([['exame_id','>=',61],['exame_id','<=',150]]);
        $DB = dados::where([['exame_id','>=',211],['exame_id','<=',270]])->union($DB1)->union($DB2)->get();

        set_time_limit(10000);
        $nr_amostras = count($DB);
        $entradas = $this->trata_entradas( $nr_amostras ,$DB);//caso for utilizar este codigo para outros problemas alterar essa função
        $nr_neuronios_entrada = count($entradas[0])-1;//quantidade de entradas
        $nr_amostras_tratadas = count($entradas);
        $rede = new RedeNeural($entradas, $nr_neuronios_entrada, $nr_amostras_tratadas);
    }

    private function trata_entradas($nr_amostras, $dados){
        //'incidencia_pelvica''inclinacao_pelvica''angulo_lordose_lombar''inclinacao_sacral''raio_pelvico''grau_espondilolistese'
        $linhas = [];
        for ($j = 0; $j < 6; $j++) {//6 entradas
            array_push($linhas, array());
        }
        $matriz = [];

        for ($i = 0; $i < $nr_amostras; $i++) {
            $k = $this->randonn();//mistura os valores

            $linhas[0] = $dados[$k]->incidencia_pelvica;
            $linhas[1] = $dados[$k]->inclinacao_pelvica;
            $linhas[2] = $dados[$k]->angulo_lordose_lombar;
            $linhas[3] = $dados[$k]->inclinacao_sacral;
            $linhas[4] = $dados[$k]->raio_pelvico;
            $linhas[5] = $dados[$k]->grau_espondilolistese;
            $linhas[6] = $dados[$k]->diagnostico;

            array_push($matriz,$linhas);
        }
        return $matriz;
    }
    private function randonn(){
        $k = rand(0, 209);
        // && ($k>270&& $k<310)
        while(($k >36 && $k <= 61) || ($k>150 && $k <= 209) || ($k>270)){
            $k = rand(0, 209);
        }
        echo $k."| ";
        return $k;
    }

}
