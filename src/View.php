<?php

namespace Ryzen\CoreLibrary;

use Ryzen\CoreLibrary\helper\Cache;

class View extends Cache
{

    /**
     * @var string
     */

    protected static string $viewContent = '';
    protected static bool $makeNewCache = false;

    /**
     * @param string $viewPath
     * @param bool $preferCaching
     * @return false|string
     */

    public static function load(string $viewPath, bool $preferCaching = false){
        if($preferCaching == true){
            self::openCacheDirectory();
            if(file_exists('./cache/'. $viewPath . '-' . Cookie::get($viewPath) . '.res')){
                self::$viewContent = file_get_contents('./cache/'. $viewPath . '-' . Cookie::get($viewPath) . '.res');
                if(!empty(self::$viewContent)){
                    return self::$viewContent;
                }
            }else{
                self::$makeNewCache = true;
            }
        }
        ob_start();
        $page = Ry_Zen::$main->theme_url . $viewPath . '.' . Ry_Zen::$main->viewExtension;
        require "{$page}";
        self::$viewContent = ob_get_contents();
        ob_end_clean();
        if(self::$makeNewCache == true){ self::view($viewPath, self::$viewContent); }
        return self::$viewContent;
    }

    /**
     * @param $viewPath
     */

    public static function cleanCache( $viewPath ){
        self::openCacheDirectory();
        if(Auth::check()){
            self::markDownViewFile($viewPath, Auth::getUserId());
        }
        if(Cookie::has( $viewPath )){
            self::markDownViewFile( $viewPath,Cookie::get( $viewPath ));
            Cookie::forget( $viewPath );
        }
    }
}