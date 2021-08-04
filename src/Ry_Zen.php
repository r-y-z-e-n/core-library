<?php

namespace Ryzen\CoreLibrary;

use PDO;
use Ryzen\DbBuilder\DbBuilder;

class Ry_Zen
{

    /**
     * @var PDO|null
     */

    public ?PDO $pdo;
    public DbBuilder $dbBuilder;
    public static Ry_Zen $main;

    /**
     * @var string|mixed
     */

    public string $T_USERS;
    public string $password;
    public string $Root_DIR;
    public string $T_SESSION;
    public string $encMethod;
    public string $hashMethod;
    public bool $rehash;

    /**
     * @var string
     */

    public string $content       = '';
    public string $theme_url     = './resources/view/';
    public string $viewExtension = 'php';

    /**
     * Ry_Zen constructor.
     * @param string $Root_DIRECTORY
     * @param $config
     */

    public function __construct(string $Root_DIRECTORY, $config)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        self::$main         =   $this;
        $this->Root_DIR     =   $Root_DIRECTORY;
        $this->password     =   $config['encryption_method']['password'];
        $this->encMethod    =   $config['encryption_method']['encryptionMethod'];
        $this->hashMethod   =   $config['auth']['password_hashing_method'];
        $this->rehash       =   (!empty($config['auth']['rehash'])) ? $config['auth']['rehash'] : false;
        $this->T_USERS      =   (!empty($config['default_tables']['users'])) ? $config['default_tables']['users'] : 'users';
        $this->T_SESSION    =   (!empty($config['default_tables']['users_sessions'])) ? $config['default_tables']['users_sessions'] : 'users_sessions';
        $this->dbBuilder    =   new DbBuilder($config);
        $this->pdo          =   $this->dbBuilder->pdo;
    }
}