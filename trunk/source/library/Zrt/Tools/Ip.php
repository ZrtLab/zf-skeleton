<?php

class Zrt_Tools_Ip
{

    private $ip;
    private $country;
    private $longIp;

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getLongIp()
    {
        return $this->longIp;
    }

    public function setLongIp($longIp)
    {
        $this->longIp = $longIp;
    }

    public static function getRealIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
                return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }

    public function __construct($ip = "")
    {

        if (isset($ip) & $ip !== "") {
            $this->ip = $ip;
            $this->longIp = $this->IP2LONG($this->ip);
        } else {
            $this->ip = self::getRealIP();
            $this->longIp = $this->IP2LONG($this->ip);
        }
    }

    /**
     *
     * @param type $ip
     * @return type Decimal de Ip
     */
    public static function IP2LONG($ip)
    {
        $d = 0.0;
        $b = explode(".", $ip, 4);
        for ($i = 0; $i < 4; $i++) {
            $d *= 256.0;
            $d += $b[$i];
        };
        return $d;
    }

    public function convIpLonG()
    {
        $d = 0.0;
        $b = explode(".", $this->ip, 4);
        for ($i = 0; $i < 4; $i++) {
            $d *= 256.0;
            $d += $b[$i];
        };
        $this->longIp = $d;
    }

}