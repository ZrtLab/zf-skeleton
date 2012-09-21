<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of Acl
 *
 * @author slovacus
 */
class Zrt_Controller_Plugin_Acl
        extends Zend_Controller_Plugin_Abstract
    {


    //put your code here
    public function preDispatch( Zend_Controller_Request_Abstract $request )
        {
        parent::preDispatch( $request );

        $acl = new Zend_Acl();
        //adding Roles
        $acl->addRole( new Zend_Acl_Role( "" ) )
                ->addRole( new Zend_Acl_Role( "guest" ) , "" )
                ->addRole( new Zend_Acl_role( "user" ) , "guest" )
                ->addRole( new Zend_Acl_role( "admin" ) , "user" );
        //Adding Resources
        $acl->add( new Zend_Acl_Resource( "default" ) )
                ->add( new Zend_Acl_Resource( "admin" ) )
                ->add( new Zend_Acl_Resource( "user" ) )
                ->add( new Zend_Acl_Resource( "error" ) );

        //set up access a roles
        $acl->allow( null , array( "error" , "error" ) );



        //set up access a Guest


        $acl->allow( "guest" , "default" );

        //access a user
        $acl->allow( "user" , "default" );
        $acl->allow( 'user' , 'user' );


        //access of admistrator
        $acl->allow( 'admin' , null );


        $auth = Zend_Auth::getInstance();

        if ( $auth->hasIdentity() )
            {
            $identity = $auth->getIdentity();
            $role = strtolower( $identity->role );
            }
        else
            {
            $role = 'guest';
            }

        $module = $request->module;
        $controller = $request->controller;
        $action = $request->action;

        if ( !$acl->isAllowed( $role , $module , $controller , $action ) )
            {
            if ( $role == 'guest' or $role === "" )
                {
                $request->setModuleName( 'default' );
                $request->setControllerName( 'usuario' );
                $request->setActionName( 'index' );
                }
            else
                {
                $request->setModuleName( 'default' );
                $request->setControllerName( "error" );
                $request->setActionName( "noauth" );
                }
            }
        }


    }

?>
