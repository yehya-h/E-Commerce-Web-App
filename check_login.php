<?php
include_once("connection.php");
//session_start();
//session_start();

$isClient=false;
$isAdmin=false;
$isOwner=false;

if(isset($_COOKIE['token'])){

    //it might be client, admin, or owner
    if(!empty($_COOKIE['token'])){

       // $_SESSION['signed_in']=true;
        //owner handling
        // if(isset($_COOKIE['isOwner'])){
        //     if($_COOKIE['isOwner']=="true"){

        //         //this is the owner, directly forward him/her to owner page
        //         $isOwner=true;
        //        // header("Location:owner.php");

        //     }
        // }

        //else
         if(isset($_COOKIE['isAdmin'])){
            
            if($_COOKIE['isAdmin']=="true"){

                if(($dbHelper->getAdminByToken($_COOKIE['token']))!=null){
                    //account exists => log in
                    $_SESSION['signed_in']=true;
                    $isAdmin = true;

                }

                else{
                    //account not found
                    $_SESSION['signed_in']=false;
                    $isAdmin=false;
                    //delete the cookie to avoid problems since account is missing
                    setcookie("token","",time()-3600);
                    setcookie("isAdmin","",time()-3600);

                }

                //this is an admin, forward him/her to the admin page:
                    //check if the account is still available in the db 
                   // $isAdmin=true;
                    //header("Location:admin.php");
            }
        }

        else if(isset($_COOKIE['isClient'])){

            if($_COOKIE['isClient']=="true"){
                
                //check if the account is still available in the db 

                if(($dbHelper->getClientByToken($_COOKIE['token']))!=null){

                    $_SESSION['signed_in']=true;
                    $isClient=true;

                }

                else{

                    $_SESSION['signed_in']=false;
                    $isClient=false;
                    setcookie("token","",time()-3600);
                    setcookie("isClient","",time()-3600);

                }
                //$isClient=true;
            }
        }


    }
    else{
        $_SESSION['signed_in']=false;
        
    }


}
else if(isset($_COOKIE['isOwner'])){
    
        

          if($_COOKIE['isOwner']=="true"){
            $_SESSION['signed_in']=true;
                //this is the owner, directly forward him/her to owner page
                $isOwner=true;
               // header("Location:owner.php");

            }


}

else $_SESSION['signed_in']=false;


?>