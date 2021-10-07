<?php

namespace Ryzen\CoreLibrary;

use Dotenv;
use Ryzen\Oauth\Oauth;
use Ryzen\DbBuilder\DbBuilder;
use Ryzen\CoreLibrary\misc\dev\Debugging;
use Ryzen\CoreLibrary\config\Application;

class Ry_Zen
{
    public Oauth $oauth;
    public DbBuilder $dbBuilder;
    public static  Ry_Zen $main;
    public string  $Table_Users;
    public string  $Table_Sessions;
    public string  $Hashing_Method;
    public bool    $Password_ReHash;
    public string  $Encryption_Method;
    public string  $Encryption_Password;
    public string  $Application_Root_Directory;
    public string  $View_Path      = './resources/view/';
    public string  $View_Extension = 'php';

    public function __construct(string $application_root_dir)
    {
        $dotenv = Dotenv\Dotenv::createImmutable($application_root_dir);
        $_ENV['app_url']   = $application_root_dir;
        $dotenv->load();
        $_ENV   = array_change_key_case($_ENV, CASE_LOWER);
        $this->oauth                        =   new Oauth(['app_id' => $_ENV['ryzen_app_id'],'app_url' => $_ENV['ryzen_app_url'],'app_secret' => $_ENV['ryzen_app_secret']]);
        $this->Table_Users                  =   (isset($_ENV['table_users']) && !empty($_ENV['table_users'])    ? $_ENV['table_users'] : 'users');
        $this->Table_Sessions               =   (isset($_ENV['table_sessions']) && !empty($_ENV['table_sessions']) ? $_ENV['table_sessions'] : 'users_sessions');
        $this->Hashing_Method               =   (isset($_ENV['hashing_method']) && !empty($_ENV['hashing_method']) ? $_ENV['hashing_method'] : 'bcrypt');
        $this->Password_ReHash              =   (bool)(isset($_ENV['password_rehash']) && !empty($_ENV['password_rehash']) ? $_ENV['password_rehash'] : false);
        $this->Encryption_Method            =   (isset($_ENV['encryption_method']) && !empty($_ENV['encryption_method']) ? $_ENV['encryption_method'] : 'AES-128-CBC');
        $this->Encryption_Password          =   (isset($_ENV['encryption_password']) && !empty($_ENV['encryption_password']) ? $_ENV['encryption_password'] : (new Application())->AppKey());
        $this->Application_Root_Directory   =   $application_root_dir;
        $this->dbBuilder                    =   new DbBuilder([
            'mysql_database_config' => [
                    'db_name'       => $_ENV['database_name'],
                    'db_prefix'     => $_ENV['database_prefixes'],
                    'db_host'       => $_ENV['database_hostname'],
                    'db_user'       => $_ENV['database_username'],
                    'db_pass'       => $_ENV['database_password'],
                    'db_driver'     => $_ENV['database__drivers'],
                    'db_charset'    => $_ENV['database_charsets'],
                    'db_collation'  => $_ENV['database_collation'],
                    'db_port'       => $_ENV['database_curr_port'],
                    'db_debug'      => $_ENV['database_debugging'],
                ]
        ]);
        self::$main = $this;
        self::init();
    }

    public static function init(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if( isset($_ENV['app_mode']) && $_ENV['app_mode'] === 'production'){
            Debugging::preventDebugging(true);
        }else{
            Debugging::preventDebugging(false);
        }
    }
}