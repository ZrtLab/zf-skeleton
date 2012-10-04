<?php
/**
 * Command
 *
 * @package default
 */
class App_Command_Facebook extends App_Command_Abstract{
    /**
     * Store the command name
     *
     * @var string
     */
    private $_commandName = 'publishToFacebook';
    
    /**
     * Convenience method to run the command
     *
     * @param string $name
     * @param mixed $args
     * @return boolean
     */
    public function onCommand($name, $args){
        if(strcasecmp($name, $this->_commandName) !== 0){
            return FALSE;
        }
        
        //Do the command
        if(count($args) && (Zend_Registry::get('IS_PRODUCTION') || App_DI_Container::get('ConfigObject')->facebook->testing)) {
            $args['access_token'] = App_DI_Container::get('ConfigObject')->facebook->access_token;
            
            $url = 'https://graph.facebook.com/YOUR_PAGE/feed';
            $client = new Zend_Http_Client($url);
            $client->setMethod(Zend_Http_Client::POST);
            $client->resetParameters();
            $client->setParameterPost($args);
            $response = $client->request();
            
            return $response->isSuccessful();
        }
        
        return FALSE;
    }
}