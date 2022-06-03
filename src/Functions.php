<?php

namespace Ryzen\CoreLibrary;

use DateTime;
use Exception;
use Ryzen\CoreLibrary\helper\BaseFunctions;

class Functions extends BaseFunctions
{

    /**
     * @param string $string
     * @param bool $breakString
     * @param int $strip
     * @return array|string|string[]
     */

    public static function safeString(string $string, bool $breakString = true, int $strip = 0) {
        $string = htmlspecialchars(self::cleanString(trim($string)), ENT_QUOTES);

        if ($breakString) {
            $string = str_replace('\r\n', " <br>", $string);
            $string = str_replace('\n\r', " <br>", $string);
            $string = str_replace('\r', " <br>", $string);
            $string = str_replace('\n', " <br>", $string);
        }
        if(!$breakString){
            $string = str_replace('\r\n', "", $string);
            $string = str_replace('\n\r', "", $string);
            $string = str_replace('\r', "", $string);
            $string = str_replace('\n', "", $string);
        }
        if ($strip == 1)  $string = stripslashes($string);
        return str_replace('&amp;#', '&#', $string);
    }

    /**
     * @param array $requestData
     * @return array
     */

    public static function safeRequest(array $requestData): array {
        foreach ($requestData as $key => $value){
            $value             = preg_replace('/on[^<>=]+=[^<>]*/m', '', $value);
            $requestData[$key] = self::safeString($value);
        }
        return $requestData;
    }

    /**
     * @param string $string
     * @return array|string|string[]|null
     */

    public static function cleanString(string $string) {
        return preg_replace("/&#?[a-z0-9]+;/i", "", $string);
    }

    /**
     * @return string
     * @throws Exception
     */

    public static function generateCsrf():string {
        $csrfToken = bin2hex(random_bytes(32));
        if(!Session::has('csrf_token')) Session::put('csrf_token', $csrfToken);
        return Session::get('csrf_token');
    }

    /**
     * @param string $token
     * @param bool $is_csrf_one_use
     * @return bool
     * @throws Exception
     */

    public static function checkCsrf(string $token, bool $is_csrf_one_use = false): bool
    {
        if (Session::has('csrf_token') && Session::get('csrf_token') !== '' && hash_equals($token, Session::get('csrf_token'))) {
            if($is_csrf_one_use) Session::forget('csrf_token') && self::generateCsrf();
            return true;
        }
        return false;
    }

    /**
     * @param string $string
     * @return false|string
     * @throws Exception
     */

    public static function createHmac(string $string){
        if(empty($string)) return "Missing Parameter";
        return hash_hmac('sha256', $string, self::generateCsrf());
    }

    /**
     * @param string $string
     * @param string $token
     * @return bool
     * @throws Exception
     */

    public static function checkHmac(string $string, string $token): bool {
        if(hash_equals(self::createHmac($string), $token)) return true;
        return false;
    }

    /**
     * @param string $url
     */

    public static function redirect(string  $url) {
        header("Location:".$url);
    }

    /**
     * @param string $url
     * @param bool $isPOST
     * @return bool|string
     */

    public static function curlUrl(string $url, bool $isPOST = false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, $isPOST);
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

    /**
     * @param $data
     * @return string
     * @throws Exception
     */

    public static function encrypt($data): string {
        $iv         = substr(sha1(mt_rand()), 0, 16);
        $password   = sha1(Ry_Zen::$main->encryptionPassword);
        $salt       = sha1(mt_rand());
        $saltWithPassword = hash('sha256', $password, $salt);
        $encryption = openssl_encrypt(self::safeString($data, false), Ry_Zen::$main->encryptionMethod, "$saltWithPassword", NULL, $iv);
        $keyChar    = "$iv:$salt:$encryption";
        if(strtolower(self::decrypt($keyChar)) == strtolower($data)) return $keyChar;
        throw new Exception('Encryption failed retry !!');
    }

    /**
     * @param $encryptedData
     * @return false|string
     */

    public static function decrypt($encryptedData){
        $password       = sha1(Ry_Zen::$main->encryptionPassword);
        $components     = explode(':', $encryptedData);
        $iv             = $components[0];
        $salt           = hash('sha256', $password,$components[1]);
        $encrypted_data = $components[2];
        $decryption     = openssl_decrypt($encrypted_data, Ry_Zen::$main->encryptionMethod, $salt,NULL, $iv);
        if(!$decryption) return false;
        substr(self::safeString($decryption, false), 41);
        return $decryption;
    }

    /**
     * @param string $string
     * @param int $length
     * @return string
     */

    public static function stripLongString(string  $string, int $length): string{
        $string = strip_tags($string);
        if (strlen($string) > $length) {
            $stringCut = substr($string, 0, $length);
            $endPoint = strrpos($stringCut, ' ');
            $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
        }
        return $string;
    }

    /**
     * @param $datetime
     * @param false $full
     * @return string
     * @throws Exception
     */

    public static function getTimeCompleted($datetime, bool $full = false): string{
        $now      = new DateTime;
        $ago      = new DateTime($datetime);
        $diff     = $now->diff($ago);
        $diff->w  = floor($diff->d / 7);
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

    /**
     * @return array
     */

    public static function getUserBrowser(): array {
        $u_agent        = $_SERVER['HTTP_USER_AGENT'];
        $browser_name   = 'Unknown';
        $platform       = 'Unknown';
        $ub             = '';

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

        if (!preg_match_all($pattern, $u_agent, $matches)) {}

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
            'ip_address'    => self::getUserIp(),
        );
    }

    /**
     * @return mixed
     */

    public static function getUserIp() {
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::validateIp($_SERVER['HTTP_X_FORWARDED'])) {
            return $_SERVER['HTTP_X_FORWARDED'];
        }

        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::validateIp($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::validateIp($_SERVER['HTTP_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_FORWARDED_FOR'];
        }

        if (!empty($_SERVER['HTTP_FORWARDED']) && self::validateIp($_SERVER['HTTP_FORWARDED'])) {
            return $_SERVER['HTTP_FORWARDED'];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @param $ip
     * @return bool
     */

    public static function validateIp($ip): bool
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

    /**
     * @param array $data
     * @param string $table
     * @param string $logicalOperator
     * @return bool
     */
    public static function checkValueInTable(array $data, string $table, string $logicalOperator = "AND") : bool{
        if(!in_array(strtoupper($logicalOperator),["AND","OR"])) return false;

        $attributes = array_keys($data);
        $clause     = ' WHERE ' . implode(strtoupper(" ".$logicalOperator." "), array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement  = Ry_Zen::$main->dbBuilder->pdo->prepare("SELECT * FROM"." $table $clause");

        foreach ($data as $key => $value) $statement->bindValue(":$key", $value);
        if ($statement->execute() && $statement->rowCount() > 0)  return true;
        return false;
    }
}