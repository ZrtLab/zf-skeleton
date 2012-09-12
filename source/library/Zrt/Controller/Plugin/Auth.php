<?php

/**
 * Maneja autorizaciones con Zend_Auth
 * @author acastillo
 *
 */
class Zrt_controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{

    private $_auth;
    private $_acl;
    private $_noAuth = array(
        'module' => 'default',
        'controller' => 'usuario',
        'action' => 'index'
    );
    private $_noAcl = array(
        'module' => 'default',
        'controller' => 'usuario',
        'action' => 'no-autorizado'
    );
    private $_noLogin = array();

    public function __construct()
    {
        $this->_auth = Zend_Auth::getInstance();
        $this->_acl = new Zrt_Acl();
        $this->addNoLogin('default', 'usuario', 'logout');
        $this->addNoLogin('default', 'index', 'no-autorizado');
        $this->addNoLogin('default', 'usuario', 'registro');
        $this->addNoLogin('default', 'usuario', 'recuperar-password');
    }

    /**
     * preDispatch hook
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($this->_auth->hasIdentity()) {
            $identity = $this->_auth->getIdentity();
            $role = strtolower($identity['rol']);
        } else {
            $role = 'invitado';
        }

        $action = $request->action;
        $resource = $request->getModuleName() . '::' . $request->getControllerName();

        if (!$this->_acl->isAllowed($role, $resource, $action)) {
            $noAuth = array(
                'module' => $request->getModuleName(),
                'controller' => $request->getControllerName(),
                'action' => $request->getActionName()
            );

            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                    'redirector'
            );
            $Zrt = new Zend_Session_Namespace('Zrt');

            $Zrt->noAuth = $noAuth;
//            $request->setModuleName('default')
//					->setControllerName('usuario')
//					->setActionName('no-autorizado')
//					->setDispatched(true);
            $redirector->gotoUrl('/default/usuario/no-autorizado')
                ->redirectAndExit();
        }

//        $role = "invitado";
//        
//        if ( $this->_auth->hasIdentity() )
//            {
//            $data = $this->_auth->getIdentity();
//            $role = strtolower( $data['rol'] );
//            }
//        
//
//
//        //$role = "invitado";
//        if ( $this->requiresLogin( $request ) )
//            {
////            if ( $this->_auth->hasIdentity() )
////                {
////                $data = $this->_auth->getIdentity();
////                $role = strtolower( $data['rol'] );
//                $resource = $request->getModuleName() . '::' . $request->getControllerName();
//                $action = $request->getActionName();
//
////                if (  $this->_acl->  )
////                    {
//                if ( !$this->_acl->isAllowed( $role , $resource , $action ) )
//                    {
//                    $request->setModuleName( $this->_noAcl['module'] );
//                    $request->setControllerName( $this->_noAcl['controller'] );
//                    $request->setActionName( $this->_noAcl['action'] );
//                    }
////                }
////                else
////                    {
////                    $this->_redirect( "/default/index/index" );
////                    }
////                }
//            else
//                {
//                $request->setModuleName( $this->_noAuth['module'] );
//                $request->setControllerName( $this->_noAuth['controller'] );
//                $request->setActionName( $this->_noAuth['action'] );
//                }
//            }
    }

    private function addNoLogin($module, $controller, $action)
    {
        $nl = array();
        $nl['module'] = $module;
        $nl['controller'] = $controller;
        $nl['action'] = $action;
        $this->_noLogin[] = $nl;
    }

    private function requiresLogin(Zend_Controller_Request_Abstract $request)
    {
        $result = true;
        $rq = array();
        $rq['module'] = $request->getModuleName();
        $rq['controller'] = $request->getControllerName();
        $rq['action'] = $request->getActionName();
        if (in_array($rq, $this->_noLogin)) {
            $result = false;
        }
        return $result;
    }

}