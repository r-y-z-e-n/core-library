<?php

namespace Ryzen\CoreLibrary;

use Exception;
use Ryzen\DbBuilder\DbBuilder;
use Ryzen\CoreLibrary\misc\token\BaseHash;
use Ryzen\CoreLibrary\helper\BaseFunctions;
use Ryzen\CoreLibrary\helper\Authentication;

class Auth extends Authentication
{
    /**
     * @return bool
     */

    public static function check(): bool {
        $user_id = '';
        if (Session::has('user_id')) {
            $user_id = self::getLoggedUserSession(Session::get('user_id'));
        } elseif (Cookie::has('user_id') && !empty(Cookie::get('user_id'))) {
            $user_id = self::getLoggedUserSession(Cookie::get('user_id'));
        }
        if (is_numeric($user_id) && !empty($user_id)) {
            return true;
        }
        return false;
    }

    /**
     * @return int|string
     */

    public static function getUserId(){
        if(self::check()){
            $session_id = (!empty(Session::get('user_id'))) ? Session::get('user_id') : Cookie::get('user_id');
            $user_id    = self::getLoggedUserSession($session_id);
            if(empty($user_id) || !is_numeric($user_id) || $user_id < 0){
                return "User Identification Failed. ERROR [707] ";
            }
            return (int) Functions::safeString($user_id);
        }
        return 'No User Was Authenticated.';
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */

    public static function attempt($username, $password): bool {
        $username = Functions::safeString($username);
        $getUser  = Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_USERS)->where('user_email', '=',$username)->orWhere('user_username', '=',$username)->orWhere('user_phone_number', '=',$username)->get();
        if ($getUser && Ry_Zen::$main->dbBuilder->numRows() > 0) {
            if(Authentication::validate_hash($password, $getUser->user_password)){
                if((new BaseHash)->rehash($password)){
                    self::updateLoginHash((new BaseHash)->rehash($password));
                }
                return true;
            }
        }
        return false;
    }

    /**
     * @param $username
     * @param null $rememberLogin
     * @return bool
     * @throws Exception
     */

    public static function createNewLoginWithUsername($username, $rememberLogin = NULL): bool {
        if(self::check() == true){
            exit('Authentication Error. User already Logged in.');
        }
        if (self::createNewLoginWithId(self::getIdFromUsername($username), $rememberLogin)) {
            return true;
        }
        return false;
    }

    /**
     * @param $user_id
     * @param $rememberLogin
     * @return bool
     * @throws Exception
     */

    public static function createNewLoginWithId($user_id, $rememberLogin): bool {
        if(self::check() == true){
            exit('Authentication Error. User already Logged in.');
        }
        self::flushOldLogin();
        $session = $_SESSION['user_id'] = self::createNewUserSession($user_id);
        if($rememberLogin){
            Cookie::put('user_id' , $session);
        }
        return true;
    }

    /**
     * @param array $data
     * @param $table
     * @param string $logicalOperator
     * @return bool
     */

    public static function checkValueExistence(array $data, $table, string $logicalOperator = "AND"): bool {
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
     * @param false $in_array
     * @return array|false|int|mixed|DbBuilder|string
     */

    public static function getUserData(int $user_id, bool $in_array = false) {
        if (empty($user_id) || !is_numeric($user_id)) { return false; }
        $user_data  =   Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_USERS)->where('user_id',Functions::safeString($user_id))->get();
        if($in_array == false){
            return $user_data;
        }
        if($in_array == true){
            return (array) $user_data;
        }
        return false;
    }

    /**
     * @param $username
     * @return array|false|string|string[]
     */

    public static function getIdFromUsername($username)
    {
        $username = Functions::safeString($username);
        $getUser  = Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_USERS)->where('user_email', '=',$username)->orWhere('user_username', '=',$username)->orWhere('user_phone_number', '=',$username)->get();

        if ($getUser && Ry_Zen::$main->dbBuilder->numRows() > 0) {
            return Functions::safeString($getUser->user_id);
        }
        return false;
    }

    /**
     * @return bool
     */

    public static function logout(): bool
    {
        session_unset();
        if(BaseFunctions::checkTable(Ry_Zen::$main->T_SESSION)){
            if (Session::has('user_id')) {
                Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('session_id',Session::get('user_id'))->delete();
            }
            if (Cookie::has('user_id')) {
                Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('session_id',$_COOKIE['user_id'])->delete();
            }
        }
        self::flushOldLogin();
        Session::flush();
        Cookie::flush();
        $_SESSION = array();
        unset($_SESSION);
        return true;
    }
}