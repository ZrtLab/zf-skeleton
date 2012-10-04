<?php
/**
 * Default parent controller for all the frontend controllers
 *
 * @package App_Controller
 * @copyright company
 */

abstract class App_Frontend_Controller extends App_Controller
{
    //Unique place to store the namespace keys
    protected $_requestNamespaceKey = 'FrontendRequest';
    
    //Store the namespaces
    protected $_session = array();
    
    /**
     * Overrides preDispatch() from App_Controller
     * Fetch and prepare the cart system in the namespace
     * 
     * @access public
     * @return void
     */
    public function preDispatch(){
        parent::preDispatch();
        
        $controllerName = $this->getRequest()->getControllerName();
        $actionName = $this->getRequest()->getActionName();
        
        Zend_Registry::set('controllerName', $controllerName);
        Zend_Registry::set('actionName', $actionName);
        Zend_Registry::set('Zend_Request', $this->getRequest());
        // check the Flag and Flipper
        $this->_checkFlagFlippers();
        
        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/jquery-ui-i18n.js');
    }
    
    /**
     * Overrides postDispatch() from App_Controller
     * 
     * @access public
     * @return void
     */
    public function postDispatch(){
        parent::postDispatch();
        
        if(isset($this->title)){
            $this->view->headTitle($this->title);
        }else{
            $this->view->headTitle('');
        }
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
    
    /**
     * Get the session namespace we're using
     *
     * @return Zend_Session_Namespace
     */
    protected function _getSessionNamespace($key){
        if(!array_key_exists($key, $this->_session)){
            $this->_session[$key] = new Zend_Session_Namespace($key);
        }
        
        return $this->_session[$key];
    }
}