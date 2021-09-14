<?php
// We’ll need a bootstrap.php file which loads our environment variables (later it will also do some additional bootstrapping for our project).

require 'vendor/autoload.php';
use Dotenv\Dotenv as Dotenv;
use Src\System\DataBaseConnector;

//load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//load dbconnction
$dbconnection = (new DataBaseConnector())->getConnection();

