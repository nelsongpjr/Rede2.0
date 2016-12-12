<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RedeNeural;
use App\dados;
class controllerIndex extends Controller
{
    public function index(){
        $DB2 = dados::where([['id','>','211'],['id','<','270']]);
        $DB1 = dados::where([['id','>','61'],['id','<','150']]);
        $DB = dados::where('id','<','36')->union($DB1)->union($DB2);
        set_time_limit(10000);
        $nr_amostras = $DB->count();
        $entradas = $this->trata_entradas($DB, $nr_amostras );//caso for utilizar este codigo para outros problemas alterar essa função
        $nr_neuronios_entrada = count($entradas[0])-1;//quantidade de entradas
        $nr_amostras_tratadas = count($entradas);
        $rede = new RedeNeural($entradas, $nr_neuronios_entrada, $nr_amostras_tratadas);
    }

    private function trata_entradas($entradas, $nr_amostras){
        $linhas = [];
        $dados = [];
        for($i = 0, $j = 0; $i < $nr_amostras; $i++, $j++){
            $dados[$j] = $entradas[$i];
            if($i == 36){
                $i = 61;
                echo "quebra";
            }elseif ($i == 150){
                $i = 211;
                echo "quebra";
            }elseif ($i == 270){
                echo "quebra";
                break;
            }
        }

        //'incidencia_pelvica''inclinacao_pelvica''angulo_lordose_lombar''inclinacao_sacral''raio_pelvico''grau_espondilolistese'
        for ($j = 0; $j < 6; $j++) {//6 entradas
            array_push($linhas, array());
        }
        $matriz = [];

        for ($i = 0; $i < count($dados); $i++) {
            $k = $this->randonn();

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
