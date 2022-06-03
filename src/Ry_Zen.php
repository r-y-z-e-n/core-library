<?php

namespace Ryzen\CoreLibrary;

use Exception;
use Ryzen\Oauth\Oauth;
use Ryzen\DbBuilder\DbBuilder;
use Ryzen\CoreLibrary\config\Application;
use Ryzen\CoreLibrary\config\BaseConfiguration;


class Ry_Zen extends BaseConfiguration
{
    public Oauth $oAuth;
    public DbBuilder $dbBuilder;
    public static  Ry_Zen $main;

    public string  $appRootPath;
    public string  $tableUsers;
    public string  $hashingMethod;
    public string  $tableUsersSessions;
    public string  $encryptionMethod;
    public string  $encryptionPassword;
    public string  $viewPath    =   './resources/view/';
    public string  $viewEngine  =   'php';

    /**
     * @throws Exception
     */
    public function __construct() {
        $this->loadENV();
        $this->initApp();
    }

    /**
     * @return void
     */
    private function initApp() {
        if (session_status() === PHP_SESSION_NONE)  session_start();
        $this->tableUsers           =   Functions::safeString($_ENV['table_users'])         ??  "users";
        $this->hashingMethod        =   Functions::safeString($_ENV['hashing_method'] )     ??  "bcrypt";
        $this->appRootPath          =   Functions::safeString($_ENV['app_url'])             ??  "";
        $this->encryptionMethod     =   Functions::safeString($_ENV['encryption_method'])   ??  "AES-128-CBC";
        $this->tableUsersSessions   =   Functions::safeString($_ENV['table_sessions'])      ??  "users_sessions";
        $this->encryptionPassword   =   Functions::safeString($_ENV['encryption_password']) ??  (new Application())->AppKey();
        $this->dbBuilder            =   BaseConfiguration::initializeDB();
        $this->oAuth                =   BaseConfiguration::initializeOauth();
        self::$main                 =   $this;
        self::initializeRuntimeEnvironment();
    }
}