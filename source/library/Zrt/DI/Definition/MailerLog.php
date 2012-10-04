<?php
/**
 * Mailer Log writer object definition
 *
 * @category Zrt
 * @package Zrt_DI
 * @copyright company
 */
class Zrt_DI_Definition_MailerLog
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $path = realpath(APPLICATION_PATH . '/../logs/' . CURRENT_MODULE . '/mailer.log');
        $logger = new Zend_Log();
        $logger->addWriter(new Zend_Log_Writer_Stream($path));
        
        return $logger;
    }
}