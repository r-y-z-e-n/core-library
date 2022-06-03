<?php

namespace Ryzen\CoreLibrary\misc\token;

class Token
{
    protected static string $alphabet      = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    protected static string $numeric       = "0123456789";
    protected static string $character     = "abcdefghijklmnopqrstuvwxyz";
    protected static string $alphaNumeric  = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    protected static string $specialChar   = " !\"#$%&'()*+,-./:;<=>?@[\]^_`{|}~";
}