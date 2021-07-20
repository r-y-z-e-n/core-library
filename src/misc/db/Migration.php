<?php

namespace Ryzen\CoreLibrary\misc\db;

class Migration
{
    public static function users( $prefix = ""): string
    {
        $tableName = $prefix.'users';
        return "
               CREATE TABLE IF NOT EXISTS `{$tableName}` (
              `user_id` int(11) AUTO_INCREMENT PRIMARY KEY,
              `user_email` varchar(100) NOT NULL,
              `user_username` varchar(100) NOT NULL,
              `user_phone_number` varchar(20) NOT NULL,
              `user_password` text NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ";
    }

    public static function user_session( $prefix = "" ): string
    {
        $tableName = $prefix.'users_sessions';
        return "
        CREATE TABLE IF NOT EXISTS `{$tableName}` (
          `id` int(11) AUTO_INCREMENT PRIMARY KEY,
          `user_id` int(11) NOT NULL,
          `session_id` text NOT NULL,
          `platform` text NOT NULL,
          `platform_details` text NOT NULL,
          `time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
    }
}