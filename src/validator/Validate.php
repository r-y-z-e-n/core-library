<?php

namespace Ryzen\CoreLibrary\validator;

use DateTime;
use Ryzen\CoreLibrary\Auth;
use Ryzen\CoreLibrary\Functions;
use Ryzen\CoreLibrary\helper\Message;
use Ryzen\CoreLibrary\config\Application;

class Validate extends Application
{
    private static string $RULE_REQUIRED    = 'required';
    private static string $RULE_UNIQUE      = 'unique';
    private static string $RULE_MINIMUM     = 'min';
    private static string $RULE_MAXIMUM     = 'max';
    private static string $RULE_MATCHES     = 'match';
    private static string $RULE_DATE        = 'date';
    private static string $RULE_EMAIL       = 'email';
    private static string $RULE_URL         = 'url';
    private static string $RULE_START_WITH  = 'starts_with';
    private static string $RULE_END_WITH    = 'ends_with';

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

        if(in_array(self::$RULE_EMAIL, $validationRule)){
            if(!filter_var($fieldOnMethod, FILTER_VALIDATE_EMAIL)){
                return str_replace('{key}',self::get_key($key, $validationRule), Message::validation('email'));
            }
        }

        if(in_array(self::$RULE_URL, $validationRule)){
            $path         = parse_url($fieldOnMethod, PHP_URL_PATH);
            $encoded_path = array_map('urlencode', explode('/', $path));
            $url          = str_replace($path, implode('/', $encoded_path), $fieldOnMethod);
            if(!filter_var($url, FILTER_VALIDATE_URL)){
                return str_replace('{key}',self::get_key($key, $validationRule), Message::validation('url'));
            }
        }

        if(in_array(self::$RULE_START_WITH, $validationRule)){
            if (substr($fieldOnMethod, 0, 1) !== self::getNextIndexValue(self::$RULE_START_WITH, $validationRule)){
                return str_replace(
                    ['{key}', '{val}'],
                    [self::get_key($key, $validationRule), self::getNextIndexValue(self::$RULE_START_WITH, $validationRule)],
                    Message::validation('starts_with')
                );
            }
        }

        if(in_array(self::$RULE_END_WITH, $validationRule)){
            if (substr($fieldOnMethod, -1) !== self::getNextIndexValue(self::$RULE_END_WITH, $validationRule)){
                return str_replace(
                    ['{key}', '{val}'],
                    [self::get_key($key, $validationRule), self::getNextIndexValue(self::$RULE_END_WITH, $validationRule)],
                    Message::validation('end_with')
                );
            }
        }
    }
}