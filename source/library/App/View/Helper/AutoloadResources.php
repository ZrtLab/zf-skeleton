<?php
/**
 * Helper used in order to load js files
 *
 * @category App
 * @package App_View
 * @subpackage Helper
 * @copyright company
 */

class App_View_Helper_AutoloadResources extends Zend_View_Helper_Abstract{
    
    /**
     * Load the js files related with the current controller and action
     *
     * @return void
     */
    public function AutoloadResources(){
        $controllerName = Zend_Registry::get('controllerName');
        $actionName = Zend_Registry::get('actionName');
        
        if(CURRENT_MODULE == 'backoffice'){
            $file = ROOT_PATH . '/public/' . CURRENT_MODULE . '/js/autoload/' . $controllerName . '/' . strtolower(App_Inflector::camelCaseToDash($actionName)) . '.js';
        }else{
            $file = ROOT_PATH . '/public/' . CURRENT_MODULE . '/js/autoload/' . strtolower(App_Inflector::camelCaseToDash($controllerName)) . '.js';
        }
        
        if(file_exists($file)){
            if(CURRENT_MODULE == 'backoffice'){
                $file = $this->view->baseUrl() . '/js/autoload/' . $controllerName . '/' . strtolower(App_Inflector::camelCaseToDash($actionName)) . '.js';
                $this->view->headScript()->offsetSetFile(2, $file);
            }else{
                $file = $this->view->baseUrl() . '/js/autoload/' . strtolower(App_Inflector::camelCaseToDash($controllerName)) . '.js';
                $this->view->headScript()->appendFile($file);
            }
        }
    }
}