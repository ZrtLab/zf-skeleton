<?php

class App_View_Helper_CutTexto
        extends Zend_View_Helper_HtmlElement {

    public function CutTexto($value, $longitud = 32,$max = 0) {
        
        if(mb_strlen($value, 'utf-8') > $longitud){
            return mb_substr( $value , 0 , $longitud,'utf-8' ) . '..';
        }
        
        return $value;
        
    }

}