<?php
/**
 * Helper used in order to route the media to the CDN
 *
 * @category App
 * @package App_Frontend_View
 * @subpackage Helper
 * @copyright company
 */

class App_View_Helper_CDN extends Zend_View_Helper_Abstract
{
    /**
     * Implements the "fluent" interface. In the view, it it's called 
     * directly, it will return the current object, so all the other methods
     * can be called without the need of explicitly instantiating the helper
     *
     * @access public
     * @return App_Frontend_View_Helper_CDN
     */
    public function CDN(){
        return $this;
    }
    
    /**
     * Return the url of the file in the CDN
     *
     * @param string $file
     * @param int $version
     * @access public
     * @return string
     **/
    public function getUrl($file, $width = 0, $height = 0){
        if(!empty($width) || !empty($height)){
            $file = sprintf('%s_%d_%d.png', substr($file, 0, strrpos($file, '.')), $width, $height);
        }
        
        $cdnUrls = App_DI_Container::get('ConfigObject')->amazon->cloudfront->url->toArray();
        shuffle($cdnUrls);
        
        return sprintf('http://%s/%s', array_pop($cdnUrls), $file);
    }
}