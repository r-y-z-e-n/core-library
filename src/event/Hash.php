<?php

namespace Ryzen\CoreLibrary\event;

use Ryzen\CoreLibrary\Functions;
use Ryzen\CoreLibrary\misc\token\BaseHash;

class Hash extends BaseHash
{
    /**
     * @param string $string
     * @param array $options
     * @return false|string|null
     */
    public static function make(string $string, array $options = []){
        return password_hash(Functions::safeString($string), PASSWORD_DEFAULT, $options);
    }

    /**
     * @param string $clearString
     * @param string $encryptedString
     * @return bool
     */
    public static function check(string $clearString, string $encryptedString): bool {
        return password_verify(Functions::safeString($clearString), Functions::safeString($encryptedString));
    }
}