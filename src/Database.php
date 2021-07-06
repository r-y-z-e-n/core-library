<?php

namespace Ryzen\CoreLibrary;

use PDOException;

/**
 * @author razoo.choudhary@gmail.com
 * Class Database
 * @package Ryzen\CoreLibrary
 */
class Database
{

    protected string $databaseHost;
    protected string $databaseName;
    protected string $databaseUser;
    protected string $databasePass;
    protected array $config = [];
    protected \PDO $pdo;

    public function __construct($config)
    {
        $this->databaseHost = $config['db_host'];
        $this->databaseName = $config['db_name'];
        $this->databaseUser = $config['db_user'];
        $this->databasePass = $config['db_pass'];
    }

    public function connect(): \PDO
    {
        try {

            $this->pdo = new \PDO("mysql:host=" . $this->databaseHost . ";dbname=" . $this->databaseName . "", $this->databaseUser, $this->databasePass);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->pdo;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }

    public function Ry_Insert_Data(array $dataArray, string $tableName): bool
    {
        try {
            $prepare = array();
            foreach ($dataArray as $key => $value) {
                $prepare[':' . $key] = $value;
            }
            $statement = $this->pdo->prepare("INSERT INTO $tableName ( " . implode(', ', array_keys($dataArray)) . ") VALUES (" . implode(', ', array_keys($prepare)) . ")");
            $statement->execute($prepare);
            return true;

        } catch (PDOException $e) {
            echo "ERROR. " . $e->getMessage();
        }
        return false;
    }

    public function Ry_Update_Data(array $dataArray, string $tableName, array $where = []): bool
    {

        try {

            $whereAttributes    = array_keys($where);
            $updateAttributes   = array_keys($dataArray);

            if (!empty($where)) {

                $whereClause = ' WHERE ' . implode(' AND ', array_map(fn($attr) => "$attr = :$attr", $whereAttributes));

            } else {

                $whereClause = '';
            }

            $setClause = implode(",", array_map(fn($attrSet) => "$attrSet = :$attrSet", $updateAttributes));

            $statement = $this->pdo->prepare("UPDATE $tableName SET $setClause $whereClause");

            foreach (array_merge($dataArray, $where) as $key => $item) {

                $statement->bindValue(":$key", $item);
            }

            $statement->execute();

            return true;

        } catch (PDOException $e) {

            echo "ERROR." . $e->getMessage();
        }

        return false;
    }

    public function Ry_Value_Exists(array $data,$table,$logicalOperator = "AND"): bool
    {

        $supported_Logical_Operator = ["AND","OR"];

        if(!in_array(strtoupper($logicalOperator),$supported_Logical_Operator)){
            return false;
        }

        $attributes = array_keys($data);
        $clause     = ' WHERE ' . implode(strtoupper(" ".$logicalOperator." "), array_map(fn($attr) => "$attr = :$attr", $attributes));

        $statement  = $this->pdo->prepare("SELECT * FROM $table $clause");
        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
        }
        if ($statement->execute() && $statement->rowCount() > 0) {

            return true;
        }
        return false;
    }

    public function Ry_Get_All(string $tableName, $where = [], $fetchType = '')
    {
        if ($where) {
            $attributes = array_keys($where);
            $sql        = implode("AND ", array_map(fn($attr) => "$attr= :$attr", $attributes));
            $statement  = $this->pdo->prepare("SELECT * FROM $tableName WHERE $sql");
            foreach ($where as $key => $value) {
                $statement->bindValue(":$key", $value);
            }
        } else {
            $statement = $this->pdo->prepare("SELECT * FROM $tableName");
        }
        if ($statement->execute() && $fetchType == 'single') {

            return $statement->fetch(\PDO::FETCH_ASSOC);

        } else {

            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function Ry_Get_One(string $tableName, $where = [])
    {
        return self::Ry_Get_All($tableName, $where, 'single');
    }
}