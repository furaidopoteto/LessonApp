<?php
try{
    $db = new PDO("mysql:dbname=on-demanddb;host=db;charset=utf8", "root", "password");
}catch(PDOException $e){
    $e->getMessage();
}