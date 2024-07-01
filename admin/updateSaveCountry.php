<?php
include_once ($_SERVER['DOCUMENT_ROOT']."/connection.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>

<body>
    <?php
    $error1=$error2=1;
    if (isset ($_POST['submit'])) {
        if (isset ($_POST['country_name']) && !empty ($_POST['country_name']) && isset ($_POST['time'])
         && !empty ($_POST['time']) && isset ($_POST['delivery_fees']) && !empty ($_POST['delivery_fees'])) {
            $time = htmlspecialchars($_POST['time']) ;
            $delivery_fees = htmlspecialchars($_POST['delivery_fees']);
   
            // if ($time < 0) {
            //     $message = "<p style='color:red ; font-weight:bold'>Delivery time can't be negative!</p>";
            //     $error1 = 0;
            // } 
            // if($delivery_fees <0 || $delivery_fees >100){
            //     $message = "<p style='color:red ; font-weight:bold'>Delivery Fees % must be between 0 and 100!</p>";
            //     $error2 = 0;
            // }
           
           //modified
           if(!is_numeric($time)){
            $message = "<p style='color:red ; font-weight:bold'>Delivery time must be a number!</p>";
            $error1 = 0;
        }
        else if ($time < 0) {
            $message = "<p style='color:red ; font-weight:bold'>Delivery time can't be negative!</p>";
            $error1 = 0;
        } 
        
        if(!is_numeric($delivery_fees)){
            $message = "<p style='color:red ; font-weight:bold'>Delivery Fees % must be a number!</p>";
            $error2 = 0;
        }
        else if($delivery_fees <0){
            $message = "<p style='color:red ; font-weight:bold'>Delivery Fees can't be negative!</p>";
            $error2 = 0;
        }
           
           
           
            if($error1==1 && $error2==1) {
                $country = $dbHelper->getCountryByName(htmlspecialchars($_POST['country_name']));
                $country->setDeliveryTime($time);
                $country->setDeliveryFees($delivery_fees);
                $dbHelper->updateCountry($country);
                $message = "<p style='color:green ; font-weight:bold'>Country updated successfully</p>";
            }  
        }
    }
    ?>
</body>

</html>