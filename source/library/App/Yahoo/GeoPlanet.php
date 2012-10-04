<?php
/**
 * Yahoo GeoPlanet API wrapper
 *
 * @category App
 * @package App_Yahoo_GeoPlanet
 * @copyright company
 */

/**
 * Handle different operations with the Yahoo GeoPlanet API
 */
class App_Yahoo_GeoPlanet
{
    /**
     * Prepare the request of the info related to a woeid
     *
     * @param int $woeid
     * @access public
     * @return array
     **/
    public static function getLocationInfo($woeid)
    {
        $url = "http://where.yahooapis.com/v1/place/{$woeid}?format=json&appid=" . App_DI_Container::get('ConfigObject')->yahoo->key;
        
        $response = self::_performRequest($url);
        
        return json_decode($response);
    }
    
    /**
     * Get the WOEID identifier from the latitude and longitude
     *
     * @param float $lat 
     * @param float $lng 
     * @return int
     */
    public static function getWoeidFromLatLng($lat, $lng){
        $url = "http://where.yahooapis.com/geocode?location={$lat}%20{$lng}&gflags=R&flags=P&appid=" . App_DI_Container::get('ConfigObject')->yahoo->key;
        
        $response = self::_performRequest($url);
        
        return unserialize($response);
    }
    
    /**
     * undocumented function
     *
     * @param string $url
     * @access private
     * @return array
     **/
    private static function _performRequest($url)
    {
        $cache = App_DI_Container::get('CacheManager')->getCache('memcache');
        
        if(($result = $cache->load(sha1($url))) === FALSE){
            $client = new Zend_Http_Client($url);
            $client->setMethod(Zend_Http_Client::GET);
            $response = $client->request();

            if($response->getStatus() == 200){
                $result = $response->getBody();
                $cache->save($result, sha1($url), array(), 86400);
            }else{
                $result = FALSE;
            }
        }
        
        return $result;
    }
}