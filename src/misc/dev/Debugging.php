<?php

namespace Ryzen\CoreLibrary\misc\dev;

class Debugging
{
    /**
     * @param bool $type
     */
    public static function preventDebugging(bool $type){
        if( $type === true){
            ini_set('gd.jpeg_ignore_warning', 1);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            ini_set('max_execution_time', 0);
            header('Cache-Control: max-age=846000');
            error_reporting(0);
        }
    }
}