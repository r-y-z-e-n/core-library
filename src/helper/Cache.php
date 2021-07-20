<?php

namespace Ryzen\CoreLibrary\helper;

use Ryzen\CoreLibrary\helper\FileSystem;
use Ryzen\CoreLibrary\Auth;
use Ryzen\CoreLibrary\Cookie;

class Cache
{
    public static function openCacheDirectory(){
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

    protected static function view($viewPath , $viewContent )
    {
        if(Auth::check() == false){
            if (Cookie::has($viewPath) == true) {
                if (!file_exists('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . Cookie::get($viewPath) . '.res')) {
                    Cookie::forget($viewPath);
                }
            }
            if (Cookie::has($viewPath) == false) {
                FileSystem::checkCreateDir('./cache/' . explode('/', $viewPath)[0]);
                $session = md5(uniqid());
                Cookie::put($viewPath, $session);
                FileSystem::checkCreateFile('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . $session . '.res');
                file_put_contents('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . $session . '.res', $viewContent);
            }
        }
        if(Auth::check() == true){
            if(Cookie::has($viewPath) == true){
                if (file_exists('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . Cookie::get($viewPath) . '.res')) {
                    unlink('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . Cookie::get($viewPath) . '.res');
                }
                Cookie::forget($viewPath);
            }
            FileSystem::checkCreateDir('./cache/' . explode('/', $viewPath)[0]);
            FileSystem::checkCreateFile('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . Auth::getUserId() . '.res');
            file_put_contents('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . Auth::getUserId() . '.res', $viewContent);
        }
    }

    /**
     * @param $viewPath
     * @param $namedPreFix
     * @return bool
     */

    protected static function markDownViewFile($viewPath, $namedPreFix ):bool {
        if(file_exists('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . $namedPreFix . '.res')){
            unlink('./cache/' . explode('/', $viewPath)[0] . '/' . explode('/', $viewPath)[1] . '-' . $namedPreFix . '.res');
        }
        return true;
    }
}