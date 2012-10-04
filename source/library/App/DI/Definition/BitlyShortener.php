<?php
/**
 * Bitly client List object definition
 *
 * @category App
 * @package App_DI
 * @copyright company
 */
class App_DI_Definition_BitlyShortener
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $bitlyService = new App_Service_ShortUrl_BitLy(
            App_DI_Container::get('ConfigObject')->bitly->username,
            App_DI_Container::get('ConfigObject')->bitly->api_key
        );
        
        return $bitlyService;
    }
}