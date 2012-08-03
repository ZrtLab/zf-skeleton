<?php

/**
 * Zrt PHP Library
 *
 * @category Zrt
 * @package Zrt_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: DeleteHandler.php 49 2010-07-18 23:23:29Z jamie $
 */


/**
 * Handles HTTP DELETE requests
 *
 * @category Zrt
 * @package Zrt_Controller
 */
class Zrt_Controller_Plugin_DeleteHandler
        extends Zend_Controller_Plugin_Abstract
    {


    public function preDispatch( Zend_Controller_Request_Abstract $request )
        {
        if ( $this->_request->isDelete() )
            {
            $deleteParams = array( );
            parse_str( $this->_request->getRawBody() , $deleteParams );
            $request->setParams( $deleteParams );
            }


        }


    }
