<?php
/**
 * Bit.ly API implementation
 *
 * @category   App
 * @package    App_Service_ShortUrl
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class App_Service_ShortUrl_BitLy extends Zend_Service_ShortUrl_AbstractShortener
{
    /**
     * Base URI of the service
     *
     * @var string
     */
    protected $_baseUri = 'http://bit.ly';
    
    /**
     * Username used to authenticate the api call
     *
     * @var string
     */
    protected $_username;
    
    /**
     * The api key of the service
     *
     * @var string
     */
    protected $_apiKey;
    
    /**
     * Store the username and the apiKey
     *
     * @param string $username 
     * @param string $apiKey 
     */
    public function __construct($username, $apiKey){
        $this->_username = $username;
        $this->_apiKey = $apiKey;
    }
    
    /**
     * This function shortens long url
     *
     * @param string $url URL to Shorten
     * @throws Zend_Service_ShortUrl_Exception When URL is not valid
     * @return string New URL
     */
    public function shorten($url)
    {
        $this->_validateUri($url);

        $serviceUri = 'http://api.bit.ly/v3/shorten';
        
        $this->getHttpClient()->setUri($serviceUri);
        $this->getHttpClient()->setMethod(Zend_Http_Client::GET);
        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setParameterGet(array(
            'login' => $this->_username,
            'apiKey' => $this->_apiKey,
            'longUrl' => $url
        ));
        
        $response = $this->getHttpClient()->request();
        
        if ($response->isSuccessful()) {
            $results = Zend_Json::decode($response->getBody());
            if ($results['status_txt'] == 'OK' && isset($results['data']['url'])) {
                return $results['data']['url'];
            }
        }
        
        throw new Zend_Service_ShortUrl_Exception(sprintf(
            'Error while shortening %s: %s', $url, $results['status_txt']
        ));
    }

    /**
     * Reveals target for short URL
     *
     * @param string $shortenedUrl URL to reveal target of
     * @throws Zend_Service_ShortUrl_Exception When URL is not valid or is not shortened by this service
     * @return string
     */
    public function unshorten($shortenedUrl)
    {
        $this->_validateUri($shortenedUrl);

        $this->_verifyBaseUri($shortenedUrl);
        
        $serviceUri = 'http://api.bit.ly/v3/expand';
        
        $this->getHttpClient()->setUri($serviceUri);
        $this->getHttpClient()->resetParameters();
        $this->getHttpClient()->setParameterGet(array(
            'login' => $this->_username,
            'apiKey' => $this->_apiKey,
            'shortUrl' => $shortenedUrl
        ));
        
        $response = $this->getHttpClient()->request();
        if ($response->isSuccessful()) {
            $results = Zend_Json::decode($response->getBody());
            if ($results['status_txt'] == 'OK' && isset($results['data']['expand'][0]['long_url'])) {
                return $results['data']['expand'][0]['long_url'];
            }
        }
        
        throw new Zend_Service_ShortUrl_Exception(sprintf(
            'Error while shortening %s: %s', $url, $results['status_txt']
        ));
    }
}
