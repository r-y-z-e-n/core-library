<?php

namespace Ryzen\CoreLibrary;

use DateTime;
use Exception;
use stdClass;

/**
 * @author razoo.choudhary@gmail.com
 * Class Functions
 * @package Ryzen\CoreLibrary
 */

class Functions
{
    protected Ry_Zen $main;

    /**
     * Functions constructor.
     */

    public function __construct()
    {
        $this->main =   Ry_Zen::$main;
    }

    /*
     * Secures your data string helps prevent malicious input
     * */
    public function Ry_Secure($string, $br = true, $strip = 0)
    {
        $string = trim($string);
        $string = $this->Ry_Clean_String($string);
        $string = htmlspecialchars($string, ENT_QUOTES);

        if ($br == true) {

            $string = str_replace('\r\n', " <br>", $string);
            $string = str_replace('\n\r', " <br>", $string);
            $string = str_replace('\r', " <br>", $string);
            $string = str_replace('\n', " <br>", $string);

        } else {

            $string = str_replace('\r\n', "", $string);
            $string = str_replace('\n\r', "", $string);
            $string = str_replace('\r', "", $string);
            $string = str_replace('\n', "", $string);

        }

        if ($strip == 1) {

            $string = stripslashes($string);
        }

        return str_replace('&amp;#', '&#', $string);
    }

    /*
     * String Cleaner
     * */
    public function Ry_Clean_String($string)
    {
        return preg_replace("/&#?[a-z0-9]+;/i", "", $string);
    }

    /**
     * @throws Exception
     */

    /*
     * Generates Random 32 Byte String as CSRF Token
     * */
    public function Ry_Generate_CSRF():string
    {
        return $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    /*
     * Matched the CSRF Token Provided With The Generated Once
     * */
    public function Ry_Match_CSRF($token): bool
    {
        if (isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] !== '' && hash_equals($token, $_SESSION['csrf_token'])) {

            return true;

        } else {

            return false;
        }
    }

    /*
     * Redirects Page to the desired location
     * */
    public function redirect($url)
    {
        $redirect = 'Location:'.$this->main->Root_DIR.$url;
        header($redirect);
    }

    /*
     * TypeCasts Object Associative Array
     * */
    public function Ry_ObjectToArray($obj)
    {
        if (is_object($obj))
            $obj = (array)$obj;
        if (is_array($obj)) {
            $new = array();
            foreach ($obj as $key => $val) {
                $new[$key] = array($val);
            }
        } else {
            $new = $obj;
        }
        return $new;
    }

    /*
     * TypeCasts Array To Object
     * */
    public function Ry_ArrayToObject($array): stdClass
    {
        $object = new stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = ToObject($value);
            }
            if (isset($value)) {
                $object->$key = $value;
            }
        }
        return $object;
    }

    /*
     * Curls URL
     * */
    public function Ry_Curl_Url($url)
    {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        return curl_exec($ch);
    }

    /*
     * Encrypts Your String into secured unreadable hash
     * */
    public function Ry_Encrypt($data): string
    {
        $iv         = substr(sha1(mt_rand()), 0, 16);
        $password   = sha1($this->main->password);

        $salt       = sha1(mt_rand());
        $saltWithPassword = hash('sha256', $password, $salt);

        $encryption = openssl_encrypt($data, $this->main->encMethod, "$saltWithPassword", null, $iv);

        return "$iv:$salt:$encryption";
    }

    /*
     * Decrypts the unreadable hash to readable string
     * */
    public function Ry_Decrypt($encryptedData){

        $password       = sha1($this->main->password);
        $components     = explode(':', $encryptedData);

        $iv             = $components[0];
        $salt           = hash('sha256', $password,$components[1]);
        $encrypted_data = $components[2];

        $decryption     = openssl_decrypt($encrypted_data, $this->main->encMethod, $salt,null, $iv);
        if($decryption === false)
            return false;
        $msg = substr($decryption, 41);
        return $decryption;
    }

    /*
     * Strips Long Texts into Short Texts
     * */
    public function Ry_Strip_Long_Text($string, $length): string{
        $string = strip_tags($string);
        if (strlen($string) > $length) {
            $stringCut = substr($string, 0, $length);
            $endPoint = strrpos($stringCut, ' ');
            $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
        }
        return $string;
    }

    /**
     * @throws Exception
     */

    /*
     * Time Structure
     * */
    public function Ry_Time_Completed($datetime, $full = false): string{
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /*
     * Returns the current browser information
     * */
    public function Ry_Get_Browser(): array
    {
        $u_agent        = $_SERVER['HTTP_USER_AGENT'];
        $browser_name   = 'Unknown';
        $platform       = 'Unknown';
        $version        = '';

        if (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        } elseif (preg_match('/iphone|IPhone/i', $u_agent)) {
            $platform = 'IPhone Web';
        } elseif (preg_match('/android|Android/i', $u_agent)) {
            $platform = 'Android Web';
        } else if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $u_agent)) {
            $platform = 'Mobile';
        } else if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }

        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $browser_name = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $browser_name = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $browser_name = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $browser_name = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $browser_name = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $browser_name = 'Netscape';
            $ub = "Netscape";
        }

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        if (!preg_match_all($pattern, $u_agent, $matches)) {

        }

        $i = count($matches['browser']);
        if ($i != 1) {

            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }

        } else {
            $version = $matches['version'][0];
        }

        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent'     => $u_agent,
            'name'          => $browser_name,
            'version'       => $version,
            'platform'      => $platform,
            'pattern'       => $pattern,
            'ip_address'    => $this->Ry_Get_Ip_Address(),
        );
    }

    /*
     * Returns the IP address Of the user
     * */
    public function Ry_Get_Ip_Address()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::validate_ip($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }

        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::validate_ip($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }

        if (!empty($_SERVER['HTTP_FORWARDED']) && self::validate_ip($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    /*
     * IP address Validator
     * */
    public function validate_ip($ip): bool
    {
        if (strtolower($ip) === 'unknown')
            return false;
        $ip = ip2long($ip);
        if ($ip !== false && $ip !== -1) {
            $ip = sprintf('%u', $ip);
            if ($ip >= 0 && $ip <= 50331647)
                return false;
            if ($ip >= 167772160 && $ip <= 184549375)
                return false;
            if ($ip >= 2130706432 && $ip <= 2147483647)
                return false;
            if ($ip >= 2851995648 && $ip <= 2852061183)
                return false;
            if ($ip >= 2886729728 && $ip <= 2887778303)
                return false;
            if ($ip >= 3221225984 && $ip <= 3221226239)
                return false;
            if ($ip >= 3232235520 && $ip <= 3232301055)
                return false;
            if ($ip >= 4294967040)
                return false;
        }
        return true;
    }
}