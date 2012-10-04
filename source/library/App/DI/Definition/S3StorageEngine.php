<?php
/**
 * S3 Storage Engine object definition
 *
 * @category App
 * @package App_DI
 * @copyright company
 */
class App_DI_Definition_S3StorageEngine
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $config = App_DI_Container::get('ConfigObject');
        
        $storage = Zend_Cloud_StorageService_Factory::getAdapter(
            array(
                Zend_Cloud_StorageService_Factory::STORAGE_ADAPTER_KEY => 'Zend_Cloud_StorageService_Adapter_S3',
                Zend_Cloud_StorageService_Adapter_S3::AWS_ACCESS_KEY => $config->amazon->aws_access_key,
                Zend_Cloud_StorageService_Adapter_S3::AWS_SECRET_KEY => $config->amazon->aws_private_key,
            )
        );
        
        return $storage;
    }
}