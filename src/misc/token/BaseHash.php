<?php

namespace Ryzen\CoreLibrary\misc\token;

use Ryzen\CoreLibrary\Ry_Zen;

class BaseHash
{
    protected array $supportedHash = ['bcrypt','md5'];

    /**
     * @return string
     */
    public function getHashingType() :string {
        if(in_array(Ry_Zen::$main->hashingMethod, $this->supportedHash)){
            return Ry_Zen::$main->hashingMethod;
        }
        return 'bcrypt';
    }
}