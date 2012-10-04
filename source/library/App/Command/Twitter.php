<?php
/**
 * Command
 *
 * @package default
 */
class App_Command_Twitter extends App_Command_Abstract{
    /**
     * Store the command name
     *
     * @var string
     */
    private $_commandName = 'publishToTwitter';
    
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
        if(!empty($args['text'])) {
            $token = new Zend_Oauth_Token_Access();
            $token->setToken(App_DI_Container::get('ConfigObject')->twitter->access_token);
            $token->setTokenSecret(App_DI_Container::get('ConfigObject')->twitter->access_token_secret);
            
            $twitter = new Zend_Service_Twitter(array(
                'username' => App_DI_Container::get('ConfigObject')->twitter->username,
                'accessToken' => $token
            ));
            $response = $twitter->status->update($args['text']);
            
            return $response->isSuccess();
        }
        
        return FALSE;
    }
}