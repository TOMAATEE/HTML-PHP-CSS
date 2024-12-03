<?php
ini_set("display_errors",1);
error_reporting(E_ALL);

$dbconfig = [
    "host"     => "localhost",
    "user"     => "root",
    "password" => "",
    "database" => "dbbenlogin2"];

//Verbindung über PDO
try {
    $pdo = new PDO("mysql:host=".$dbconfig["host"].";".
                   "dbname=".$dbconfig["database"].";",
                   $dbconfig["user"],
                   $dbconfig["password"]);
} catch (PDOException $e) {
    exit("Datenbankverbindung ist fehlgeschlagen");
}