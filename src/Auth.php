<?php

namespace Ryzen\CoreLibrary;

/**
 * @author razoo.choudhary@gmail.com
 * Class Auth
 * @package Ryzen\CoreLibrary
 */

class Auth
{
    protected \PDO $pdo;
    protected Ry_Zen $main;

    /**
     * Auth constructor.
     */

    public function __construct()
    {
        $this->main         = Ry_Zen::$main;
        $this->pdo          = $this->main->pdo;
    }

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

    public function Ry_Get_User_From_Session_ID(string $session_id, string $platform = 'web')
    {

        $session_id = $this->main->function->Ry_Secure($session_id);
        $statement = $this->pdo->prepare("SELECT * FROM " . $this->main->T_SESSION . " WHERE `session_id`= :session_id LIMIT 1");
        $statement->bindValue(':session_id', $session_id);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            if (empty($result['platform_details']) && $result['platform'] == 'web') {
                $userBrowser = json_encode($this->main->function->Ry_Get_Browser());
                if (isset($result['platform_details'])) {
                    $this->main->database->Ry_Update_Data(['platform_details' => $userBrowser], $this->main->T_SESSION, ['id' => $result['id']]);
                }
            }
            return $result['user_id'];
        }
        return false;
    }

    public function Ry_Create_Login_Session(int $user_id = 0)
    {
        if (empty($user_id)) {
            return false;
        }

        $user_id    = $this->main->function->Ry_Secure($user_id);
        $hash       = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
        $query      = $this->pdo->prepare("DELETE FROM " . $this->main->T_SESSION . " WHERE `session_id` = :session_id");
        $query->bindValue(':session_id', $hash);

        if ($query->execute()) {

            $userBrowser        = json_encode($this->main->function->Ry_Get_Browser());
            $deleteSameSession  = $this->pdo->prepare("DELETE FROM " . $this->main->T_SESSION . " WHERE platform_details = :platform_details");
            $deleteSameSession->bindValue(':platform_details', $userBrowser);
            $this->main->database->Ry_Insert_Data(['user_id' => $user_id, 'session_id' => $hash, 'platform' => 'web', 'platform_details' => $userBrowser], $this->main->T_SESSION);

            return $hash;
        }
        return false;
    }

    public function Ry_Is_Valid_Sign_In($username, $password): bool
    {
        $username = $this->main->function->Ry_Secure($username);
        $password = $this->main->function->Ry_Secure($password);

        $getUser  = $this->pdo->query("SELECT * FROM " . $this->main->T_USERS . " WHERE `user_email` = '$username' OR `user_username`='$username' OR `user_phone_number` = '$username'");

        if ($getUser->execute() && $getUser->rowCount() > 0) {
            $user = $getUser->fetch(\PDO::FETCH_ASSOC);
            if (password_verify($password, $user['user_password'])) {
                return true;
            }
        }
        return false;
    }

    public function Ry_Get_User_Id($username)
    {
        $username = $this->main->function->Ry_Secure($username);

        $getUser  = $this->pdo->query("SELECT `user_id` FROM " . $this->main->T_USERS . " WHERE `user_email` = '$username' OR `user_username` = '$username' OR `user_phone_number` = '$username'");
        if ($getUser->execute() && $getUser->rowCount() > 0) {
            return $this->main->function->Ry_Secure($getUser->fetch(\PDO::FETCH_ASSOC)['user_id']);
        }
        return false;
    }

    public function Ry_Create_Login($username,$rememberLogin = NULL): bool
    {
        $username   = $this->main->function->Ry_Secure($username);
        $user_id    = $this->Ry_Get_User_Id($username);
        if ($this->Ry_Login_With_Id($user_id,$rememberLogin)) {
            return true;
        }
        return false;
    }

    public function Ry_Login_With_Id($user_id,$rememberLogin): bool
    {
        $session = $_SESSION['user_id'] = $this->Ry_Create_Login_Session($user_id);
        if($rememberLogin){
            setcookie("user_id", $session, time() + (10 * 365 * 24 * 60 * 60));
        }
        return true;
    }
    public function Ry_User_Data(int $user_id)
    {
        if (empty($user_id) || !is_numeric($user_id)) {

            return false;
        }
        $data = [];
        $user_id    = $this->main->function->Ry_Secure($user_id);
        $statement  = $this->pdo->query("SELECT * FROM " . $this->main->T_USERS . " WHERE `user_id` = '$user_id' ");
        $statement->execute();

        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function Ry_Sign_Out(): bool
    {
        session_unset();
        if (!empty($_SESSION['user_id'])) {
            $_SESSION['user_id'] = '';
            $query = $this->pdo->prepare("DELETE FROM ".$this->main->T_SESSION." WHERE `session_id` = '".$_SESSION['user_id']."'");
            $query->execute();
        }
        session_destroy();
        if (isset($_COOKIE['user_id'])) {
            $query = $this->pdo->prepare("DELETE FROM ".$this->main->T_SESSION." WHERE `session_id` = '".$_SESSION['user_id']."'");
            $query->execute();
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