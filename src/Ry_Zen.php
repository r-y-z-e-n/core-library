<?php

namespace Ryzen\CoreLibrary;

use PDO;
use Ryzen\DbBuilder\DbBuilder;
use Ryzen\CoreLibrary\config\Application;
use Ryzen\CoreLibrary\misc\dev\Debugging;
use Ryzen\Oauth\Oauth;

class Ry_Zen
{

    /**
     * @var PDO|null
     */

    public ?PDO $pdo;
    public  $oauth;
    public DbBuilder $dbBuilder;
    public static Ry_Zen $main;

    /**
     * @var string|mixed
     */

    public string  $T_USERS;
    public string  $password;
    public string  $Root_DIR;
    public string  $T_SESSION;
    public string  $encMethod;
    public string  $hashMethod;
    public bool    $rehash;
    public string  $theme_url     = './resources/view/';
    public string  $viewExtension = 'php';

    /**
     * Ry_Zen constructor.
     * @param string $Root_DIRECTORY
     * @param $config
     */

    public function __construct(string $Root_DIRECTORY, $config)
    {
        self::$main         =   $this;
        self::init( $config );
        $this->Root_DIR     =   $Root_DIRECTORY;
        $this->password     =   ( isset($config['encryption_method']['password']) && !empty($config['encryption_method']['password']) ? $config['encryption_method']['password'] : (new Application())->AppKey());
        $this->encMethod    =   ( isset($config['encryption_method']['encryptionMethod']) && !empty($config['encryption_method']['encryptionMethod']) ? $config['encryption_method']['encryptionMethod'] : 'AES-128-CBC');
        $this->hashMethod   =   ( isset($config['auth']['password_hashing_method']) && !empty($config['auth']['password_hashing_method']) ? $config['auth']['password_hashing_method'] : 'bcrypt');
        $this->rehash       =   ( isset($config['auth']['rehash']) && !empty($config['auth']['rehash'])) ? $config['auth']['rehash'] : false;
        $this->T_USERS      =   ( isset($config['default_tables']['users']) && !empty($config['default_tables']['users'])) ? $config['default_tables']['users'] : 'users';
        $this->T_SESSION    =   ( isset($config['default_tables']['users_sessions']) && !empty($config['default_tables']['users_sessions'])) ? $config['default_tables']['users_sessions'] : 'users_sessions';
        $this->dbBuilder    =   new DbBuilder($config);
        $this->pdo          =   $this->dbBuilder->pdo;
        $this->oauth        =   new Oauth($config['oauth']);
    }

    /**
     * @param array $config
     */

    public static function init( array $config ){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if( isset($config['application']['production']) && $config['application']['production'] === true){
            Debugging::preventDebugging(true);
        }else{
            Debugging::preventDebugging(false);
        }
    }
}