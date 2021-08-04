<?php

namespace Ryzen\CoreLibrary;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Ryzen\CoreLibrary\helper\FileSystem;
use Ryzen\CoreLibrary\misc\db\Migration;
use Ryzen\CoreLibrary\helper\BaseFunctions;

class Lazy extends FileSystem
{

    /**
     * @var string
     */

    protected static string $rootFolder = 'Lazy Backup';

    /**
     * @return bool
     */

    protected static function dir_establish(): bool
    {
        /**
         * Check and Create Directory.
         */

        self::checkCreateDir(self::$rootFolder. "/" . date('Y-m-d'));
        self::checkCreateDir(self::$rootFolder. "/" . date('Y-m-d') . '/' . time());
        self::checkCreateDir(self::$rootFolder. "/" . date('Y-m-d') . '/' . time() .'/Files');

        /**
         * Check and create index.html file.
         */

        self::checkCreateFile(self::$rootFolder . "/" . "index.html");
        self::checkCreateFile(self::$rootFolder . "/" . date('Y-m-d') . "/index.html");
        self::checkCreateFile(self::$rootFolder . "/" . date('Y-m-d') . '/' . time() . "/index.html");
        self::checkCreateFile(self::$rootFolder . "/" . ".htaccess",'deny from all\nOptions -Indexes');
        self::checkCreateFile(self::$rootFolder . "/" . date('Y-m-d') . '/' . time() . '/Files' . "/index.html");

        return true;
    }

    /**
     * @return string
     */

    protected static function folderName(): string
    {
        return self::$rootFolder . "/" . date('Y-m-d') . '/' . time();
    }

    /**
     * @param string $folder_name
     * @return bool
     */

    public static function backupFiles(string $folder_name): bool
    {
        self::dir_establish();
        $rootPath = realpath('./');
        $zip      = new ZipArchive();
        $open     = $zip->open($folder_name . '/Files/Files-' . date('Y-m-d') . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($open !== true) {
            return false;
        }
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file) {
            if (!preg_match('/\b'.self::$rootFolder.'\b/', $file)) {
                if (!$file->isDir()) {
                    $filePath     = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
        $zip->close();
        return true;
    }

    /**
     * @return bool
     */

    public static function backMeUp(): bool
    {
        if(self::dir_establish() && self::backupFiles(self::folderName())){
            return true;
        }
        return false;
    }

    /**
     * @param string $tablePrefix
     * @return bool
     */

    public static function migrateDefaults(string $tablePrefix = ""): bool{
        if(!empty($tablePrefix)){
            $tablePrefix .= '_';
        }
        if(!BaseFunctions::checkTable($tablePrefix.'users')){
            if(Ry_Zen::$main->dbBuilder->pdo->exec(Migration::users($tablePrefix))){
                return true;
            }
        }
        if(!BaseFunctions::checkTable($tablePrefix.'users_sessions')){
            if(Ry_Zen::$main->dbBuilder->pdo->exec(Migration::user_session($tablePrefix))){
                return true;
            }
        }
        return false;
    }
}