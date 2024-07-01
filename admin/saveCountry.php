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
        if (isset ($_POST['name']) && !empty ($_POST['name']) && isset ($_POST['time']) &&
         !empty ($_POST['time']) && isset($_POST['delivery_fees']) && !empty($_POST['delivery_fees'])) {
            if ($dbHelper->country_unique($_POST['name']) == 0) {
                $name = htmlspecialchars($_POST['name']) ;
                $time = htmlspecialchars($_POST['time']) ;
                $delivery_fees = htmlspecialchars($_POST['delivery_fees']);
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
                //-------
            } else {
                $message = "<p style='color:red ; font-weight:bold'>Country name already token!</p>";
                $error1 = 0;
            } 
            if($error1==1 && $error2==1) {
                    $country = new Country($name,$time,$delivery_fees);
                    $dbHelper->addCountry($country);
                    $message = "<p style='color:green ; font-weight:bold'>Country added</p>";
            }
        }
    }
    ?>
</body>

</html>