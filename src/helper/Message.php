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
        if($messageKey == 'email'){
            return ValidationMessage::$email;
        }
        if($messageKey == 'url'){
            return ValidationMessage::$url;
        }
        if($messageKey == 'starts_with'){
            return ValidationMessage::$starts_with;
        }
        if($messageKey == 'end_with'){
            return ValidationMessage::$end_with;
        }
    }
}