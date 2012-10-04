<?php
/**
 * Various string utilities
 *
 * @category App
 * @package App_Inflector
 * @copyright company
 */

class App_Inflector
{
    /**
     * Converts a controller's name to a resource name
     * 
     * Ex: MyExampleController => my-example
     * 
     * @param string $string 
     * @access public
     * @return string
     */
    public static function convertControllerName($string){
        $string = substr($string, 0, -10);
        return self::camelCaseToDash($string);
    }
    
    /**
     * Converts an action's name to a privilege name
     * 
     * Ex: myExampleAction => my-example
     * 
     * @param string $string 
     * @access public
     * @return string
     */
    public static function convertActionName($string){
        $string = substr($string, 0, -6);
        return self::camelCaseToDash($string);
    }
    
    /**
     * Convers a camelCasedString to lower cased string with
     * words separated by dashes
     *
     * Ex: myCamelCasedString => my-camel-cased-string
     * 
     * @param string $string 
     * @access public
     * @return string
     */
    public static function camelCaseToDash($string){
        $string = preg_replace('/([A-Z]+)([A-Z])/','$1-$2', $string);
        $string = preg_replace('/([a-z])([A-Z])/', '$1-$2', $string);
        
        return strtolower($string);
    }
    
    /**
     * Converts a camelCasedString to lower cased string with
     * words separated by underscores
     *
     * Ex: myCamelCasedString => my_camel_cased_string
     * 
     * @param string $string 
     * @access public
     * @return string
     */
    public static function camelCaseToUnderscore($string){
        $string = preg_replace('/([A-Z]+)([A-Z])/','$1_$2', $string);
        $string = preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);
        
        return strtolower($string);
    }
    
    /**
     * Converts a underscored string to camelCasedString
     * 
     * Ex: my_camel_cased_string => myCamelCasedString
     *
     * @param string $string 
     * @access public
     * @return string
     */
    public static function unserscoreToCamelCase($string){
        $words = explode('_', strtolower($string));
        
        $string = '';
        foreach ($words as $word) {
            $string .= ucfirst(trim($word));
        }
        
        return $string;
    }
    
    /**
     * Converts a dashed or underscored string to a humanly readable
     * one. Ex:
     * my-action-controller => My action controller
     * 
     * @param mixed $string 
     * @access public
     * @return void
     */
    public static function humanize($string){
        return ucfirst(str_replace(array('_', '-'), ' ', $string));
    }
    
    /**
     * Computes a slug of the given string
     * 
     * @param mixed $string 
     * @access public
     * @return string
     */
    public static function slug($string){
        $clean = str_replace(' ', '-', $string);
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $clean);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+-]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        
        return $clean;
    }
}