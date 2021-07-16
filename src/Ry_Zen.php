<?php

namespace Ryzen\CoreLibrary;

use Ryzen\DbBuilder\DbBuilder;

/**
 * @author razoo.choudhary@gmail.com
 * Class Ry_Zen
 * @package Ryzen\CoreLibrary
 */

class Ry_Zen
{

    public ?\PDO $pdo;
    public DbBuilder $dbBuilder;

    public static Ry_Zen $main;
    public string $Root_DIR;
    public string $T_SESSION;
    public string $T_USERS;
    public string $password;
    public string $encMethod;
    public string $theme_url = './themes/default/layouts/';

    /**
     * Ry_Zen constructor.
     * @param string $Root_DIRECTORY
     * @param $config
     */

    public function __construct(string $Root_DIRECTORY, $config)
    {
        self::$main         =   $this;
        $this->Root_DIR     =   $Root_DIRECTORY;
        $this->password     =   $config['encryption_method']['password'];
        $this->encMethod    =   $config['encryption_method']['encryptionMethod'];
        $this->T_USERS      =   $config['default_tables']['users'];
        $this->T_SESSION    =   $config['default_tables']['users_sessions'];
        $this->dbBuilder    =   new DbBuilder($config);
        $this->pdo          =   $this->dbBuilder->pdo;
    }

    /**
     * @param string $pageURL
     * @return false|string
     */

    public function Ry_Load_Page(string $pageURL = '')
    {
            global $ry;
            $page = $this->theme_url . $pageURL . '.phtml';
            $page_content = '';
            ob_start();
            require $page;
            $page_content = ob_get_contents();
            ob_end_clean();
            return $page_content;
    }
}