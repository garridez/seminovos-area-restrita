<?php


namespace AreaRestrita\View\Helper;


class Data
{
    public function __construct()
    {
        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');
    }

    public function dataPorExtenso()
    {
        echo strftime('%A, %d de %B de %Y', strtotime('today'));
    }

    public function converterDataBR ($data)
    {
        return date("d/m/Y", strtotime($data));
    }

    public function converterDataHoraBR ($data)
    {
        return date("d/m/Y H:i:s", strtotime($data));
    }
}