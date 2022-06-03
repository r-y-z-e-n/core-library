<?php

namespace Ryzen\CoreLibrary\helper;

use Ryzen\CoreLibrary\Ry_Zen;

class BaseFunctions
{

    /**
     * @param $table
     * @return bool
     */
    public static function checkTable($table): bool {
        $statement = Ry_Zen::$main->dbBuilder->table($table)->check();
        if(strtolower($statement->Msg_type) == 'error') return false;
        return true;
    }
}