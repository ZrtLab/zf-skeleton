<?php


/**
 * @author slovacus <slovacus@gmail.com>
 */
class App_View_Helper_ShowMap extends Zend_View_Helper_Abstract
{

    public function ShowMap($ubicacion)
    {
        $html = "";

        if ($ubicacion->state) {
            $html = "<div class='view-map'>";
            $html .= "<a title = 'Ver mapa' href = '/'>Ver mapa</a>";
            $html .= "<span class = 'map'></span>";
            $html .= "</div>";
        }

        echo $html;
    }

}