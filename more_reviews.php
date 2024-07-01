<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reviews</title>
    <style>
        body{
            padding: 10px;
        }

        p{
            font-size: 20px;
        }
    </style>
</head>
<body>

<nav>

</nav>

<?php
$moreReviews = array_slice($_SESSION['reviews'], 15);

foreach($moreReviews as $review){
    //display the name of client who added this review
    echo "<p><b>".$review['firstName']." ".$review['lastName']."</b><br>";
    //display the rating
    for($i=1; $i <= $review['value']; $i++){
        echo "<span style='color:gold;' >&#9733</span>";
    }
    echo "<br><br>";
    //display the text of the review
    echo "&nbsp; &nbsp;".$review['text']."</p><hr>";
}
?>

<footer>

</footer>

</body>
</html>