<?php
/**
 * Cache Manager object definition
 *
 * @category App
 * @package App_DI
 * @copyright company
 */
class App_DI_Definition_CacheManager
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $manager = new Zend_Cache_Manager();
        
        //Add the templates to the manager
        foreach (App_DI_Container::get('ConfigObject')->cache->toArray() as $k => $v) {
            $manager->setCacheTemplate($k, $v);
        }
        
        return $manager;
    }
}