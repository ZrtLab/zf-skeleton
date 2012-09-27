<?php


/**
 * 
 */
class App_View_Helper_ImagenLadoMayor extends Zend_View_Helper_HtmlElement
{

    public function ImagenLadoMayor($urlImagen)
    {
        $width = 0;
        $height = 0;
        @list($width, $height, $type, $attr) = getimagesize($urlImagen);
        //echo "<br>".$width." ".$height;
        if (($width / 135) < ($height / 86)) {
            return 'height="86px"';
        }
        return 'width="135px"';
    }

}