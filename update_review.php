<?php
//rated before by this client
    if(isset($_POST['updateReview'])){
        $ratingValue = (int)$_POST['rating'];
        $clientId = $client->getClientId();
        $rating = $dbHelper->getRatingByClPrId($clientId, $productId);
        $rating->setValue($ratingValue);

        $dbHelper->updateRating($rating);

            $text = $_POST['review'];
            $review = $dbHelper->getReviewByClPrId($clientId, $productId);
            //if reviewed before and client wants to update
            if($review && !empty($text)){
                $review->setText($text);
                $dbHelper->updateReview($review);
            }
            //if reviewed before and client wants to empty the review
            else if($review && empty($text)){
                $dbHelper->deleteReview($review);
            }
            //if not reviewed before and clients wants to add a review
            else if(! $review && !empty($text)){
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