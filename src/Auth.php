<?php

namespace Ryzen\CoreLibrary;

/**
 * @author razoo.choudhary@gmail.com
 * Class Auth
 * @package Ryzen\CoreLibrary
 */

class Auth
{

    /**
     * @return bool
     */

    public static function Ry_Is_Logged_In(): bool
    {
        if (Session::has('user_id')) {
            $user_id = self::Ry_Get_User_From_Session_ID(Session::get('user_id'));
            if (is_numeric($user_id) && !empty($user_id)) {
                return true;
            }
        } elseif (isset($_COOKIE['user_id']) && !empty($_COOKIE['user_id'])) {
            $user_id = self::Ry_Get_User_From_Session_ID($_COOKIE['user_id']);
            if (is_numeric($user_id) && !empty($user_id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $session_id
     * @param string $platform
     * @return mixed
     */

    public static function Ry_Get_User_From_Session_ID(string $session_id, string $platform = 'web')
    {
        $session_id = Functions::Ry_Secure($session_id);
        $statement  = Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->select('*')->where('session_id',$session_id)->limit(1)->get();

        if (Ry_Zen::$main->dbBuilder->numRows() > 0) {
            if (empty($statement->platform_details) && $statement->platform == 'web') {
                $userBrowser = json_encode(Functions::Ry_Get_Browser());
                if (isset($statement->platform_details)) {
                    Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('id','=',$statement->id)->update(['platform_details' => $userBrowser]);
                }
            }
            return $statement->user_id;
        }
        return false;
    }

    /**
     * @param int $user_id
     * @return false|string
     */

    public static function Ry_Create_Login_Session(int $user_id = 0){
        if (empty($user_id)) { return false; }

        $user_id    = Functions::Ry_Secure($user_id);
        $hash       = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
        $query      = Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('session_id',$hash)->delete();

        if ($hash) {
            $userBrowser        = json_encode(Functions::Ry_Get_Browser());
            Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('platform_details',$userBrowser)->delete();
            Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->insert(['user_id' => $user_id, 'session_id' => $hash, 'platform' => 'web', 'platform_details' => $userBrowser]);
            return $hash;
        }
        return false;
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */

    public static function Ry_Is_Valid_Sign_In($username, $password): bool
    {
        $username = Functions::Ry_Secure($username);
        $getUser  = Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_USERS)->where('user_email', '=',$username)->orWhere('user_username', '=',$username)->orWhere('user_phone_number', '=',$username)->get();

        if ($getUser && Ry_Zen::$main->dbBuilder->numRows() > 0) {
            if (password_verify(Functions::Ry_Secure($password), $getUser->user_password)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $username
     * @return array|false|string|string[]
     */

    public static function Ry_Get_User_Id($username)
    {
        $username = Functions::Ry_Secure($username);
        $getUser  = Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_USERS)->where('user_email', '=',$username)->orWhere('user_username', '=',$username)->orWhere('user_phone_number', '=',$username)->get();

        if ($getUser && Ry_Zen::$main->dbBuilder->numRows() > 0) {
            return Functions::Ry_Secure($getUser->user_id);
        }
        return false;
    }

    /**
     * @param $username
     * @param null $rememberLogin
     * @return bool
     */

    public static function Ry_Create_Login($username, $rememberLogin = NULL): bool
    {
        if (self::Ry_Login_With_Id(self::Ry_Get_User_Id($username), $rememberLogin)) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @param $rememberLogin
     * @return bool
     */

    public static function Ry_Login_With_Id($user_id, $rememberLogin): bool
    {
        $session = $_SESSION['user_id'] = self::Ry_Create_Login_Session($user_id);
        if($rememberLogin){
            setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
        }
        return true;
    }

    /**
     * @param array $data
     * @param $table
     * @param string $logicalOperator
     * @return bool
     */

    public static function Ry_Value_Exists(array $data, $table, string $logicalOperator = "AND"): bool
    {

        $supported_Logical_Operator = ["AND","OR"];

        if(!in_array(strtoupper($logicalOperator),$supported_Logical_Operator)){
            return false;
        }

        $attributes = array_keys($data);
        $clause     = ' WHERE ' . implode(strtoupper(" ".$logicalOperator." "), array_map(fn($attr) => "$attr = :$attr", $attributes));

        $statement  = Ry_Zen::$main->dbBuilder->pdo->prepare("SELECT * FROM $table $clause");
        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
        }
        if ($statement->execute() && $statement->rowCount() > 0) {

            return true;
        }
        return false;
    }

    /**
     * @param int $user_id
     * @return array|false
     */

    public static function Ry_User_Data(int $user_id)
    {
        if (empty($user_id) || !is_numeric($user_id)) { return false; }
        return (array) Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_USERS)->where('user_id',Functions::Ry_Secure($user_id))->get();
    }

    /**
     * @return bool
     */

    public static function Ry_Sign_Out(): bool
    {
        session_unset();
        if (Session::has('user_id')) {
            Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('session_id',Session::get('user_id'))->delete();
            Session::forget('user_id');
        }
        session_destroy();
        if (isset($_COOKIE['user_id'])) {
            Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('session_id',$_COOKIE['user_id'])->delete();
            $_COOKIE['user_id'] = '';
            unset($_COOKIE['user_id']);
            setcookie('user_id', null, -1);
            setcookie('user_id', null, -1,'/');
        }
        $_SESSION = array();
        unset($_SESSION);
        return true;
    }
}