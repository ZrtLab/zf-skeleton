<?php
/**
 * Default application wide controller parent class
 *
 * @category App
 * @package App_Controller
 * @copyright company
 */

abstract class App_Controller extends Zend_Controller_Action
{
    /**
     * Store the available commands
     *
     * @var array
     */
    private $_commands = array();
    
    /**
     * Overrides init() from App_Controller
     * 
     * @access public
     * @return void
     */
    public function init()
    {
        parent::init();
        
        $this->t = Zend_Registry::get('Zend_Translate');
    }
    
    /**
     * Add a new command to the chain
     *
     * @param object $cmd
     * @return void
     */
    protected function _addCommand($cmd){
        if(is_object($cmd) && !array_key_exists(get_class($cmd), $this->_commands)){
            $this->_commands[get_class($cmd)] = $cmd;
        }
    }
    
    /**
     * Run a command through the command chain
     *
     * @param string $name
     * @param mixed $args
     * @return void
     */
    protected function _runCommand($name, $args){
        foreach($this->_commands as $cmd){
            if($result = $cmd->onCommand($name, $args)){
                return $result;
            }
        }
    }
    
    /**
     * Queries the Flag and Flipper and redirects the user to a different
     * page if he/her doesn't have the required permissions for
     * accessing the current page
     * 
     * @access protected
     * @return void
     */
    protected function _checkFlagFlippers(){
        $controllerName = Zend_Registry::get('controllerName');
        $actionName = Zend_Registry::get('actionName');
        
        $user = BaseUser::getSession();
        
        if(Zend_Registry::get('IS_DEVELOPMENT') && $controllerName != 'error'){
            $flagModel = new Flag();
            
            $flag = strtolower(CURRENT_MODULE) . '-' . $controllerName;
            
            if(!$flagModel->checkRegistered($flag, App_Inflector::camelCaseToDash($actionName))){
                $params = array(
                    'originalController' => $controllerName,
                    'originalAction' => $actionName
                );
                
                $this->_forward('flagflippers', 'error', NULL, $params);
                return;
            }
        }
        
        //Check the flag and flippers for ZFDebug
        if(!App_FlagFlippers_Manager::isAllowed($user->group->name, 'testing', 'zfdebug')){
            Zend_Controller_Front::getInstance()->unregisterPlugin('ZFDebug_Controller_Plugin_Debug');
        }
        
        if(!App_FlagFlippers_Manager::isAllowed($user->group->name, $controllerName, $actionName)){
            if(empty($user->id)){
                // the user is a guest, save the request and redirect him to
                // the login page
                $session = new Zend_Session_Namespace('FrontendRequest');
                $session->request = serialize($this->getRequest());
                $this->_redirect('/profile/login/');
            }else{
                $this->_redirect('/error/forbidden/');
            }
        }
    }
    
    /**
     * Convenience method to get the paginator
     *
     * @param mixed $array 
     * @return void
     */
    protected function _getPaginator($array){
        $paginator = Zend_Paginator::factory($array);
        $paginator->setCurrentPageNumber($this->_getPage());
        $paginator->setItemCountPerPage(App_DI_Container::get('ConfigObject')->paginator->items_per_page);
        
        return $paginator;
    }
}
