<?php

namespace Ryzen\CoreLibrary;

use Ryzen\CoreLibrary\validator\Validate;

class Validator extends Validate
{
    private static array $errors = [];

    /**
     * @param array $validation
     */

    public static function make(array $validation){
       foreach ($validation as $key => $value){
           $validationRule     = explode('|', strtolower($value));
           $fieldOnMethod      = self::getMethod($key);
           if(!empty(self::newValidation($key, $validationRule, $fieldOnMethod))){
               self::$errors[$key] = self::newValidation($key, $validationRule, $fieldOnMethod);
           }
       }
    }

    /**
     * @return bool
     */

    public static function passed(): bool{
        return empty(self::$errors);
    }

    /**
     * @return bool
     */
    public static function failed() : bool{
        return !empty(self::$errors);
    }

    /**
     * @param string $key
     * @return array|mixed
     */

    public static function error(string $key = 'all' ){
        return ($key == 'all') ? self::$errors : self::$errors[$key];
    }
}