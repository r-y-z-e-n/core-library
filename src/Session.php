<?php

namespace Ryzen\CoreLibrary;

class Session
{
    /**
     * @param $sessionSet
     * @param null $value
     */

    public static function put($sessionSet, $value = null){
        if(is_array($sessionSet)){
            foreach ($sessionSet as $key => $value){
                $_SESSION[$key] = Functions::Ry_Secure($value);
            }
        }
        if(is_string($sessionSet)){
            $_SESSION[$sessionSet] = Functions::Ry_Secure($value);
        }
    }

    /**
     * @param $key
     * @return array|string|string[]
     */

    public static function get($key){
        if(self::has($key)){
            return Functions::Ry_Secure($_SESSION[$key]);
        }
        return 'Session Undeclared';
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
     *
     */

    public static function flush(){
        session_destroy();
    }
}