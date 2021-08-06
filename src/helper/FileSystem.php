<?php


namespace Ryzen\CoreLibrary\helper;


class FileSystem
{
    /**
     * @param $path
     * @return bool
     */

    public static function checkCreateDir($path): bool
    {
        if(!file_exists($path)){
            @mkdir($path, 0777, true);
            return true;
        }
        return false;
    }

    /**
     * @param $file
     * @param string $content
     * @return bool
     */

    public static function checkCreateFile($file, string $content = ""): bool
    {
        if(!file_exists($file)){
            $fileNew = @fopen($file, "a+");
            @fwrite($fileNew, $content);
            @fclose($fileNew);
            return true;
        }
        return false;
    }

    /**
     * @param string $filePath
     * @return false|string
     */
    
    public static function getFileContent( string $filePath){
        return file_get_contents( $filePath );
    }
}