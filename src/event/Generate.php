<?php

namespace Ryzen\CoreLibrary\event;

use Exception;
use Ryzen\CoreLibrary\Functions;
use Ryzen\CoreLibrary\misc\token\Token;

class Generate extends Token
{
    /**
     * @param int|null $keyLength
     * @return string
     */

    public static function key( int $keyLength = null): string{
        return base64_encode(openssl_random_pseudo_bytes((!is_null($keyLength)) ? $keyLength : 32));
    }

    /**
     * @throws Exception
     */
    public static function token(int $length = null): string{
        if(function_exists('random_bytes')){
            return bin2hex(random_bytes((!is_null($length)) ? $length : 22));
        }
        return sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
    }

    /**
     * @param int $passwordLength
     * @return string
     */

    public static function password(int $passwordLength = 8): string {
        $passwordCharacters = Functions::safeString(self::$alphabet . self::$alphaNumeric .  self::$numeric .  self::$character . self::$specialChar);
        $pass = array();
        $alphaLength = strlen($passwordCharacters) - 1;
        for ($i = 0; $i < $passwordLength; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $passwordCharacters[$n];
        }
        return implode($pass);
    }
}