<?php
//echo "sign out";
include_once("include_all.php");
include_once("connection.php");
$flag=false;
$deleted=false;
if(isset($_GET['isClient'])){
//echo 'Entered conde';
    if($_GET['isClient']==1 || $_GET['isClient']=="1"){

        setcookie("token","",time()-3600);
        setcookie("isClient","",time()-3600);
        $flag=true;
    }

   // header("Location:index.php");
}

else if(isset($_GET['isOwner'])){

    if($_GET['isOwner']==1 || $_GET['isOwner']=="1"){

        setcookie("isOwner","",time()-3600);
        $flag=true;
    }
}

else if(isset($_GET['isAdmin'])){

    if($_GET['isAdmin']==1 || $_GET['isAdmin']=="1"){
        setcookie("token","",time()-3600);
        setcookie("isAdmin","",time()-3600);
        $flag=true;
    }
}

if(isset($_GET['deleteAccount'])){
    echo "ENTERED DELETE";
    if($dbHelper->deleteAccount($_GET['acc_id'])==false){
       
       echo "<br>DELETE FAILED<br>";
        // echo '<script>alert('.'An error has occured in account deletion'.');window.location.href="index.php";</script>';
    }
    else{
    //deleted
        echo "<br>DELETE SUCCESS";
        $deleted=true;
        header("Location:popup.php?page=deleteAccount");

    }
   // else $flag
}
//echo "WE ARE HERE";
if($flag==true && $deleted==false) header("Location:index.php");

//else  echo '<script>alert('.'An error has occured'.');window.location.href="index.php";</script>';




?>