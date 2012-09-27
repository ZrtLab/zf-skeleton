<?php


/**
 * @author slovacus <slovacus@gmail.com>
 * @example path description
 */
class App_View_Helper_TitleLocations extends Zend_View_Helper_Abstract
{

    /**
     * 
     * @param App_Model_Ubicacion $ubicacion
     */
    public function TitleLocations($ubicacion)
    {

        if (!empty($ubicacion)) {
            $text = "";
            if ($ubicacion->state) {
                $text = $this->_checkRoute($ubicacion->instance);
                return ucwords($text);
            }
            return $ubicacion->name;
        }
        return "";
    }

    /**
     * 
     * @param App_Model_Ubicacion $ubicacion
     */
    private function _checkRoute($ubicacion)
    {
        $data = $ubicacion->getNameArray();
        return "Clasificados {$this->_deleteCharacter($data['departamento'])}";
    }

    /**
     * 
     * @param type $texto
     * @param type $search
     * @param type $replace
     * @return type
     */
    private function _deleteCharacter($texto, $search = "-", $replace = " ")
    {
        return strtr($texto, $search, $replace);
    }

}


