<?php

namespace Ryzen\CoreLibrary;

use Exception;
use Ryzen\DbBuilder\DbBuilder;
use Ryzen\CoreLibrary\helper\BaseFunctions;
use Ryzen\CoreLibrary\helper\Authentication;

class Auth extends Authentication
{
    /**
     * @param int $user_id
     * @return bool
     */
    public static function check( int $user_id = 0 ): bool {

        if (Session::has('user_id')) $user_id = self::getLoggedUserSession(Session::get('user_id'));
        if (Cookie::has('user_id'))  $user_id = self::getLoggedUserSession(Cookie::get('user_id'));

        if (is_numeric($user_id) && $user_id > 0)  return true;
        return false;
    }

    /**
     * @return int|string
     */
    public static function getUserId() {
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
        $getUser  = Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->tableUsers)->where('user_email', '=',$username)->orWhere('user_username', '=',$username)->orWhere('user_phone_number', '=',$username)->get();
        if ($getUser && Ry_Zen::$main->dbBuilder->numRows() > 0) {
            if(Authentication::validate_hash($password, $getUser->user_password)){
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $username
     * @param bool $rememberLogin
     * @return bool
     * @throws Exception
     */

    public static function createNewLoginWithUsername(string $username, bool $rememberLogin = false): bool {
        if(self::check()) exit('Authentication Error. User already Logged in.');
        if (self::createNewLoginWithId(self::getIdFromUsername($username), $rememberLogin)) return true;
        return false;
    }

    /**
     * @param int $user_id
     * @param bool $rememberLogin
     * @return bool
     * @throws Exception
     */

    public static function createNewLoginWithId(int $user_id, bool $rememberLogin = false): bool {
        if(self::check()) exit('Authentication Error. User already Logged in.');
        self::flushOldLogin();
        $session = $_SESSION['user_id'] = self::createNewUserSession($user_id);
        if($rememberLogin) Cookie::put('user_id' , $session);
        return true;
    }

    /**
     * @param array $data
     * @param string $table
     * @param string $logicalOperator
     * @return bool
     */

    public static function checkValueExistence(array $data, string $table, string $logicalOperator = "AND"): bool {
        return Functions::checkValueInTable($data, $table, $logicalOperator);
    }

    /**
     * @param int $user_id
     * @param false $in_array
     * @return array|false|int|mixed|DbBuilder|string
     */

    public static function getUserData(int $user_id, bool $in_array = false) {
        if (empty($user_id) || !is_numeric($user_id)) return false;
        $user_data  =   Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->tableUsers)->where('user_id',Functions::safeString($user_id))->get();
        return !$in_array ? $user_data : (array) $user_data;
    }

    /**
     * @param string $username
     * @return array|false|string|string[]
     */

    public static function getIdFromUsername(string $username) {
        $username = Functions::safeString($username);
        $getUser  = Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->tableUsers)->where('user_email', '=',$username)->orWhere('user_username', '=',$username)->orWhere('user_phone_number', '=',$username)->get();

        if ($getUser && Ry_Zen::$main->dbBuilder->numRows() > 0)  return Functions::safeString($getUser->user_id);
        return false;
    }

    /**
     * @return bool
     */

    public static function logout(): bool {
        if(BaseFunctions::checkTable(Ry_Zen::$main->tableUsersSessions)){
            if (Session::has('user_id'))  Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->tableUsersSessions)->where('session_id',Session::get('user_id'))->delete();
            if (Cookie::has('user_id'))  Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->tableUsersSessions)->where('session_id',$_COOKIE['user_id'])->delete();
        }
        self::flushOldLogin();
        Session::flush();
        Cookie::flush();
        $_SESSION = array();
        unset($_SESSION);
        session_unset();
        return true;
    }
}