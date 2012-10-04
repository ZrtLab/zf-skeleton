<?php
/**
 * Cache Manager object definition
 *
 * @category Zrt
 * @package Zrt_DI
 * @copyright company
 */
class Zrt_DI_Definition_CacheManager
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $manager = new Zend_Cache_Manager();
        
        //Add the templates to the manager
        foreach (Zrt_DI_Container::get('ConfigObject')->cache->toArray() as $k => $v) {
            $manager->setCacheTemplate($k, $v);
        }
        
        return $manager;
    }
}