<?php
/**
 * Renders the main menu for the site. 
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright company
 */

class App_View_Helper_RenderSubMenu extends Zend_View_Helper_Abstract
{
    /**
     * Template for the links
     * 
     * @var string
     * @access protected
     */
    protected $_linkTemplate = '<li><a href="%1$s" title="%2$s">%2$s</a></li>';
    
    /**
     * Template for the selected link
     * 
     * @var string
     * @access protected
     */
    protected $_linkSelectedTemplate = '<li class="selected"><a href="%1$s" title="%2$s">%2$s</a></li>';
    
    /**
     * Convenience method
     * call $this->renderMenu() in the view to access 
     * the helper
     *
     * @access public
     * @return string
     */
    public function renderSubMenu(){
        $navigation = App_Backoffice_Navigation::getInstance()->getNavigation();
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        
        //Extract the submenu we have to show
        foreach($navigation as $tab){
            if(isset($tab['main']['active']) && $tab['main']['active']){
                $pages = $tab['pages'];
                break;
            }
        }
        
        $menu = array();
        if(!empty($pages)){
            foreach($pages as $page){
                if(isset($page['action'])){
                    $url = $baseUrl . '/' . $page['controller'] . '/' . $page['action'];
                }else{
                    $url = $baseUrl . '/' . $page['controller'];
                }

                if (isset($page['active']) && $page['active']) {
                    $li = sprintf($this->_linkSelectedTemplate, $url, $page['label']);
                } else {
                    $li = sprintf($this->_linkTemplate, $url, $page['label']);
                }

                $menu[] = $li;
            }
        }
        
        $xhtml = '<ul class="prefix_1 clearfix">' . PHP_EOL . implode(PHP_EOL, $menu) . PHP_EOL . '</ul>';
        
        return $xhtml;
    }
}