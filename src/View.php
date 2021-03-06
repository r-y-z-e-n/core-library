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
     * @param mixed $viewOptions
     * @return false|string
     */

    public static function load(string $viewPath, $viewOptions = false){
        if(!is_array($viewOptions) && !is_string($viewOptions) && $viewOptions === true){
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
        if(is_array($viewOptions)) foreach ($viewOptions as $key => $value) $$key = $value;

        ob_start();
        $page = Ry_Zen::$main->viewPath . $viewPath . '.' . Ry_Zen::$main->viewEngine;
        require "{$page}";
        self::$viewContent = ob_get_contents();
        ob_end_clean();
        if(self::$makeNewCache){ self::view($viewPath, self::$viewContent); }
        return self::$viewContent;
    }

    /**
     * @param string $viewPath
     */

    public static function cleanCache( string $viewPath = "" ){
        self::openCacheDirectory();
        if(Auth::check()) self::markDownViewFile($viewPath, Auth::getUserId());
        if(Cookie::has( $viewPath )){
            self::markDownViewFile( $viewPath,Cookie::get( $viewPath ));
            Cookie::forget( $viewPath );
        }
    }
}