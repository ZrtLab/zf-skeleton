<?php
/**
 * Gearman Client object definition
 *
 * @category Zrt
 * @package Zrt_DI
 * @copyright company
 */
class Zrt_DI_Definition_GearmanClient
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $config = Zrt_DI_Container::get('ConfigObject');
        $gearmanClient = new GearmanClient();
        
        if (!empty($config->gearman->servers)) {
            $gearmanClient->addServers($config->gearman->servers->toArray());
        } else {
            $gearmanClient->addServer();
        }
        
        return $gearmanClient;
    }
}