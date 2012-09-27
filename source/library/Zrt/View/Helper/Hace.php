<?php


/**
 * Description of Hace
 *
 * @author eanaya
 */
class App_View_Helper_Hace extends Zend_View_Helper_HtmlElement
{

    /**
     * @link http://css-tricks.com/snippets/php/time-ago-function/
     * @param  String
     * @return string
     */
    public function Hace($diahora)
    {
        $rcs = 0;
        $tm = is_int($diahora) ? $diahora : strtotime($diahora);
        $curTm = time();
        $dif = $curTm - $tm;
        $negativo = false;
        if ($dif < 0) {
            $dif = $dif * -1;
            $negativo = true;
        }
        $pds = array('seg', 'min', 'hour', 'day', 'week', 'month', 'year', 'decade');
        //$pds = array('segundo', 'minuto', 'hora', 'día', 'semana', 'mes', 'año', 'década');
        $lngh = array(1, 60, 3600, 86400, 604800,
            2630880, 31570560, 315705600);
        for (
        $v = sizeof($lngh) - 1; ($v >= 0) && (($no = $dif / $lngh[$v]) <= 1);
                $v--) ;
        if ($v < 0) $v = 0; $_tm = $curTm - ($dif % $lngh[$v]);
        $no = floor($no);
        if ($no <> 1) $pds[$v] .= substr($pds[$v], -1) == 's' ? 'es' : 's';
        $x = sprintf("%d %s ", $no, $pds[$v]);
        if (($rcs == 1) && ($v >= 1) && (($curTm - $_tm) > 0))
                $x .= time_ago($_tm);
        if ($negativo) return "-" . $x;
        return $x;
    }

}