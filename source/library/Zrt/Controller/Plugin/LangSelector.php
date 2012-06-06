<?php


class Zrt_Controller_Plugin_LangSelector
        extends Zend_Controller_Plugin_Abstract
    {


    public function preDispatch( Zend_Controller_Request_Abstract $request )
        {
        parent::preDispatch( $request );

        $Zrt = new Zend_Session_Namespace( 'Zrt' );

        if ( !isset( $Zrt->config->lang ) )
            {
            $zl = new Zend_Locale();
            $Zrt->config->lang = $zl->getLanguage();
            }

        if ( $Zrt->config->lang !== 'en' && $Zrt->config->lang !== 'de' &&
                $Zrt->config->lang !== 'es' && $Zrt->config->lang !== 'pl' )
            {
            $Zrt->config->lang = 'en';
            }

        if ( !isset( $Zrt->config->idlang ) )
            {
            $_idioma = new Zrt_Models_Bussines_Idioma();

            $Zrt->config->idlang = $_idioma->getByPrefijo(
                            $Zrt->config->lang
                    )->id;
            }


        $translate = new Zend_Translate(
                        Zend_Translate::AN_GETTEXT ,
                        APPLICATION_PATH . '/configs/locale/' ,
                        $Zrt->config->lang ,
                        array( 'scan' => Zend_Translate::LOCALE_FILENAME ) ,
                        $Zrt->config->lang );

        Zend_Registry::set( 'Zend_Translate' , $translate );

        /* translate para Zend_Validate */
        $translator = new Zend_Translate(
                        Zend_Translate::AN_ARRAY ,
                        APPLICATION_PATH . '/configs/resources/languages/' ,
                        $Zrt->config->lang ,
                        array( 'scan' => Zend_Translate::LOCALE_DIRECTORY )
        );

        Zend_Validate_Abstract::setDefaultTranslator( $translator );


        /*         * variables para la vista* */

        $viewRenderer =
                Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'viewRenderer'
        );
        if ( null === $viewRenderer->view )
            {
            $viewRenderer->initView();
            }
        $view = $viewRenderer->view;

        $view->assign( 'sessionZrt' , $Zrt );

        /*         * variables para la vista* */




        /* translate para Zend_Validate */


        /* formulario idioma */
        /* fixme formulario idiomapais */

//        $data = array( );
//        if ( isset( $Zrt->config->lang ) )
//            {
//            if ( isset( $Zrt->config->idlang ) )
//                {
//                $data['idioma'] = $Zrt->config->idlang;
//                }
//            if ( isset( $Zrt->config->idpais ) )
//                {
//                $data['pais'] = $Zrt->config->idpais;
//                }
//            }



        /**/
        /* formulario idioma */
        }


    }