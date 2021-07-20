<?php

namespace Ryzen\CoreLibrary;

class Cookie
{
    /**
     * @param $cookieSet
     * @param null $value
     * @return bool|string
     */

    public static function put($cookieSet, $value = null){
        $put = '';
        if(is_array($cookieSet)){
            foreach ($cookieSet as $key => $value){
                $put = setcookie($key, Functions::safeString($value), time() + (10 * 365 * 24 * 60 * 60));
            }
        }
        if(is_string($cookieSet)){
            $put = setcookie($cookieSet, Functions::safeString($value), time() + (10 * 365 * 24 * 60 * 60));
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