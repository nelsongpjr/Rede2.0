<?php
/**
 * Created by PhpStorm.
 * User: junio
 * Date: 08/11/2016
 * Time: 17:04
 */

namespace App;

use App\dados;
use App\RedeNeural;

class teste{

    private $conexoes_primeira_camada;
    private $conexoes_segunda_camada;
    private $bias1;
    private $bias2;
    private $nr_neuronios_camada_escondida;
    private $nr_neuronios_entrada;
    private $nr_neuronios_saida;
    private $nr_amostras;
    public function __construct(RedeNeural $rede){
        $this->conexoes_primeira_camada = $rede->getConexoesPrimeiraCamada();
        $this->conexoes_segunda_camada = $rede->getConexoesSegundaCamada();
        $this->bias2 = $rede->getBias2();
        $this->bias1= $rede->getBias2();
        $this->nr_neuronios_entrada = $rede->getNrNeuroniosEntrada();
        $this->nr_neuronios_saida = $rede->getNrNeuroniosSaida();
        $this->nr_neuronios_saida = $rede->getNrNeuroniosSaida();
    }

    public function testar($entradas){
        $erro_medio_quadratico = 1;
        $z_in = [];
        $z = [];
        $y_in = [];
        $fator_certeza = 0.8;
        for ($p = 0; $p< $this->nr_amostras; $p++) {
            echo "Amostra: $p | Errom Médio: $erro_medio_quadratico<br>";
            $saida_esperada = $this->trata_resultados($entradas, $p);
            for ($k = 0; $k < $this->nr_neuronios_saida; $k++) {
                $soma_y_in = 0;
                for ($j = 0; $j < $this->nr_neuronios_camada_escondida; $j++) {
                    $soma_z_in = 0;
                    for ($i = 0; $i < $this->nr_neuronios_entrada; $i++) {
                        $soma_z_in = $soma_z_in + $entradas[$p][$i] * $this->conexoes_primeira_camada[$i][$j];
                    }
                    $z_in[$j] = $this->bias1[$j] + $soma_z_in;
                    $z[$j] = (2 / (1 + exp(-$z_in[$j]))) - 1;

                    $soma_y_in = $soma_y_in + $z[$j] * $this->conexoes_segunda_camada[$j][$k];
                }
                $y_in[$k] = $this->bias2[$k] + $soma_y_in;

                $y[$k] = (2 / (1 + exp(-$y_in[$k]))) - 1;
                if ($y[$k]> $fator_certeza && $saida_esperada[$k]){
                    echo "Acertou";
                }elseif ($y[$k] < (-$fator_certeza) && $saida_esperada[$k]< 0){
                    echo "Acertou";
                }else{
                    echo "Errou";
                }
            }
        }
    }


    //adiciona os dados do objeto em um array simples
    private function trata_entradas($dados){
        $linhas = [];
        //'incidencia_pelvica''inclinacao_pelvica''angulo_lordose_lombar''inclinacao_sacral''raio_pelvico''grau_espondilolistese'
        for ($j = 0; $j < $this->nr_neuronios_entrada; $j++) {//6 entradas
            array_push($linhas, array());
        }
        $matriz = [];

        for ($i = 0; $i < $this->nr_amostras; $i++) {
            $k = rand(0, 309);

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

    private function trata_resultados($DB, $i){
        $array = [];
        $str = $DB[$i][6];
        if (strcmp($str[0], "N") == 0) {
            $array[0] = 1;
            $array[1] = -1;
            $array[2] = -1;
        } elseif (strcmp($str[0], "D") == 0) {
            $array[0] = -1;
            $array[1] = 1;
            $array[2] = -1;
        } elseif (strcmp($str[0], "S") == 0) {
            $array[0] = -1;
            $array[1] = -1;
            $array[2] = 1;
        }else{
            echo "erro trata_resultados";
        }
        return $array;
    }


    private function randon(){
        //gera um numero aleatório entre -0,5 e +0,5
        $num = 0;
        while ($num == 0) {
            $sinal = rand(0, 1);
            if ($sinal == 0) {
                $num = (rand(0, 5000) / 10000) * -1;
            } else {
                $num = (rand(0, 5000) / 10000);
            }
        }
        return $num;
    }



    /**
     * @return array
     */
    public function getConexoesSegundaCamada()
    {
        return $this->conexoes_segunda_camada;
    }

    /**
     * @param array $conexoes_segunda_camada
     */
    public function setConexoesSegundaCamada($conexoes_segunda_camada)
    {
        $this->conexoes_segunda_camada = $conexoes_segunda_camada;
    }

    /**
     * @return array
     */
    public function getConexoesPrimeiraCamada()
    {
        return $this->conexoes_primeira_camada;
    }

    /**
     * @param array $conexoes_primeira_camada
     */
    public function setConexoesPrimeiraCamada($conexoes_primeira_camada)
    {
        $this->conexoes_primeira_camada = $conexoes_primeira_camada;
    }



}