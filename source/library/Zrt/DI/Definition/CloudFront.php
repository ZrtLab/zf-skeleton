<?php
/**
 * CloudFront Manager object definition
 *
 * @category Zrt
 * @package Zrt_DI
 * @copyright company
 */
class Zrt_DI_Definition_CloudFront
{
    /**
     * This method will instantiate the object, configure it and return it
     *
     * @return Zend_Cache_Manager
     */
    public static function getInstance(){
        $config = Zrt_DI_Container::get('ConfigObject');
        
        $cloudFrontConfig = array(
            'accessKey' => $config->amazon->aws_access_key,
            'privateKey' => $config->amazon->aws_private_key,
            'distributionId' => $config->amazon->cloudfront->distribution_id,
        );
        
        $cloudFront = new Zrt_Amazon_CloudFront($cloudFrontConfig);
        
        return $cloudFront;
    }
}