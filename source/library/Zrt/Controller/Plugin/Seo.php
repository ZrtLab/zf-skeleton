<?php


class Zrt_Controller_Plugin_Seo
        extends Zend_Controller_Plugin_Abstract
    {


    public function routeStartup( $request )
        {
        $url = $request->getRequestUri();

        $param = substr( $url , (strrpos( $url , '/' ) + 1 ) );
        list($product , $num , $category) = explode( '-' , $param );

        $category = substr( $category , 0 , -5 );

        //Find the URL part before the product-num-category.html
        $dirPart = substr( $url , 0 , strrpos( $url , '/' ) );

        //Change the request's URL
        $request->setRequestUri(
                $dirPart . '/products/show/product/' . $product . '/category/' . $category
        );
        }


    }