<?php

namespace Ryzen\CoreLibrary\misc\token;

use Ryzen\CoreLibrary\Ry_Zen;
use Ryzen\CoreLibrary\event\Hash;

class BaseHash
{
    protected array $supportedHash = ['bcrypt','md5'];

    /**
     * @return mixed|string
     */

    public function getHashingType(){
        if(in_array(Ry_Zen::$main->Hashing_Method, $this->supportedHash)){
            return Ry_Zen::$main->Hashing_Method;
        }
        return 'bcrypt';
    }

    /**
     * @param $clearString
     * @return false|string|null
     */

    public function rehash($clearString){
        if(Ry_Zen::$main->Password_ReHash == true){
            return Hash::make($clearString);
        }
        return false;
    }
}