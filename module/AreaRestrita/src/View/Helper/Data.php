<?php

namespace AreaRestrita\View\Helper;

class Data
{
    public function __construct()
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese');
//        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
//        setlocale(LC_ALL, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
    }

    public function dataPorExtenso($datetime)
    {
        // TODO verificar o porque do setlocale não funciona e alterar a função para
        // return strftime('%A, %d de %B de %Y', strtotime($datetime));

        if (!empty($datetime)) {
            $data = explode("-", (string) $datetime);
            $dianum = explode(" ", $data[2]);
            $dia = date($dianum[0]);
            $mes = date($data[1]);
            $ano = date($data[0]);
            $semana = date("w", mktime(0, 0, 0, $mes, $dia, $ano));

            switch ($mes) {
                case 1:
                    $mes = "Janeiro";
                    break;
                case 2:
                    $mes = "Fevereiro";
                    break;
                case 3:
                    $mes = "Março";
                    break;
                case 4:
                    $mes = "Abril";
                    break;
                case 5:
                    $mes = "Maio";
                    break;
                case 6:
                    $mes = "Junho";
                    break;
                case 7:
                    $mes = "Julho";
                    break;
                case 8:
                    $mes = "Agosto";
                    break;
                case 9:
                    $mes = "Setembro";
                    break;
                case 10:
                    $mes = "Outubro";
                    break;
                case 11:
                    $mes = "Novembro";
                    break;
                case 12:
                    $mes = "Dezembro";
                    break;
            }

            switch ($semana) {
                case 0:
                    $semana = "Domingo";
                    break;
                case 1:
                    $semana = "Segunda-Feira";
                    break;
                case 2:
                    $semana = "Terça-Feira";
                    break;
                case 3:
                    $semana = "Quarta-Feira";
                    break;
                case 4:
                    $semana = "Quinta-Feira";
                    break;
                case 5:
                    $semana = "Sexta-Feira";
                    break;
                case 6:
                    $semana = "Sábado";
                    break;
            }

            //Retorna a data por extenso
            return "$semana, $dia de $mes de $ano";
        } else {
            return $datetime;
        }
    }

    public function converterDataBR($data): string
    {
        return date("d/m/Y", strtotime((string) $data));
    }

    public function converterDataHoraBR($data): string
    {
        return date("d/m/Y H:i:s", strtotime((string) $data));
    }
}
