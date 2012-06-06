<?php

/**
 *@author slovacus@gmail.com
 *  
 */
class Zrt_Tools_Text
{

    public static function getShortText($texto, $longitud)
    {
        $cadena = substr($texto, 0, $longitud);
        $posicion = strripos($cadena, ' ');
        return substr($cadena, 0, $posicion);
    }

    public static function getCutText($texto, $longitud)
    {
        $cadena = "";
        $cadena = substr($texto, 0, (strlen($texto) - $longitud));
        return $cadena;
    }

    public static function getJoinText($parameters = array(), $glue = " ")
    {

        return implode($glue, $parameters);
    }

    public static function addJsonComillas($json = "")
    {

        return preg_replace("/([a-zA-Z0-9_]+?):/", "\"$1\":", $json);
    }

    public static function cleanTexto($texto)
    {

        return preg_replace("/[^0-9.\+]/", "", $texto);
    }

    public static function getIn(array $data)
    {
        return str_replace("'", "", implode(',', $data));
    }

    public static function generarPassword($limit = 6)
    {

        $abc = array('A', 'B', 'C', 'D', 'E',
            'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U',
            'V', 'W', 'X', 'Y', 'Z', '0', '1',
            '2', '3', '4', '5', '6', '7', '9');
        $pass = "";
        for ($x = 0; $x <= $limit; $x++) {
            $pass .= strtolower($abc[mt_rand(0, count($abc) - 1)]);
        }

        return $pass;
    }

}