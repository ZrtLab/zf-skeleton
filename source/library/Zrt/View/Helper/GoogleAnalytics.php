<?php

/**
 * Description of Attribs
 *
 * @author eanaya
 */
class App_View_Helper_GoogleAnalytics extends Zend_View_Helper_HtmlElement
{

    public function GoogleAnalytics()
    {
        return "<!-- Start Google Analytics -->".
                '<script type="text/javascript">'.
                "    var _gaq = _gaq || [];
                  _gaq.push(['_setAccount', 'here goes ID']);
                  _gaq.push(['_setDomainName', 'heregoesdomain.com']);
                  _gaq.push(['_trackPageview']);
                  _gaq.push(['_trackPageLoadTime']);
                  (function() {
                    var ga = document.createElement('script'); ga.type = 'text/javascript';".
                    "ga.async = true;
                    ga.src =
                    ('https:' == document.location.protocol ? 'https://ssl' : 'http://www')".
                    "+'.google-analytics.com/ga.js';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(ga, s);
                  })();
                </script>".
        "<!-- End Google Analytics -->";
    }

}
