<?php

namespace Ryzen\CoreLibrary\helper;

use Ryzen\CoreLibrary\Auth;
use Ryzen\CoreLibrary\Cookie;

class Cache
{
    protected static function openCacheDirectory(){
        if(!file_exists('./cache')){
            $oldmask = umask(0);
            @mkdir('./cache', 0777, true);
            @umask($oldmask);
        }
        if(!file_exists('./cache/.htaccess')){
            FileSystem::checkCreateFile('./cache/.htaccess','deny from all');
        }

        if(!file_exists('cache/index.html')){
            FileSystem::checkCreateFile('./cache/index.html');
        }
    }

    /**
     * @param $viewPath
     * @param $viewContent
     */
    protected static function view($viewPath , $viewContent ){
        if(!Auth::check()){
            if (Cookie::has($viewPath)) {
                if (!file_exists('./cache/' . $viewPath . '-' . Cookie::get($viewPath) . '.res')) {
                    Cookie::forget($viewPath);
                }
            }
            if (!Cookie::has($viewPath)) {
                FileSystem::checkCreateDir('./cache/' . self::makeDirName($viewPath));
                $session = md5(uniqid());
                Cookie::put($viewPath, $session);
                FileSystem::checkCreateFile('./cache/' . $viewPath . '-' . $session . '.res');
                file_put_contents('./cache/' . $viewPath . '-' . $session . '.res', $viewContent);
            }
        }
        if(Auth::check()){
            if(Cookie::has($viewPath)){
                if (file_exists('./cache/' . $viewPath . '-' . Cookie::get($viewPath) . '.res')) {
                    unlink('./cache/' . $viewPath . '-' . Cookie::get($viewPath) . '.res');
                }
                Cookie::forget($viewPath);
            }
            FileSystem::checkCreateDir('./cache/' . self::makeDirName($viewPath));
            FileSystem::checkCreateFile('./cache/' . $viewPath . '-' . Auth::getUserId() . '.res');
            file_put_contents('./cache/' . $viewPath . '-' . Auth::getUserId() . '.res', $viewContent);
        }
    }

    /**
     * @param $viewPath
     * @param $namedPreFix
     * @return bool
     */
    protected static function markDownViewFile($viewPath, $namedPreFix ):bool {
        if(file_exists('./cache/' . $viewPath . '-' . $namedPreFix . '.res')){
            unlink('./cache/' . $viewPath . '-' . $namedPreFix . '.res');
        }
        return true;
    }

    /**
     * @param $viewPath
     * @return false|string
     */
    protected static function makeDirName( $viewPath ){
        $explodedDir = explode('/', $viewPath);
        if(count($explodedDir) > 1){
            $onlyDir =array_slice($explodedDir, 0, -1);
            return implode('/', $onlyDir);
        }
        return false;
    }
}