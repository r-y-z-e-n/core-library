<?php

namespace Ryzen\CoreLibrary\helper;

use Ryzen\CoreLibrary\helper\messages\ValidationMessage;

class Message
{
    /**
     * @param $messageKey
     * @return string
     */

    public static function validation( $messageKey ) : string{
        if($messageKey == 'required'){
            return ValidationMessage::$required;
        }
        if($messageKey == 'minimum'){
            return ValidationMessage::$minimum;
        }
        if($messageKey == 'maximum'){
            return ValidationMessage::$maximum;
        }
        if($messageKey == 'unique'){
            return ValidationMessage::$unique;
        }
        if($messageKey == 'matches'){
            return ValidationMessage::$matches;
        }
        if($messageKey == 'date'){
            return ValidationMessage::$date;
        }
    }
}