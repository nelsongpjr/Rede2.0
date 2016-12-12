<?php
/**
 * Created by PhpStorm.
 * User: junio
 * Date: 08/11/2016
 * Time: 17:04
 */

namespace App;

use App\dados;
use App\teste;

class RedeNeural
{

    private $TAXA_APRENDIZADO = 0.03;
    private $conexoes_primeira_camada= [array()];
    private $conexoes_segunda_camada = [array()];
    private $nr_neuronios_camada_escondida;
    private $nr_neuronios_entrada;
    private $nr_neuronios_saida;
    private $epocas = 0;
    private $nr_amostras;
    private $bias1 = [];
    private $bias2= [];
    private $teste;
    public function __construct($entradas, $nr_neuronios_entrada, $amostras){

        $this->nr_amostras = $amostras;
        //qunatidade de dados para treinamento
        $this->nr_neuronios_entrada = $nr_neuronios_entrada;
        $this->nr_neuronios_camada_escondida = 5; //quantidades de neuronios na segunda camada
        $this->nr_neuronios_saida = 3;
        echo "Amostras: $this->nr_amostras <br>";
        echo "Entradas: $this->nr_neuronios_entrada <br>";
        echo  "Camada escondida: $this->nr_neuronios_camada_escondida <br>";
        echo "Camada de saída: $this->nr_neuronios_saida <br>";

        $this->iniciar_conexoes();

        $this->treinar($entradas);
    }

    private function treinar($entradas){
        $erro_medio_quadratico = 1;
        $erro_quadratico = 0;
        $erro_minimo = 0.04;
        $erro_total = [];
        $delta_bias1 = [];
        $delta_bias2 = [];
        $z_in = [];
        $z = [];
        $delta_k = [];
        $y_in = [];
        $delta_w = $this->conexoes_segunda_camada;
        $delta_in = [];
        $delta_v = [];
        $delta_j = [];
        $y = [];
        $stagnado = 0;
        while (($this->epocas< 10000) && ($erro_medio_quadratico > $erro_minimo) &&($stagnado  == 0)){
            $this->epocas++;
            $erro_medio_quadratico = 0;
            echo "<br> Epoca: $this->epocas<br>";
            for ($p = 0; $p< $this->nr_amostras; $p++){
                $erro_quadratico = 0;
                $saida_esperada = $this->trata_resultados($entradas, $p);
                //echo "Amostra: $p | Errom Médio: $erro_medio_quadratico<br>";
                for ($k = 0; $k < $this->nr_neuronios_saida; $k++){
                    $soma_y_in = 0;
                    for ($j = 0 ; $j < $this->nr_neuronios_camada_escondida; $j++){
                        $soma_z_in = 0;
                        for ($i = 0; $i < $this->nr_neuronios_entrada; $i++){
                            $soma_z_in =$soma_z_in + $entradas[$p][$i]* $this->conexoes_primeira_camada[$i][$j];
                        }
                        $z_in[$j] = $this->bias1[$j] + $soma_z_in;
                        $z[$j] = (2/(1+exp(-$z_in[$j])))-1;
                        $soma_y_in =$soma_y_in + $z[$j] * $this->conexoes_segunda_camada[$j][$k];
                    }
                    $y_in[$k] = $this->bias2[$k] + $soma_y_in;
                    $y[$k] = (2/(1+exp(-$y_in[$k])))-1;
                    $delta_k[$k] = ($saida_esperada[$k] - $y[$k]) * 0.5 * (1 + $y[$k]) * (1 - $y[$k]);
                    $erro_quadratico = $erro_quadratico + abs(0.5 * pow($saida_esperada[$k] - $y[$k], 2));
                    for ($j = 0; $j < $this->nr_neuronios_camada_escondida; $j++){
                        $delta_w[$j][$k] = $this->TAXA_APRENDIZADO * $delta_k[$k] * $z[$j];
                    }

                    $delta_bias2[$k]= $this->TAXA_APRENDIZADO * $delta_k[$k];
                    if(($z[0] != 1 || $z[0] != -1)|| ($z[1] != 1 || $z[1] != -1)|| ($z[2] != 1 || $z[2] != -1)|| ($z[3] != 1 || $z[3] != -1)|| ($z[4] != 1 || $z[4] != -1)){
                        $stagnado = 1;
                    }
                }
                for ($j = 0; $j < $this->nr_neuronios_camada_escondida; $j++){
                    $soma_delta_in = 0;
                    for ($k = 0; $k< $this->nr_neuronios_saida; $k++){
                        $soma_delta_in = $soma_delta_in + $delta_k[$k]*$this->conexoes_segunda_camada[$j][$k];
                    }
                    $delta_in[$j] = $soma_delta_in;
                    $delta_j[$j] = $delta_in[$j] * 0.5 * (1 + $z[$j]) * (1 - $z[$j]);
                    for ($i = 0; $i< $this->nr_neuronios_entrada; $i++){
                        $delta_v[$i][$j] = $this->TAXA_APRENDIZADO * $delta_j[$j]*$entradas[$p][$i];
                    }
                    $delta_bias1[$j] = $this->TAXA_APRENDIZADO *$delta_j[$j];
                }
                for ($k = 0; $k< $this->nr_neuronios_saida; $k++){
                    $this->bias2[$k] = $this->bias2[$k] + $delta_bias2[$k];
                    for ($j = 0; $j < $this->nr_neuronios_camada_escondida; $j++){
                        $this->conexoes_segunda_camada[$j][$k] = $this->conexoes_segunda_camada[$j][$k] +
                            $delta_w[$j][$k];

                        for ($i = 0; $i < $this->nr_neuronios_entrada; $i++){
                            $this->conexoes_primeira_camada[$i][$j] =$this->conexoes_primeira_camada[$i][$j] + $delta_v[$i][$j];
                        }
                        $this->bias1[$j] =$this->bias1[$j] + $delta_bias1[$j];
                    }
                }

                if ($erro_quadratico/$this->nr_neuronios_saida > $erro_medio_quadratico){
                    $erro_medio_quadratico = $erro_quadratico/$this->nr_neuronios_saida;
                }
                array_push($erro_total, $erro_medio_quadratico);
                $this->imprimir($z,$y, $erro_medio_quadratico,$saida_esperada);
            }

        }
    }


