<?php

namespace Ryzen\CoreLibrary;

class Cookie
{

    /**
     * @param $cookieName
     * @param null $cookieValue
     * @param null $cookieExpiry
     * @param null $cookiePath
     * @param null $cookieDomain
     * @param null $cookieSecurity
     * @return bool|string
     */

    public static function put($cookieName, $cookieValue = null, $cookieExpiry = null, $cookiePath = null, $cookieDomain = null, $cookieSecurity = null){
        $put = '';
        $cookieExpiry = (!empty($cookieExpiry)) ? $cookieExpiry : time() + (10 * 365 * 24 * 60 * 60);
        if(is_array($cookieName)){
            foreach ($cookieName as $key => $cookieValue){
                $put = setcookie($key, Functions::safeString($cookieValue), $cookieExpiry, $cookiePath, $cookieDomain, $cookieSecurity);
            }
        }
        if(is_string($cookieName)){
            $put = setcookie($cookieName, Functions::safeString($cookieValue), $cookieExpiry, $cookiePath, $cookieDomain, $cookieSecurity);
        }
        return $put;
    }

    /**
     * @param $key
     * @return array|string|string[]
     */

    public static function get($key){
        if(self::has($key)){
            return Functions::safeString($_COOKIE[$key]);
        }
        return false;
    }

    /**
     * @param $key
     * @return bool
     */

    public static function has($key): bool {
        if(isset($_COOKIE[$key]) && $_COOKIE[$key] !== "" && !empty($_COOKIE[$key]) && !is_null($_COOKIE[$key])){
            return true;
        }
        return false;
    }

    /**
     * @param $key
     */

    public static function forget($key){
        if(self::has($key)){
            $_COOKIE[$key] = '';
            unset($_COOKIE[$key]);
            setcookie($key,null,-1);
            setcookie($key,null,-1,'/');
        }
    }

    /**
     * @return bool
     */

    public static function flush() : bool{
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
            return true;
        }
        return true;
    }
}