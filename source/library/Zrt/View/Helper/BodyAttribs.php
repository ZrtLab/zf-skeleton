<?php

/**
 * Description of BodyAttribs
 *
 */
class App_View_Helper_BodyAttribs extends Zend_View_Helper_Abstract
{

    /**
     *
     * @return string 
     */
    public function BodyAttribs()
    {

        /**
         *
         *  @todo Mejorar este codigo 
         */
        $router = Zend_Controller_Front::getInstance()->getRouter()->getCurrentRoute();
        if ($router instanceof Zend_Controller_Router_Route) {
            $vars = $router->getVariables();
            if (is_array($vars)) {
                if ($vars[0] == 'ubicacion') {
            $req = Zend_Controller_Front::getInstance()->getRequest();
            $mUbicacion = new App_Model_Ubicacion($req->getParam('ubicacion'));
            if (!$mUbicacion->checkExist()) {
                return ' class="p404"';
            }
        }
            }
        }
        return "";
    }

}
