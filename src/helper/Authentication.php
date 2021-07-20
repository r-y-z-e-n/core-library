<?php

namespace Ryzen\CoreLibrary\helper;

use Exception;
use Ryzen\CoreLibrary\Cookie;
use Ryzen\CoreLibrary\Ry_Zen;
use Ryzen\CoreLibrary\Session;
use Ryzen\CoreLibrary\Functions;

class Authentication
{

    /**
     * @param $session_id
     * @return false|int
     */

    protected static function getUserIdSessionStorage($session_id){
        $user_id = '';
        if(Session::has('user_session_storage_id')){
            if($session_id == Session::get('user_id')){
                $user_id = (int) Functions::safeString(Functions::decrypt(Session::get('user_session_storage_id')));
            }
        }
        if(Cookie::has('user_session_storage_id')){
            if($session_id == Cookie::get('user_id')){
                $user_id = (int) Functions::safeString(Functions::decrypt(Cookie::get('user_session_storage_id')));
            }
        }
        if(is_numeric($user_id) && !empty($user_id) && $user_id > 0){
            return $user_id;
        }
        return false;
    }

    /**
     * @param string $session_id
     * @param string $platform
     * @return mixed
     */

    protected static function getLoggedUserSession(string $session_id, string $platform = 'web') {
        $session_id = Functions::safeString($session_id);
        if(BaseFunctions::checkTable(Ry_Zen::$main->T_SESSION)){
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
        }else{ return self::getUserIdSessionStorage($session_id); } return false;
    }

    /**
     * @param int $user_id
     * @return false|string
     * @throws Exception
     */

    protected static function createNewUserSession(int $user_id = 0){
        $user_id    = Functions::safeString($user_id);
        $hash       = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
        if(BaseFunctions::checkTable(Ry_Zen::$main->T_SESSION)){
            if ($hash) {
                Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('session_id',$hash)->delete();
                Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->where('platform_details',json_encode(Functions::getUserBrowser()))->delete();
                Ry_Zen::$main->dbBuilder->table(Ry_Zen::$main->T_SESSION)->insert(['user_id' => $user_id, 'session_id' => $hash, 'platform' => 'web', 'platform_details' => json_encode(Functions::getUserBrowser())]);
                return $hash;
            }
        }
        if(BaseFunctions::checkTable(Ry_Zen::$main->T_SESSION) == false){
            if($hash){
                $user_id = Functions::encrypt($user_id);
                if(Session::put('user_session_storage_id',$user_id) && Cookie::put('user_session_storage_id', $user_id)){
                    return $hash;
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */

    protected static function flushOldLogin() : bool{
        if (!empty(Session::get('user_id'))) {
            Session::forget('user_id');
        }
        if(!empty(Session::get('user_session'))){
            Session::forget('user_session');
        }
        if (!empty($_COOKIE['user_id'])) {
            Cookie::forget('user_id');
        }
        if (!empty($_COOKIE['user_session'])) {
            Cookie::forget('user_session');
        }
        return true;
    }
}