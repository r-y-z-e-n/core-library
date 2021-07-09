<?php

namespace Ryzen\CoreLibrary;

/**
 * @author razoo.choudhary@gmail.com
 * Class Auth
 * @package Ryzen\CoreLibrary
 */

class Auth
{
    protected ?\PDO $pdo;
    protected Ry_Zen $main;

    /**
     * Auth constructor.
     */

    public function __construct()
    {
        $this->main = Ry_Zen::$main;
        $this->pdo  = $this->main->pdo;
    }

    /*
     * Returns Whether The User is Logged in or Not (boolean)
     * */
    public function Ry_Is_Logged_In(): bool
    {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            $user_id = $this->Ry_Get_User_From_Session_ID($_SESSION['user_id']);
            if (is_numeric($user_id) && !empty($user_id)) {
                return true;
            }
        } elseif (isset($_COOKIE['user_id']) && !empty($_COOKIE['user_id'])) {
            $user_id = $this->Ry_Get_User_From_Session_ID($_COOKIE['user_id']);
            if (is_numeric($user_id) && !empty($user_id)) {
                return true;
            }
        }
        return false;
    }

    /*
     * Returns user_id from the session
     * */
    public function Ry_Get_User_From_Session_ID(string $session_id, string $platform = 'web')
    {
        $session_id = $this->main->function->Ry_Secure($session_id);
        $statement  = $this->main->dbBuilder->table($this->main->T_SESSION)->select('*')->where('session_id',$session_id)->limit(1)->get();

        if ($this->main->dbBuilder->numRows() > 0) {
            if (empty($statement->platform_details) && $statement->platform == 'web') {
                $userBrowser = json_encode($this->main->function->Ry_Get_Browser());
                if (isset($statement->platform_details)) {
                    $this->main->dbBuilder->table($this->main->T_SESSION)->where('id','=',$statement->id)->update(['platform_details' => $userBrowser]);
                }
            }
            return $statement->id;
        }
        return false;
    }

    /*
     * Creates New Login Session For The User
     * */
    public function Ry_Create_Login_Session(int $user_id = 0)
    {
        if (empty($user_id)) { return false; }

        $user_id    = $this->main->function->Ry_Secure($user_id);
        $hash       = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
        $query      = $this->main->dbBuilder->table($this->main->T_SESSION)->where('session_id',$hash)->delete();

        if ($query) {
            $userBrowser        = json_encode($this->main->function->Ry_Get_Browser());
            $this->main->dbBuilder->table($this->main->T_SESSION)->where('platform_details',$userBrowser)->delete();
            $this->main->dbBuilder->table($this->main->T_SESSION)->insert(['user_id' => $user_id, 'session_id' => $hash, 'platform' => 'web', 'platform_details' => $userBrowser]);
            return $hash;
        }
        return false;
    }

    /*
     * Checks whether the username and password is valid or not
     * */
    public function Ry_Is_Valid_Sign_In($username, $password): bool
    {
        $username = $this->main->function->Ry_Secure($username);
        $getUser  = $this->main->dbBuilder->table($this->main->T_USERS)->where('user_email', '=',$username)->orWhere('user_username', '=',$username)->orWhere('user_phone_number', '=',$username)->get();

        if ($getUser && $this->main->dbBuilder->numRows() > 0) {
            if (password_verify($this->main->function->Ry_Secure($password), $getUser->user_password)) {
                return true;
            }
        }
        return false;
    }

    /*
     * Returns user_id (primary key ) in exchange for username
     * */
    public function Ry_Get_User_Id($username)
    {
        $username = $this->main->function->Ry_Secure($username);
        $getUser  = $this->main->dbBuilder->table($this->main->T_USERS)->where('user_email', '=',$username)->orWhere('user_username', '=',$username)->orWhere('user_phone_number', '=',$username)->get();

        if ($getUser && $this->main->dbBuilder->numRows() > 0) {
            return $this->main->function->Ry_Secure($getUser->user_id);
        }
        return false;
    }

    /*
     * Logs User in
     * */
    public function Ry_Create_Login($username, $rememberLogin = NULL): bool
    {
        if ($this->Ry_Login_With_Id($this->Ry_Get_User_Id($username), $rememberLogin)) {
            return true;
        }
        return false;
    }

    /*
     * Logs user in with ID
     * */
    public function Ry_Login_With_Id($user_id, $rememberLogin): bool
    {
        $session = $_SESSION['user_id'] = $this->Ry_Create_Login_Session($user_id);
        if($rememberLogin){
            setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
        }
        return true;
    }

    /*
     * Returns user data from ID
     * */
    public function Ry_User_Data(int $user_id)
    {
        if (empty($user_id) || !is_numeric($user_id)) { return false; }
        return (array) $this->main->dbBuilder->table($this->main->T_USERS)->where('user_id',$this->main->function->Ry_Secure($user_id))->get();
    }

    /*
     * Signs Currently Logged in User oUT
     * */
    public function Ry_Sign_Out(): bool
    {
        session_unset();
        if (!empty($_SESSION['user_id'])) {
            $this->main->dbBuilder->table($this->main->T_SESSION)->where('session_id',$_SESSION['user_id'])->delete();
            $_SESSION['user_id'] = '';
        }
        session_destroy();
        if (isset($_COOKIE['user_id'])) {
            $this->main->dbBuilder->table($this->main->T_SESSION)->where('session_id',$_COOKIE['user_id'])->delete();
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