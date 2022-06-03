<?php

namespace Ryzen\CoreLibrary\config;

use Dotenv;
use Exception;
use Ryzen\CoreLibrary\Functions;
use Ryzen\Oauth\Oauth;
use Ryzen\DbBuilder\DbBuilder;
use Ryzen\CoreLibrary\misc\dev\Debugging;

class BaseConfiguration
{
    /**
     * @return DbBuilder
     */
    protected static function initializeDB(): DbBuilder {
        return new DbBuilder([
            'mysql_database_config' => [
                'db_name'       => Functions::safeString($_ENV['database_name'])       ?? "",
                'db_prefix'     => Functions::safeString($_ENV['database_prefixes'])   ?? "",
                'db_host'       => Functions::safeString($_ENV['database_hostname'])   ?? "",
                'db_user'       => Functions::safeString($_ENV['database_username'])   ?? "",
                'db_pass'       => Functions::safeString($_ENV['database_password'])   ?? "",
                'db_driver'     => Functions::safeString($_ENV['database__drivers'])   ?? "",
                'db_charset'    => Functions::safeString($_ENV['database_charsets'])   ?? "",
                'db_collation'  => Functions::safeString($_ENV['database_collation'])  ?? "",
                'db_port'       => Functions::safeString($_ENV['database_curr_port'])  ?? "",
                'db_debug'      => Functions::safeString($_ENV['database_debugging'])  ?? "",
            ]
        ]);
    }

    /**
     * @return Oauth
     */
    protected static function initializeOauth(): Oauth {
        return new Oauth(
            [
                'app_id'        => Functions::safeString($_ENV['ryzen_app_id'])     ?? "",
                'app_url'       => Functions::safeString($_ENV['ryzen_app_url'])    ?? "",
                'app_secret'    => Functions::safeString($_ENV['ryzen_app_secret']) ?? ""
            ]
        );
    }

    /**
     * @return void
     */
    protected static function initializeRuntimeEnvironment() {
        if( isset($_ENV['app_mode']) && Functions::safeString($_ENV['app_mode']) === 'production'){
            Debugging::preventDebugging(true);
        }else{
            Debugging::preventDebugging(false);
        }
    }


    /**
     * @return void
     * @throws Exception
     */
    protected function loadENV() {
        try {
            $dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__,5));
            $dotenv->load();
            $_ENV   = array_change_key_case($_ENV, CASE_LOWER);
        } catch ( Exception $e )  {
            exit($e->getMessage());
        }
    }
}