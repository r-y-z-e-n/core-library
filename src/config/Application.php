<?php

namespace Ryzen\CoreLibrary\config;

use Ryzen\CoreLibrary\event\Generate;
use Ryzen\CoreLibrary\helper\FileSystem;

class Application
{
    private string $keyPath = '../src/misc/keys';

    /**
     * @return false|string
     */

    public function AppKey(){
        $this->checkAndCreate();
        return $this->getApplicationKey();
    }

    /**
     * PreFinned
     */

    private function checkAndCreate(){
        FileSystem::checkCreateDir($this->keyPath);
        FileSystem::checkCreateFile($this->keyPath . '/' . '.htaccess', 'deny from all');
        FileSystem::checkCreateFile($this->keyPath . '/' . '.gitignore','app_key.php');
    }

    /**
     * @return false|string
     */

    private function getApplicationKey(){
        if(file_exists($this->keyPath . '/app_key.php')){
            $application_key = FileSystem::getFileContent($this->keyPath . '/app_key.php');
            return (!empty($application_key)) ? $application_key : $this->generateNewKeyFile();
        }
        return $this->generateNewKeyFile();
    }

    /**
     * @return false|string
     */

    private function generateNewKeyFile(){
       if(file_exists($this->keyPath . '/app_key.php') && empty(FileSystem::getFileContent($this->keyPath . '/app_key.php'))){
           $this->generateNewAppKey();
       }
        if(FileSystem::checkCreateFile($this->keyPath . '/app_key.php',Generate::key())){
            return FileSystem::getFileContent($this->keyPath . '/app_key.php');
        }
        return false;
    }

    /**
     * Deletes Key File
     */

    private function generateNewAppKey(){
        unlink($this->keyPath . '/app_key.php');
    }
}