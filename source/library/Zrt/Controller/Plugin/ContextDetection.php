<?php

/**
 * Zrt PHP Library
 *
 * @category Zrt
 * @package Zrt_Controller
 * @copyright Copyright (c) 2008-2010 Jamie Talbot (http://jamietalbot.com)
 * @version $Id: ContextDetection.php 69 2010-09-08 12:32:03Z jamie $
 */


/**
 * Adds support for context detection via a custom header.
 *
 * @category Zrt
 * @package Zrt_Controller
 */
class Zrt_Controller_Plugin_ContextDetection
        extends Zend_Controller_Plugin_Abstract
    {


    public function preDispatch( Zend_Controller_Request_Abstract $request )
        {
        $context = $request->getHeader( 'X-Zrt-Format' );
        if ( $context )
            {
            $request->setParam( 'format' , $context );
            }


        }


    }