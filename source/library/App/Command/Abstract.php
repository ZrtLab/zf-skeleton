<?php
/**
 * Observer
 *
 * @package default
 */
abstract class App_Command_Abstract{
    /**
     * Convenience method to run the command
     *
     * @param string $name
     * @param mixed $args
     * @return boolean
     */
    abstract public function onCommand($name, $args);
    
    /**
     * Init the translator
     *
     * @return void
     */
    public function __construct(){
        if(!Zend_Registry::isRegistered('Zend_Translate')){
            $bootstrap = Zend_Registry::get('Bootstrap');
            $bootstrap->bootstrap(array('Translator'));
        }
        
        $this->t = Zend_Registry::get('Zend_Translate');
    }
}