<?php

include_once("DBHelper.php");
include_once("classes/config.php");

try{
    $dbHelper=new DBHeLper(DB_SERVER,DB_USERNAME,DB_PASSWORD);
    //$dbHelper=new DBHELper("localhost",DB_USERNAME,DB_PASSWORD);
}catch(PDOEXCEPTION $e){
    die("Connection Failed: ".  $e->getMessage());
}

?>