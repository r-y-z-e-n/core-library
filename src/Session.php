<?php

namespace Ryzen\CoreLibrary;

class Session
{
    /**
     * @param $sessionSet
     * @param null $value
     * @return array|string|string[]
     */

    public static function put($sessionSet, $value = null){
        $put = '';
        if(is_array($sessionSet)){
            foreach ($sessionSet as $key => $value){
                $put = $_SESSION[$key] = $value;
            }
        }
        if(is_string($sessionSet)){
            $put = $_SESSION[$sessionSet] = $value;
        }
        return $put;
    }

    /**
     * @param $key
     * @return array|string|string[]
     */

    public static function get($key){
        if(self::has($key)){
            return $_SESSION[$key];
        }
        return false;
    }

    /**
     * @param $key
     * @return bool
     */

    public static function has($key): bool{
        if(isset($_SESSION[$key]) && $_SESSION[$key] !== "" && !empty($_SESSION[$key]) && !is_null($_SESSION[$key])){
            return true;
        }
        return false;
    }

    /**
     * @param $key
     */

    public static function forget($key){
        if(self::has($key)){
            unset($_SESSION[$key]);
        }
    }

    /**
     * Cleans All Sessions;
     */

    public static function flush(){
        session_destroy();
    }
}