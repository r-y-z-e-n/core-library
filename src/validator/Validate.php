<?php

namespace Ryzen\CoreLibrary\validator;

use DateTime;
use Ryzen\CoreLibrary\Auth;
use Ryzen\CoreLibrary\Functions;
use Ryzen\CoreLibrary\helper\Message;
use Ryzen\CoreLibrary\config\Application;

class Validate extends Application
{
    private static string $RULE_REQUIRED = 'required';
    private static string $RULE_UNIQUE   = 'unique';
    private static string $RULE_MINIMUM  = 'min';
    private static string $RULE_MAXIMUM  = 'max';
    private static string $RULE_MATCHES  = 'match';
    private static string $RULE_DATE     = 'date';

    /**
     * @param $needle
     * @param $validationRule
     * @return mixed
     */
    protected static function getNextIndexValue($needle, $validationRule){
        return $validationRule[array_search($needle, $validationRule) + 1];
    }

    /**
     * @param $field
     * @return false|string
     */

    protected static function getMethod( $field ){
        if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'post'){
            return strip_tags(trim($_POST[$field]));
        }
        if($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'get'){
            return strip_tags(trim($_GET[$field]));
        }
        return false;
    }

    protected static function get_key( $key , $validationRule){
        return (in_array('label', $validationRule)) ? self::getNextIndexValue('label', $validationRule) : $key;
    }

    /**
     * @param $key
     * @param $validationRule
     * @param $fieldOnMethod
     * @return string|void
     */

    protected static function newValidation($key, $validationRule, $fieldOnMethod){
        $fieldOnMethod = Functions::safeString($fieldOnMethod);
        if(in_array(self::$RULE_REQUIRED, $validationRule)){
            if(empty($fieldOnMethod)){
                return str_replace('{key}',self::get_key($key, $validationRule), Message::validation('required'));
            }
        }
        if(in_array(self::$RULE_MINIMUM, $validationRule)){
            (int) $minimumRequiredLength = self::getNextIndexValue(self::$RULE_MINIMUM, $validationRule);
            if(strlen($fieldOnMethod) < $minimumRequiredLength){
                return str_replace(['{key}', '{val}'],[self::get_key($key, $validationRule), $minimumRequiredLength], Message::validation('minimum'));
            }
        }
        if(in_array(self::$RULE_MAXIMUM, $validationRule)){
            (int) $maximumRequiredLength = self::getNextIndexValue(self::$RULE_MAXIMUM, $validationRule);
            if(strlen($fieldOnMethod) > $maximumRequiredLength){
                return str_replace(['{key}', '{val}'],[self::get_key($key, $validationRule), $maximumRequiredLength], Message::validation('maximum'));
            }
        }
        if(in_array(self::$RULE_UNIQUE, $validationRule)){
            $tableName = self::getNextIndexValue(self::$RULE_UNIQUE, $validationRule);
            if(Auth::checkValueExistence([$key => $fieldOnMethod], $tableName)){
                return str_replace('{key}',self::get_key($key, $validationRule), Message::validation('unique'));
            }
        }

        if(in_array(self::$RULE_MATCHES, $validationRule)){
            $toMatch = self::getNextIndexValue(self::$RULE_MATCHES, $validationRule);
            if($fieldOnMethod !== self::getMethod($toMatch)){
                return str_replace(['{key}', '{matching.key}'],[self::get_key($key, $validationRule), self::get_key($toMatch, $validationRule)], Message::validation('matches'));
            }
        }

        if(in_array(self::$RULE_DATE, $validationRule)){
            $acceptedDateFormat = str_replace('/','-', $fieldOnMethod);
            $acceptedDateFormat = str_replace('.', '-', $fieldOnMethod);
            $d = DateTime::createFromFormat('Y-m-d', $acceptedDateFormat);
            if(($d && $d->format('Y-m-d') === $acceptedDateFormat) == false){
                return str_replace('{key}',self::get_key($key, $validationRule), Message::validation('date'));
            }
        }
    }
}