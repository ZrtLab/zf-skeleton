<?php


/**
 * @author slovacus <slovacus@gmail.com>
 */
class App_View_Helper_Locations extends Zend_View_Helper_Abstract
{

    /**
     *
     * @var App_Model_Ubicacion
     */
    protected $_ubicacion = null;

    /**
     *
     * @var Zend_Filter_Word_DashToSeparator
     */
    protected $_fwordDT = null;

    /**
     *
     * @var Ec_SiteManager
     */
    protected $_siteManager = null;

    public function __construct()
    {

        $this->_fwordDT = new Zend_Filter_Word_DashToSeparator();
        $this->_siteManager = Ec_SiteManager::getInstance();
    }

    /**
     * 
     * @todo upgrade code performance
     * @param type $listLocations
     * @param string $nivel
     * @return type
     */
    public function Locations($listLocations, $nivel = '')
    {
        $this->_ubicacion = $this->view->ubicacion->instance;
        return $this->_generateMenu($listLocations, $nivel = '');
    }

    /**
     * 
     * @param type $listLocations
     * @param type $nivel
     * @param bool $allAds
     * @return string
     */
    protected function _generateMenu($listLocations, $nivel = '', $allAds = true)
    {
        $html = "<ul id='filtradas' class='nivel{$nivel}' >";
        $html .= $this->_allAds($allAds);
        foreach ($listLocations as $key => $value) {
            if ($value['showMenu'] == 'true') {
                $param = array_key_exists('url', $value) ? $value['url'] : $key;
                $html.="<li>";
                $html .="<a href='{$this->view->url($this->_extractUrl($param),
                        'ubicacion', true)}'>";
                $html.= $this->_checkUrl($key, $value, $allAds);
                $html.="</a>";
                $html.="</li>";
                if (isset($value['nodos'])) {
                    $html.=$this->_generateMenu($value['nodos'], 1, FALSE);
                }
            }
        }
        $html .="</ul>";
        return $html;
    }

    /**
     * @todo upgrade code
     * @param string $key
     * @param array $value
     */
    private function _checkUrl($key, $value, $active = true)
    {
        $html = "";
        if (trim($value['url']) ==
            $this->_deleteFirstCaracter($this->_ubicacion->getNameUrl())) {
            $html.= '<b style="color: #222;" >';
        }
        $name = !empty($value['title']) ? $value['title'] : $key;

        $html.= ucwords($this->_fwordDT->filter($name));

        $html.= $this->_allContAds($value, $active);

        if (trim($value['url']) ==
            $this->_deleteFirstCaracter($this->_ubicacion->getNameUrl())) {
            $html.='</b>';
        }
        return $html;
    }

    /**
     * 
     * @todo upgrade of code
     * @param bool $active
     * @return string
     */
    private function _allAds($active = true)
    {
        $html = "";
        if ($this->view->ubicacion->state && $active) {
            $location = $this->_ubicacion->getNameArray();
            /**
             * @todo fix of code
             */
            $location['provincia'] = "";
            $location['distrito'] = "";
            $valor = $this->_ubicacion->getTotalAdsByLocation(
                $location
            );
            $html .= "<li>";
            $html .="<a href='{$this->view->url($this->_extractUrl($location),
                    'ubicacion', true)}'>";

            if (trim($location['departamento']) ==
                $this->_deleteFirstCaracter($this->_ubicacion->getNameUrl())) {
                $html.= '<b style="color: #222;" >';
            }
            $html .= "Todos ";
            $html .= sprintf(" (%s) ", $valor);

            if (trim($location['departamento']) ==
                $this->_deleteFirstCaracter($this->_ubicacion->getNameUrl())) {
                $html.='</b>';
            }
            $html .= "</a>";
            $html .= "</li>";
        }
        return $html;
    }

    private function _allContAds($value, $active = true)
    {
        $html = "";
        if (!$this->view->ubicacion->state && $active) {
            $html .= "({$value['avisos']})";
        }
        return $html;
    }

    /**
     * 
     * @param string $url
     * @todo mejorar codigo
     */
    private function _extractUrl($url)
    {
        if (is_array($url)) {
            return $url;
        }

        $locations = array('departamento', 'provincia', 'distrito');
        $extractUrl = explode('/', $url);
        $tmpArray = array();
        foreach ($extractUrl as $key => $value) {
            $tmpArray = array_merge(array($locations[$key] => $value), $tmpArray);
        }

        return $tmpArray;
    }

    /**
     * @author slovacus <slovacus@gmail.com>
     * @param string $data
     */
    private function _deleteFirstCaracter($data)
    {
        return trim(substr($data, 1));
    }

}