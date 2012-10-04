<?php
/**
 * Amazon SNS Topic
 * 
 * Allows creating of new topics, deleting, setting the display name and publishing to existing topics
 * 
 * @author Russell Smith <russell.smith@ukd1.co.uk>
 */
class App_Amazon_SNS_Topic {
    private $_topicArn = '';
    private $_accessKey = '';
    private $_privateKey = '';
    private $_host = '';
    
    /**
     * Create a new topic object
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
     * Publish a message to this topic
     * 
     * @param string subject of the message (for emails)
     * @param string message body
     */
    public function publish($subject, $message){
        $response = $this->_request(array(
            'Subject' => $subject,
            'Message' => $message,
            'TopicArn' => $this->_topicArn,
            'Action' => 'Publish'
        ));
    }
    
    /**
     * Sign and perform a request
     * 
     * @param array key / value list of parameters to pass
     * @return SimpleXMLElement
     */
    private function _request($params){
        //Add in the extra parameters which we know
        $params['AWSAccessKeyId'] = $this->_accessKey;
        $params['Timestamp'] = gmdate("Y-m-d\TH:i:s\Z");
        $params['SignatureMethod'] = 'HmacSHA256';
        $params['SignatureVersion'] = 2;
        
        //Create the string to be signed
        $string = "GET\n" . $this->_host . "\n/\n";
        
        //Sort them
        ksort($params);
        
        $encoded = array();
        foreach($params as $key => $param) {
            $encoded[] = rawurlencode($key) . '=' . rawurlencode($param);
        }
        $string .= implode('&', $encoded);
        
        $params['Signature'] = base64_encode(hash_hmac('sha256', $string, $this->_privateKey, TRUE));
        $encoded[] = 'Signature=' . rawurlencode($params['Signature']);
        $url = 'https://' . $this->_host . '/?' . implode('&', $encoded);
        
        //Init curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        //Execute
        $output = curl_exec($ch);
        
        //Really should check for some http error codes or something...
        return new SimpleXMLElement($output);
    }
}