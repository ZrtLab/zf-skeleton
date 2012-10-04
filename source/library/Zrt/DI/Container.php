<?php
/**
 * Dependency Injector Container
 *
 * @category Zrt
 * @package Zrt_DI
 * @copyright company
 */
class Zrt_DI_Container
{
    /**
     * Store the instances of the objects
     *
     * @var array
     */
    protected static $_instances = array();
    
    /**
     * Method to be able to retrieve the objects stored in the container,
     * if the object is not stored we try to find the object definition and
     * we create and store the object.
     *
     * @param string $className
     * @return mixed
     */
    public static function get($className){
        $key = trim(strtolower($className));
        
        if(isset(self::$_instances[$key])){
            return self::$_instances[$key];
        }
        
        $objectDefinition = sprintf('Zrt_DI_Definition_%s', $className);
        
        return self::$_instances[$key] = $objectDefinition::getInstance();
    }
}