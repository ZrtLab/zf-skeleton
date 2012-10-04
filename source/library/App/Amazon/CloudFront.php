<?php
/**
 * Amazon CloudFront
 * 
 * Allows invalidation requests on files
 */
class App_Amazon_CloudFront {
    private $_accessKey = '';
    private $_privateKey = '';
    private $_serviceUrl = 'https://cloudfront.amazonaws.com';
    private $_distributionId = '';
    
    /**
     * Create a new CloudFront object
     * 
     * @param string the topicarn you got previously after creating a new topic
     */
    public function __construct(array $config){
        foreach ($config as $key => $value) {
            if(isset($this->{'_' . $key})) {
                $this->{'_' . $key} = $value;
            }
        }
    }
    
    /**
     * Invalidates object with passed key on CloudFront
     * 
     * @param string|array $key
     */   
    public function invalidate($keys){
        if (!is_array($keys)){
            $keys = array($keys);
        }
        
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $requestUrl = sprintf('%s/2010-11-01/distribution/%s/invalidation', $this->_serviceUrl, $this->_distributionId);
        
        //Assemble request body
        $body = '<InvalidationBatch>';
        foreach($keys as $key){
            $key = (preg_match('/^\//', $key)) ? $key : '/' . $key;
            $body .= sprintf('<Path>%s</Path>', $key);
        }
        $body .= sprintf('<CallerReference>%s</CallerReference>', time());
        $body .= '</InvalidationBatch>';
        
        //Make and send request
        $client = new Zend_Http_Client($requestUrl);
        $client->setMethod(Zend_Http_Client::POST);
        $client->setHeaders(array(
            'Date' => $date,
            'Authorization' => $this->makeKey($date)
        ));
        $client->setEncType('text/xml');
        $client->setRawData($body);
        
        $response = $client->request();
        
        return ($response->getStatus() === 201);
    }
    
    /**
     * Returns header string containing encoded authentication key
     * @param   $date       {Date}
     * @return  {String}
     */
    function makeKey($date){
        return sprintf('AWS %s:%s', $this->_accessKey, base64_encode(hash_hmac('sha1', $date, $this->_privateKey, TRUE)));
    }
}