<?php
namespace Src\System;

require_once(__DIR__.'/../../vendor/autoload.php') ;
use \PDO as Pdo;

class DataBaseConnector{
    private $dbconnction = null;
    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db_name = $_ENV['DB_DATABASE'];
        $user = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try{
            $dsn =  "mysql:host=$host;port=$port;dbname=$db_name";
            $this->dbconnction = new Pdo(
                $dsn,
                $user,
                $password
            );
            $this->dbconnction->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (\PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getConnection(){
        return $this->dbconnction;
    }

}