<?php
/**
 * Config object definition
 *
 * @category App
 * @package App_DI
 * @copyright company
 */
class App_DI_Definition_ConfigObject
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Config_Ini
     */
    public static function getInstance(){
        return new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
    }
}