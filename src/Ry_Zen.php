<?php

namespace Ryzen\CoreLibrary;

use PDO;
use Ryzen\DbBuilder\DbBuilder;

/**
 * @author razoo.choudhary@gmail.com
 * Class Ry_Zen
 * @package Ryzen\CoreLibrary
 */

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
        $this->T_USERS      =   (!empty($config['default_tables']['users'])) ? $config['default_tables']['users'] : 'users';
        $this->T_SESSION    =   (!empty($config['default_tables']['users_sessions'])) ? $config['default_tables']['users_sessions'] : 'users_sessions';
        $this->dbBuilder    =   new DbBuilder($config);
        $this->pdo          =   $this->dbBuilder->pdo;
    }
}