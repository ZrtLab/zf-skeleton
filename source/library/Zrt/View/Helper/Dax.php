<?php


/**
 * Description of Dax
 *
 * @author eanaya
 */
class App_View_Helper_Dax extends Zend_View_Helper_Abstract
{

    /**
     *
     * @var App_Model_Ubicacion
     */
    protected $_modelUbicacion = null;

    public function Dax()
    {
        $req = Zend_Controller_Front::getInstance()->getRequest();
        $key = $req->getControllerName() . '.' . $req->getActionName();
        $departamento = $req->getParam('departamento');
        $provincia = $req->getParam('provincia');
        $distrito = $req->getParam('distrito');

        if (!empty($departamento)) {
            $this->_modelUbicacion = new App_Model_Ubicacion(
                    array('departamento' => $departamento,
                        'provincia' => $provincia,
                        'distrito' => $distrito)
            );
        }

        $tag = null;

        $map = array(
            'index.index' => 'portada.portada',
            'index.publicar' => 'publicacion.portada',
            'index.contacto' => 'contacto.portada',
            'index.error404' => '404.404'
        );

        $default = 'otros.otros';

        if ($this->_modelUbicacion instanceof App_Model_Ubicacion &&
            $this->_modelUbicacion->checkExist()) {
            $tag = "ciudades.{$this->_modelUbicacion->getTag()}";
            if ($this->_modelUbicacion->getDepartamento() != "" &&
                $this->_modelUbicacion->getProvincia() == "" &&
                $this->_modelUbicacion->getDistrito() == "") {
                $tag = "{$this->_modelUbicacion->getDepartamento()}.portada";
            } else {
                $tag = "{$this->_modelUbicacion->getTag()}";
            }
        }

        if (is_null($tag)) {
            $tag = array_key_exists($key, $map) ? $map[$key] : $default;
        }

        return <<<EOD
                <!-- Begin comScore UDM code 1.1104.26 -->
                <script type="text/javascript">
                // <![CDATA[
                function comScore(t){var b="comScore",o=document,f=o.location,a="",e="undefined",g=2048,s,k,p,h,r="characterSet",n="defaultCharset",m=(typeof encodeURIComponent!=e?encodeURIComponent:escape);if(o.cookie.indexOf(b+"=")!=-1){p=o.cookie.split(";");for(h=0,f=p.length;h<f;h++){var q=p[h].indexOf(b+"=");if(q!=-1){a="&"+unescape(p[h].substring(q+b.length+1))}}}t=t+"&ns__t="+(new Date().getTime());t=t+"&ns_c="+(o[r]?o[r]:(o[n]?o[n]:""))+"&c8="+m(o.title)+a+"&c7="+m(f&&f.href?f.href:o.URL)+"&c9="+m(o.referrer);if(t.length>g&&t.indexOf("&")>0){s=t.substr(0,g-8).lastIndexOf("&");t=(t.substring(0,s)+"&ns_cut="+m(t.substring(s+1))).substr(0,g)}if(o.images){k=new Image();if(typeof ns_p==e){ns_p=k}k.src=t}else{o.write('<p><'+'img src="'+t+'" height="1" width="1" alt="*"></p>')}};
                comScore('http'+(document.location.href.charAt(4)=='s'?'s://sb':'://b')+'.scorecardresearch.com/p?c1=2&c2=6906602&amp;ns_site=clasificados-principal&name=$tag');
                // ]]>
                </script>
                <noscript><p><img src="http://b.scorecardresearch.com/p?c1=2&amp;c2=6906602&amp; ns_site=clasificados-principal&amp;name=$tag" height="1" width="1" alt="*"></p></noscript>
                <!-- End comScore UDM code -->   
EOD;
    }

}