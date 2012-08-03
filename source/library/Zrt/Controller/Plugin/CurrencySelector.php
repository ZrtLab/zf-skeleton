<?php


class Zrt_Controller_Plugin_CurrencySelector
        extends Zend_Controller_Plugin_Abstract
    {


    public function preDispatch( Zend_Controller_Request_Abstract $request )
        {
        parent::preDispatch( $request );

        $Zrt = new Zend_Session_Namespace( 'Zrt' );

        $viewRenderer =
                Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'viewRenderer'
        );
        if ( null === $viewRenderer->view )
            {
            $viewRenderer->initView();
            }
        $view = $viewRenderer->view;
        
        if(!isset($Zrt->currency->id))
            {
                $Zrt->currency->id = 1;
            }
        if(!isset($Zrt->currency->code))
            {
                $Zrt->currency->code = "CH";
            }
        if(!isset($Zrt->currency->codeGoogle))
            {
                $Zrt->currency->codeGoogle = "CHF";
            }
          
        $view->assign(
                    'formCurrency' , new Zrt_Form_Currency(
                        array(
                            'data'=>array(
                                'moneda_id' => $Zrt->currency->id
                                )
                                )
                        )
            ) ;
        $view->assign( 'currency' , $Zrt->currency );

        }


    }