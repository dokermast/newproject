<?php

class Db
{
    public static function getConnection()
    {
        $configPath = ROOT . '/config/db_config.php';
        $params = include($configPath);
        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']};port={$params['port']}";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        try{
            $db = new PDO($dsn, $params['user'], $params['password'], $options);

            return $db;

        } catch(PDOException $e) {

            die("Don`t connect to DB!");
        }
    }
}
