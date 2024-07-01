<?php

include_once("../DBHelper.php");
include_once("../classes/config.php");//added

try{
    $androidDBHelper=new DBHeLper(DB_SERVER,DB_USERNAME_ANDROID,DB_PASSWORD_ANDROID);
}catch(PDOEXCEPTION $e){
    die("Connection Failed: ".  $e->getMessage());
}


?>