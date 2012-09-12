<?php

class Core_Acl extends Zend_Acl
{

    const POSTULANTE = 'postulante';
    const INVITADO = 'invitado';

    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('invitado'));
        $this->addRole(new Zend_Acl_Role(self::POSTULANTE), 'invitado');

        $this->add(new Zend_Acl_Resource('default::error::error'));
        $this->add(new Zend_Acl_Resource('default::index::error404'));
        $this->add(new Zend_Acl_Resource('default::index::auth'));
        $this->add(new Zend_Acl_Resource('default::index::index'));
        $this->add(new Zend_Acl_Resource('default::index::ingresar'));
        $this->add(new Zend_Acl_Resource('default::index::listado'));
        $this->add(new Zend_Acl_Resource('default::index::login'));
        $this->add(new Zend_Acl_Resource('default::index::ver-anuncio'));
        $this->add(new Zend_Acl_Resource('default::index::postular'));
        $this->add(new Zend_Acl_Resource('default::index::error-api'));
        $this->add(new Zend_Acl_Resource('default::index::loading'));

        $this->add(new Zend_Acl_Resource('default::perfil::estudios'));
        $this->add(new Zend_Acl_Resource('default::perfil::experiencia'));
        $this->add(new Zend_Acl_Resource('default::perfil::idiomas'));
        $this->add(new Zend_Acl_Resource('default::perfil::index'));
        $this->add(new Zend_Acl_Resource('default::perfil::programas'));
        $this->add(new Zend_Acl_Resource('default::perfil::referencias'));

        $this->add(new Zend_Acl_Resource('default::registro::finalizar-registro'));
        $this->add(new Zend_Acl_Resource('default::registro::index'));
        $this->add(new Zend_Acl_Resource('default::registro::perfil-profesional'));
        $this->add(new Zend_Acl_Resource('default::registro::registra-tus-datos'));

        $this->add(new Zend_Acl_Resource('default::usuario::cambio-clave'));
        $this->add(new Zend_Acl_Resource('default::usuario::datos-personales'));
        $this->add(new Zend_Acl_Resource('default::usuario::index'));
        $this->add(new Zend_Acl_Resource('default::usuario::lista-postulaciones'));
        $this->add(new Zend_Acl_Resource('default::usuario::login'));
        $this->add(new Zend_Acl_Resource('default::usuario::logout'));
        $this->add(new Zend_Acl_Resource('default::usuario::ver-anuncios-web'));
        $this->add(new Zend_Acl_Resource('default::usuario::ver-postulaciones'));
        $this->add(new Zend_Acl_Resource('default::usuario::postular'));

        //  $this->add(new Zend_Acl_Resource('admin::tipo-antecedentes'));
        //PERMISOS
        $this->allow('invitado', 'default::error::error');
        $this->allow('invitado', 'default::index::error404');
        $this->allow('invitado', 'default::index::auth');
        $this->allow('invitado', 'default::index::index');
        $this->allow('invitado', 'default::index::ingresar');
        $this->allow('invitado', 'default::index::listado');
        $this->allow('invitado', 'default::index::login');
        $this->allow('invitado', 'default::index::ver-anuncio');
        $this->allow('invitado', 'default::index::error-api');
        $this->allow('invitado', 'default::index::loading');

        $this->allow('invitado', 'default::usuario::logout');

        $this->allow('invitado', 'default::registro::index');
        $this->allow('invitado', 'default::registro::finalizar-registro');
        $this->allow('invitado', 'default::registro::perfil-profesional');
        $this->allow('invitado', 'default::registro::registra-tus-datos');

        $this->allow(self::POSTULANTE, 'default::perfil::estudios');
        $this->allow(self::POSTULANTE, 'default::perfil::experiencia');
        $this->allow(self::POSTULANTE, 'default::perfil::idiomas');
        $this->allow(self::POSTULANTE, 'default::perfil::index');
        $this->allow(self::POSTULANTE, 'default::perfil::programas');
        $this->allow(self::POSTULANTE, 'default::perfil::referencias');


        $this->allow(self::POSTULANTE, 'default::usuario::postular');
        $this->allow(self::POSTULANTE, 'default::usuario::cambio-clave');
        $this->allow(self::POSTULANTE, 'default::usuario::datos-personales');
        $this->allow(self::POSTULANTE, 'default::usuario::index');
        $this->allow(self::POSTULANTE, 'default::usuario::lista-postulaciones');
        $this->allow(self::POSTULANTE, 'default::usuario::login');
        $this->allow(self::POSTULANTE, 'default::usuario::ver-anuncios-web');
        $this->allow(self::POSTULANTE, 'default::usuario::ver-postulaciones');
    }

}