    //Nesta rede neural a ultima coluna sempre é a representação do bias
    private function iniciar_conexoes(){
        //inicia valores das arestas da primeira camada

        for ($i = 0; $i < $this->nr_neuronios_entrada; $i++) {
            for ($j = 0; $j < $this->nr_neuronios_camada_escondida; $j++) {
                $this->conexoes_primeira_camada[$i][$j] = $this->randon();//nova linha
                echo $this->conexoes_primeira_camada[$i][$j] . "| ";
            }
            echo "<br>";
            array_push($this->conexoes_primeira_camada, array());//nova coluna
        }
        echo "<br>";
        echo "<br>";
        for($i = 0; $i < $this->nr_neuronios_camada_escondida; $i++){
            $this->bias1[$i] = $this->randon();
            echo $this->bias1[$i]."| ";
        }

        echo "<br>";
        echo "<br>--------bias 2---------------";
        for ($j = 0; $j < $this->nr_neuronios_saida; $j++) {
            $this->bias2[$j] = $this->randon();
            echo $this->bias2[$j]."| ";
        }
        echo "<br>";echo "<br>";

        for ($i = 0; $i < $this->nr_neuronios_camada_escondida; $i++) {
            for ($j = 0; $j < $this->nr_neuronios_saida; $j++) {
                $this->conexoes_segunda_camada[$i][$j] = $this->randon();
                echo $this->conexoes_segunda_camada[$i][$j] . "| ";
            }
            echo "<br>";
            array_push($this->conexoes_segunda_camada, array());//nova coluna
        }
    }

    //adiciona os dados do objeto em um array simples

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
    private function imprimir( $camada_escondida, $saida, $erro_medio, $saidas_esperadas){

        echo '<br>---------- arestas entradas x camada escondida -----------<br>';
        for($j = 0; $j < $this->nr_neuronios_entrada; $j++){
            for ($k= 0;  $k < $this->nr_neuronios_camada_escondida; $k++){
                echo  $this->conexoes_primeira_camada[$j][$k]."| ";
            }
            echo "<br>";
        }
        echo '<br>---------- camada escondida -----------<br>';
        for($n = 0; $n < $this->nr_neuronios_camada_escondida; $n++){
            echo $camada_escondida[$n]."| ";
        }
        echo "<br>";
        echo '<br>---------- arestas camada escondida x arestas entradas -----------<br>';
        for($l = 0; $l < $this->nr_neuronios_camada_escondida; $l++){
            for($m = 0; $m < $this->nr_neuronios_saida; $m++){
                echo $this->conexoes_segunda_camada[$l][$m]. "| ";
            }
            echo "<br>";
        }
        /*| $saida[1] | $saida[2]*/ /*| $erros[1] | $erros[2]*/
        echo "<br> Saidas<br>";
        for ($i = 0; $i< count($saida); $i++) {
            echo $saida[$i]."| ";
        }

        echo "<br>Saídas esperadas: $saidas_esperadas[0] | $saidas_esperadas[1] | $saidas_esperadas[2] <br>";
        echo "<br> Erro Médio> $erro_medio";
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

    /**
     * @return array
     */
    public function getBias1()
    {
        return $this->bias1;
    }

    /**
     * @param array $bias1
     */
    public function setBias1($bias1)
    {
        $this->bias1 = $bias1;
    }

    /**
     * @return array
     */
    public function getBias2()
    {
        return $this->bias2;
    }

    /**
     * @param array $bias2
     */
    public function setBias2($bias2)
    {
        $this->bias2 = $bias2;
    }

    /**
     * @return int
     */
    public function getNrNeuroniosCamadaEscondida()
    {
        return $this->nr_neuronios_camada_escondida;
    }

    /**
     * @return int
     */
    public function getNrNeuroniosEntrada()
    {
        return $this->nr_neuronios_entrada;
    }

    /**
     * @return int
     */
    public function getNrNeuroniosSaida()
    {
        return $this->nr_neuronios_saida;
    }



}