<?php

/**
 * Description of MenuFooter
 *
 * @author slovacus
 */
class App_View_Helper_MenuFooter extends Zend_View_Helper_Abstract
{

    /**
     *
     * @return string 
     */
    public function MenuFooter()
    {
        $ubicacion = Zend_Controller_Front::getInstance()->getRequest()->getParam('ubicacion',
                'todos');

        $texto = "<ul class=\"menu-footer\">";
        foreach (Ec_SiteManager::getInstance()->listar() as $site) {
            $class = 'Ec_Site_' . ucfirst($site);
            $mUbicacion = new App_Model_Ubicacion($ubicacion);
            $site = new $class($ubicacion);
            if ($mUbicacion->checkExist()) {
                $tot = $site->getTotalAvisosSite();
                $texto.= sprintf(
                        "<li><a target='_blank' title='' href=%s>%s</a>%s</li>",
                        $site->getUbigeoLink(), $site->title,
                        $tot>0?' ('.$tot.')':''
                );
            } else {
                $texto.= sprintf(
                        "<li><a target='_blank' title='' href=%s>%s</a> (%s)</li>",
                        $site->url, $site->title, $site->getTotalAvisosSite()
                );
            }
        }
        $texto.="</ul>";

        return $texto;
    }

}
