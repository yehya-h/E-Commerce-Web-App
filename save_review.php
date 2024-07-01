<?php
//first time client rate the product
    if(isset($_POST['postReview'])){
        $ratingValue = (int)$_POST['rating'];
        $clientId = $client->getClientId();
        $rating = new Rating(-1, $clientId, $productId, $ratingValue);

        $dbHelper->addRating($rating);

        if(!empty($_POST['review'])){
            $text = $_POST['review'];
            $review = new Review(-1, $clientId, $productId, $text);
            $dbHelper->addReview($review);
        }
        

        //reload with $_POST['reloadOnPostRating'] to use it to reload page and go back to review section
        echo "<form action='displayProduct.php?product_id=".$productId."' method='post' >";
        echo "<input id='reload' type='submit' name='reloadOnPostRating' hidden>";
        echo "</form>";

        echo "<script> 
            document.getElementById('reload').click(); </script>";
    }
