<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

class Functions{

    //same for all

    public static function myShuffle($str){

        if(empty($str)) return "";

        $n=strlen($str);
        //if($n==0) return "";
        $l=0;
        $selected_indices=array();
        $nb=0;//nb of elts in array
        $res="";
        while($l<$n){
            $randomIndex=rand(0,$n-1);
            if(!in_array($randomIndex,$selected_indices)){
                $res[$l++]=$str[$randomIndex];
                $selected_indices[$nb++]=$randomIndex;
            }
        }
    
        return $res;

    }

    public static function generateOtp(){

        $length=6;
        $characters="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $otp="";
        for($i=0;$i<$length;$i++){
            $randomIndex=rand(0,strlen($characters)-1);
            $otp[$i]=$characters[$randomIndex];
        }
        return $otp;
        }

        public static function sendMail($senderMail,$senderAppPassword,$destinationEmail, $subject, $body){

            $mail=new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host= "smtp.gmail.com";  //
            $mail->SMTPAuth=true;
            $mail->Username=$senderMail;
            $mail->Password=$senderAppPassword;// to be obtained from google account settings after enabling 2f auth
            $mail->SMTPSecure='ssl';
            $mail->Port=465;
            $mail->setFrom($senderMail);
            $mail->addAddress($destinationEmail);
            $mail->isHTML(true);
            $mail->Subject=$subject;
            $mail->Body=$body;
            return $mail->send();
            // if($mail->send()) echo "\nsuccess\n";
            // else echo "\nFailed\n";

        }

}


?>