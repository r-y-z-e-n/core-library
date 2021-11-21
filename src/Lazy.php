<?php

namespace Ryzen\CoreLibrary;

use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Ryzen\CoreLibrary\helper\FileSystem;
use Ryzen\CoreLibrary\misc\db\Migration;
use Ryzen\CoreLibrary\helper\BaseFunctions;

class Lazy
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

        FileSystem::checkCreateDir(self::$rootFolder. "/" . date('Y-m-d'));
        FileSystem::checkCreateDir(self::$rootFolder. "/" . date('Y-m-d') . '/' . time());
        FileSystem::checkCreateDir(self::$rootFolder. "/" . date('Y-m-d') . '/' . time() .'/Files');
        FileSystem::checkCreateDir(self::$rootFolder. "/" . date('Y-m-d') . '/' . time() .'/SQL-BACKUP');

        /**
         * Check and create index.html file.
         */

        FileSystem::checkCreateFile(self::$rootFolder . "/" . "index.html");
        FileSystem::checkCreateFile(self::$rootFolder . "/" . date('Y-m-d') . "/index.html");
        FileSystem::checkCreateFile(self::$rootFolder . "/" . date('Y-m-d') . '/' . time() . "/index.html");
        FileSystem::checkCreateFile(self::$rootFolder . "/" . ".htaccess",'deny from all\nOptions -Indexes');
        FileSystem::checkCreateFile(self::$rootFolder . "/" . date('Y-m-d') . '/' . time() . '/Files' . "/index.html");
        FileSystem::checkCreateFile(self::$rootFolder . "/" . date('Y-m-d') . '/' . time() . '/SQL-BACKUP' . "/index.html");

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

    /**
     * @return bool
     */

    public static function backupEverything(): bool
    {
        if(self::dir_establish() && self::sqlBackUp(self::folderName()) && self::backupFiles(self::folderName())){
            return true;
        }
        return false;
    }

    /**
     * @param string $folder_name
     * @return bool
     */

    private static function backupFiles(string $folder_name): bool
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
     * @param $folder_name
     * @param $tables
     * @return bool
     */
    private static function sqlBackUp($folder_name, $tables = false): bool
    {
        $query_tables = Ry_Zen::$main->dbBuilder->pdo->prepare('SHOW TABLES');
        $query_tables->execute();

        while($row = $query_tables->fetch()){
            $target_tables[]    =   $row->Tables_in_ryzen;
        }

        if($tables !== false) {$target_tables = array_intersect($target_tables, $tables);}

        $content = "-- phpMyAdmin SQL Dump
-- http://www.phpmyadmin.net
--
-- Host Connection Info: " . 'host_info' . "
-- Generation Time: " . date('F d, Y \a\t H:i A ( e )') . "
-- Server version: " . 'server info' . "
-- PHP Version: " . PHP_VERSION . "
--\n
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET time_zone = \"+00:00\";\n
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;\n\n";
        $db = Ry_Zen::$main->dbBuilder;
        foreach ($target_tables as $table) {
            $result        = $db->query('SELECT * FROM ' . $table);
            $fields_amount = $result->queryCount();
            $rows_num      = $db->numRows();
            $res           = $db->query('SHOW CREATE TABLE ' . $table);
            $TableMLine    = (array) $res->fetch();
            $content       = (!isset($content) ? '' : $content) . "
-- ---------------------------------------------------------
--
-- Table structure for table : `{$table}`
--
-- ---------------------------------------------------------
\n" . $TableMLine['Create Table'] . ";\n";
            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
                while ($row = $result->fetch_row()) {
                    if ($st_counter % 100 == 0 || $st_counter == 0) {
                        $content .= "\n--
-- Dumping data for table `{$table}`
--\n\nINSERT INTO " . $table . " VALUES";
                    }
                    $content .= "\n(";
                    for ($j = 0; $j < $fields_amount; $j++) {
                        $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                        if (isset($row[$j])) {
                            $content .= '"' . $row[$j] . '"';
                        } else {
                            $content .= '""';
                        }
                        if ($j < ($fields_amount - 1)) {
                            $content .= ',';
                        }
                    }
                    $content .= ")";
                    if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                        $content .= ";\n";
                    } else {
                        $content .= ",";
                    }
                    $st_counter = $st_counter + 1;
                }
            }
            $content .= "";
        }
        $content .= "
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";

        $put_content    =   @file_put_contents($folder_name . '/SQL-BACKUP/SQL-BACKUP-' . date('Y-m-d') . '.sql', $content);
        if($put_content) return true;
        return false;
    }
}