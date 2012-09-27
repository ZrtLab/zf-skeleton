<?php


/**
 * Description of Ga
 *
 * @author eanaya
 */
class App_View_Helper_Ga
{

    public function Ga($account, $domain)
    {
        return <<<EOD

		<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '$account']);
		_gaq.push(['_setDomainName', '$domain']);
		_gaq.push(['_trackPageview']);
		_gaq.push(['_trackPageLoadTime']); 

		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
		</script>

        
EOD;
    }

}