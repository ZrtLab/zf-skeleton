<?php
/**
 * Default parent controller for all the backoffice controllers
 *
 * @category App
 * @package App_Backoffice
 * @copyright company
 */

abstract class App_Backoffice_Controller extends App_Controller
{
    /**
     * Holds the title for this controller
     * 
     * @var string
     * @access public
     */
    public $title = '';
    
    /**
     * Overrides init() from App_Controller
     * 
     * @access public
     * @return void
     */
    public function init(){
        parent::init();
    }
    
    /**
     * Overrides preDispatch() from App_Controller
     * 
     * @access public
     * @return void
     */
    public function preDispatch(){
        parent::preDispatch();
        
        $controllerName = $this->getRequest()->getControllerName();
        $actionName = $this->getRequest()->getActionName();
        
        $this->view->headScript()->prependFile($this->view->baseUrl() . '/js/jquery.min.js');
        
        Zend_Registry::set('controllerName', $controllerName);
        Zend_Registry::set('actionName', $actionName);
        // check the Flag and Flippers
        $this->_checkFlagFlippers();
    }
    
    /**
     * Overrides postDispatch() from App_Controller
     * 
     * @access public
     * @return void
     */
    public function postDispatch(){
        parent::postDispatch();
        
        $this->_helper->layout()->getView()->headTitle($this->title);
    }
    
    /**
     * Gets the current page. Convenience method for using
     * paginators
     * 
     * @param int $default 
     * @access protected
     * @return int
     */
    protected function _getPage($default = 1){
        $page = $this->_getParam('page');
        if (!$page || !is_numeric($page)) {
            return $default;
        }
        
        return $page;
    }
}