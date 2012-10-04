<?php
/**
 * Bitly client List object definition
 *
 * @category Zrt
 * @package Zrt_DI
 * @copyright company
 */
class Zrt_DI_Definition_BitlyShortener
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $bitlyService = new Zrt_Service_ShortUrl_BitLy(
            Zrt_DI_Container::get('ConfigObject')->bitly->username,
            Zrt_DI_Container::get('ConfigObject')->bitly->api_key
        );
        
        return $bitlyService;
    }
}