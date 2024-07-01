<?php
session_start();
include_once ("connection.php");
include_once ("include_all.php");
include_once ("check_login.php");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <title>Product</title>
    <!-- <link rel="stylesheet" href="style/header.css">
    <link rel="stylesheet" href="style/displayProduct.css" /> -->
    <link rel="stylesheet" href="style/header.css" />
    <link rel="stylesheet" href="style/displayProduct.css" />
    <link rel="stylesheet" href="style/footer.css" />
    <link rel="icon" type="image/x-icon" href="../logos/primary_icon.jpeg" />

</head>


<body>
    <header>
        <nav class="nav-container">
            <div class='left-nav'>
                <div class="pc-logo">
                    <a href="../index.php"><img src="../logos/primary_logo.png" alt="logo" width='220rem'
                            height='90rem'></a>
                </div>
                <div class="phone-logo">
                    <a href="../index.php"><img src="logos/primary_logo_phone_2.png" alt="logo" width='70em'
                            height='60em'></a>
                </div>
            </div>
            <div class="center-nav">
                <div class="search-bar">
                    <form id="filter" action="index.php" method="POST">
                        <!-- <label for="categoryFilter">Select Categories</label> -->
                        <div class="filter-wrapper">
                            <select id="categoryFilter" name="categoryFilter" class="filter-select">
                                <?php
                                $categories = $dbHelper->getAllCategories("name");
                                foreach ($categories as $category) {
                                    echo "<option value='" . $category->getCategoryId() . "'>" . $category->getName() . "</option>";
                                }
                                ?>
                            </select>
                            <input type="submit" name="filterByCategory" value="ok" class='filter-button' />
                        </div>
                    </form>
                    <form action="index.php" method="post">
                        <input type="text" placeholder="What are you looking for?" name="searchField" required
                            class='search-input' />
                        <button type="submit" name="search" class='search-button'><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
            <?php
            if ($_SESSION['signed_in'] == true && $isClient == true) {
                $client = $dbHelper->getClientByToken($_COOKIE['token']);
                if ($client == null) {
                    //account not founed depending on token => somebody signed in from another device
                    //in this case, sign out from this device to avoid errors
                    header("Location:sign_out.php?isClient=1");
                }
                $cart_id = $client->getCartId();
                $cartItems = 0;
                if ($dbHelper->getNbCartItems($cart_id) != null) {
                    $cartItems = $dbHelper->getNbCartItems($cart_id);
                }
                echo '<div class="right-nav"><h3>&nbsp<div class="account-select" onclick="showOptions()">
                    ' . $client->getFirstName() . '
                    <i class="bi bi-person-fill-gear"></i>
                    <select id="accountSelect" onchange="goToPage(this.value)">
                            <option disabled selected>--choose--</option>
                            <option value="manageAccount.php?forward=viewOrders">View Orders</option>
                            <option value="manageAccount.php?forward=updateProfile">Update Profile</option>
                            <option value="manageAccount.php?forward=changePassword">Change Password</option>
                            <option value="delete">Delete Account</option>
                        </select>
                        </div>
                    ';
                echo '&nbsp|&nbspPoints:' . $client->getPoints();
                echo '&nbsp<h2 style="background-color:transparent;"><div class="cart-icon"><a href="cart.php"><i class="bi bi-bag-fill"></i> </a>
                                   <span id="nbItems">' . $cartItems . '</span></div></h2>
                                ';
                // echo '&nbsp<a href="#about">About</a>';
                echo '&nbsp&nbsp<h2 style="background-color:transparent;">
                <a href="sign_out.php?isClient=1"><i class="bi bi-box-arrow-right"></i> </a></h2></h3></div>';
            } else {
                echo '<div class="right-nav">
                    <h3><a href="sign_in.php">Sign In&nbsp<i class="bi bi-person-fill"></i></a></h3>&nbsp &nbsp
                    <div class="cart-icon"><a href="cart.php"><i class="bi bi-bag-fill"></i> </a>
                                   <span id="nbItems">0</span></div></div>';
                // &nbsp &nbsp<a href="createAccount.php">Create account&nbsp<i class="bi bi-person-fill-add"></i></a>;
            }
            ?>
            <!-- <li><a href="#about">About</a></li> -->
            <!-- <li><a href="cart.php">Cart</a></li> -->
            </ul>
        </nav>
    </header>

    <div class="middle-section">
        <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="deleteAccount()">Yes</button> <button class='add-button'
                    onclick="closePopUp()">No</button>
            </div>
        </div>

        <?php
        /////////////////////////////////////////////////////////
        if (isset($_GET['product_id'])) {
            if (!$product = $dbHelper->getProductById($_GET['product_id']))
                exit();
            else {   //correct product id
                $product->loadImages2();
                $prodImages = $product->getImages();
                // print_r($prodImages);
                echo "<div class='product-container'>";

                //main image:
                echo "<div class='img-container'><img id='mainImage' src='" . $prodImages[0] . "'>";
                //display other images:
                echo "<div class='scroll-container'>";
                foreach ($prodImages as $key => $value) {

                    echo "<img src='" . $value . "' 
                    onclick='changeImage(" . json_encode($value) . ")' >";

                }
                echo "</div></div>";
                echo "<div class='productInfo-container'>
            <h2>" . $product->getName() . "</h2>
            <p>" . $product->getDescription() . "</p>";


                $productId = $product->getProductId();

                //Rating handling:
                $rating = $product->getRating();
                if (isset($rating)) {
                    echo "<p><span class='stars bi bi-star-fill'></span><span class='rating'> " . number_format($rating, 0) . " / 5</span>";
                    echo " (" . $dbHelper->getRatingsByProductId($productId) . " ratings)</p>";
                } else {
                    echo "<p style=' color:var(--red)'>No ratings yet.</p>";
                }
                echo "<div class='price-container'>";
                //Price handling:
                $price = $product->getPrice();
                $discount = $product->getDiscount();
                if (isset($discount)) {
                    echo "<p>Was: <del>$" . $price . "</del>  <span class='discount'>" . $discount . "% OFF</span></p>";
                    echo "<p>Now: <span class='after'>$" . $product->getNewPrice() . "</span></p>";
                } else {
                    echo "<p>Now: <span class='after'>$" . $price . "</span></p>";
                }
                echo "</div><br>";

                //stock handling and form:
                $stock = $product->getStock();
                if ($stock > 0) {
                    //Add to cart form:
                    echo "<form action='displayProduct.php?product_id=" . $productId . "' method='post'>";
                    echo "<select name='quantity' class='custom-select'>";
                    for ($i = 1; $i <= $stock; $i++) {
                        echo "<option>" . $i . "</option>";
                    }
                    echo "</select><br><br> ";
                    echo "<input type='submit' name='addToCart' value='Add to Cart' class='addToCart_btn' >";
                    echo "</form>";
                }
                //out of stock
                else {
                    echo "<button class='outOfStock_btn' >OUT OF STOCK</button>";
                }


                //Add to cart(on submit) handling:
                if (isset($_POST['addToCart'])) {
                    //signed in AND Client:
                    if ($_SESSION['signed_in'] && $isClient) {
                        $selectedQuantity = (int) $_POST['quantity'];
                        $client = $dbHelper->getClientByToken($_COOKIE['token']);
                        if ($client == null) {
                            header("Location:sign_out.php?isClient=1");
                        }
                        $cartId = $client->getCartId();
                        $cartItem = $dbHelper->getCartItemByCaPrId($cartId, $productId);
                        //first time added to cart:
                        if (!$cartItem) {
                            $cartItem = new CartItem(-1, $cartId, $productId, $selectedQuantity);
                            $dbHelper->addCartItem($cartItem);
                        }
                        //added to cart before:
                        else {
                            $newQuantity = $selectedQuantity + $cartItem->getQuantity();
                            if ($newQuantity <= $stock) {
                                $cartItem->setQuantity($newQuantity);
                                $dbHelper->updateCartItemQuantity($cartItem);
                            } else {
                                $cartItem->setQuantity($stock);
                                $dbHelper->updateCartItemQuantity($cartItem);
                            }
                        }

                        //echo "<p style='color:green;' > Added to Cart</p>";
                        echo "<script>
                        window.location.href='popup.php?page=displayProduct'; </script>";
                    }

                    //not signed in OR not Client --> direct user to sign in page
                    else {
                        echo "<script>
                    window.location.href='sign_in.php?product_id=" . $_GET['product_id'] . "'; </script>";
                    }
                }

                echo "</div>
        </div>"; ///product div
        
                //about this product:
                // echo "<h2><ins>About this Product:</ins></h2>";
                // echo "<p><b>" . $product->getName() . "</b></h2>
                // <p>-Description:</p>
                // <p>" . $product->getDescription() . "</p><br><br>";
        
                //ratings & reviews handling:
                ///
                echo "<div class='rating-review'>
            <h2 id='reviewSection' >Ratings & Reviews:</h2>";

                //signed in AND Client:
                if ($_SESSION['signed_in'] && $isClient) {
                    $client = $dbHelper->getClientByToken($_COOKIE['token']);
                    if ($client == null) {
                        header("Location:sign_out.php?isClient=1");
                    }
                    $clientId = $client->getClientId();
                    $isOrdered = $dbHelper->isOrderedProduct($productId, $clientId);

                    //post a review(on submit) handling:
                    include_once ("save_review.php"); //before $dbHelper->isRatedProduct to insert into the tables in the DB
                    //so that the function would return correct answer after submit
        
                    //update a review(on submit) handling:
                    include_once ("update_review.php");

                    //get back to review section after posting a rating:
                    if (isset($_POST['reloadOnPostRating'])) {
                        echo "<script>
                    window.location.href='#reviewSection'; 
                    window.alert('Thank you for your feedback.'); 
                    </script>";
                    }

                    $isRated = $dbHelper->isRatedProduct($productId, $clientId);

                    //if product is ordered and not rated before by this client
                    if ($isOrdered && !$isRated) {
                        //display the review form
        
                        echo "<div class='rating-review-container'>";
                        echo "<form class='rating-form' action='displayProduct.php?product_id=" . $productId . "' method='post' >";
                        //display input for ratings:
                        echo "<div class='rating-container' id='rating'>
                            <p>Rate this product:</p>
                            <input type='hidden' name='rating' id='ratingValue' value='1'>";
                        for ($i = 1; $i <= 5; $i++) {
                            echo "<i class='star bi bi-star' data-value='" . $i . "'> </i>";
                        }
                        echo "</div>";

                        //display input for review:
                        echo "<div class='review-container'>";
                        echo "<textarea name='review' placeholder='Describe your Experience (optional)'
                    maxlength='65535' rows='4' ></textarea>";

                        echo "<div><input type='submit' name='postReview' value='Post' class='post_btn' ></div>";
                        echo "</form>";
                        echo "</div>";
                        echo "</div>";

                    }


                    //if product is ordered but rated before by this client, display form to update the review
                    else if ($isOrdered && $isRated) {
                        //display the review form
                        echo "<div class='rating-review-container' >";

                        echo "<form class='rating-form' action='displayProduct.php?product_id=" . $productId . "' method='post' >";
                        //display input for ratings:
                        $rat = $dbHelper->getRatingByClPrId($clientId, $productId);
                        $ratValue = $rat->getValue();
                        echo "<div class='rating-container' id='rating'>
                            <p>Update your Rating:</p>
                            <input type='hidden' name='rating' id='ratingValue' value='" . $ratValue . "'>";
                        for ($i = 1; $i <= 5; $i++) {
                            echo "<i class='star bi bi-star' data-value='" . $i . "'> </i>";
                        }
                        echo "</div>";

                        //display input for review:
                        $rev = $dbHelper->getReviewByClPrId($clientId, $productId);
                        //if client entered a rating but did not enter a review before
                        if (!$rev) {
                            $revText = "";
                        } else {
                            $revText = $rev->getText();
                        }
                        echo "<div class='review-container'>";
                        echo "<textarea name='review' placeholder='Describe your Experience (optional)'
                    maxlength='65535' rows='4' >" . $revText . "</textarea>";

                        echo "<div><input type='submit' name='updateReview' value='Update' class='post_btn' ></div>";
                        echo "</div>";
                        echo "</form>";

                        echo "</div>";

                    }


                    //if product is not ordered by this client
                    else if (!$isOrdered) {
                        echo "<p style='text-align:center; margin:2rem 0 2rem 0;' >How to review this item?<br>
                    To add a review, you should purchase this item before.</p>";

                    }
                }

                //not signed in OR not Client
                else {
                    echo "<a href='sign_in.php?product_id=" . $_GET['product_id'] . "' >
                <button class='signIn_btn' >Sign in to Add a review</button></a>";

                }


                echo "<hr>";

                //display all ratings with reviews:
        
                //display select (top-lowest) form:
                echo "<form action='displayProduct.php?product_id=" . $productId . "' method='post' >";

                echo "<select name='selectRating' id='selectRating' >";
                echo "<option>Top Ratings</option>";
                echo "<option>Lowest Ratings</option>";

                echo "  <input class='view' type='submit' name='viewRating' value='View' >";
                echo "</form>";

                //by default, display by Top Ratings:
                $reviews = $dbHelper->getRatingsReviewsByProductId($productId, 'DESC');

                //if user changes the selection:
                if (isset($_POST['viewRating'])) {
                    $selectedRating = $_POST['selectRating'];

                    //keep the selection
                        echo "<script>
                        var selectRating = document.getElementById('selectRating');
                        selectRating.value='".$selectedRating."';
                        </script>";

                    if ($selectedRating == "Top Ratings") {
                        $reviews = $dbHelper->getRatingsReviewsByProductId($productId, 'DESC');
                    } else if ($selectedRating == "Lowest Ratings") {
                        $reviews = $dbHelper->getRatingsReviewsByProductId($productId, 'ASC');
                    }

                    //go back to review section to view:
                    echo "<script>
            window.location.href='#reviewSection'; </script>";
                }



                //if there exists reviews (and ratings) on this item
                if ($reviews) {

                    $nonEmptyReviews = array();
                    //extract the non empty reviews (client did a rating with a review) to display
                    foreach ($reviews as $review) {
                        if (!empty($review['text'])) {
                            $nonEmptyReviews[] = $review;
                        }
                    }

                    if (!empty($nonEmptyReviews)) {
                        $_SESSION['reviews'] = $nonEmptyReviews;
                        $reviewsNb = count($nonEmptyReviews);
                        echo "<h2>" . $reviewsNb . " Reviews</h2>";

                        $firstReviews = array_slice($nonEmptyReviews, 0, 15);
                        //display reviews:
                        foreach ($firstReviews as $review) {
                            //display the name of client who added this review
                            echo "<p><b>" . $review['firstName'] . " " . $review['lastName'] . "</b>   ";
                            //display the rating
                            for ($i = 1; $i <= $review['value']; $i++) {
                                echo "<span class='bi bi-star-fill' > </span>";
                            }
                            echo "<br><br>";
                            //display the text of the review
                            echo "&nbsp; &nbsp;" . $review['text'] . "</p><hr>";
                        }

                        //if there are more reviews:
                        if ($reviewsNb > 15) {
                            echo "<a href='more_reviews.php'><p style='text-align:right;' >see more Reviews> </p></a>";
                        }
                    }

                    //there is no reviews (there is ratings but no reviews(comments))
                    else {
                        echo "<p><b>No Reviews yet.</b></p>";
                    }
                } else {
                    echo "<p><b>No Reviews yet.</b></p>";
                }

                echo "</div>";
            }
        }
        ?>

        <script>
            function changeImage(path) {
                document.getElementById("mainImage").src = path;
                console.log(path);
            }
        </script>

        <script>
            document.querySelectorAll('.star').forEach(function (star) {
                star.addEventListener('mouseenter', function () {
                    var value = this.getAttribute('data-value');
                    //document.getElementById('ratingValue').value = value;
                    highlightStars(value);
                });

                star.addEventListener('mouseleave', function () {
                    var value = document.getElementById('ratingValue').value;
                    highlightStars(value);
                });

                star.addEventListener('click', function () {
                    var value = this.getAttribute('data-value');
                    document.getElementById('ratingValue').value = value;
                    highlightStars(value);
                });
            });

            function highlightStars(value) {
                var stars = document.querySelectorAll('.star');
                stars.forEach(function (star) {
                    if (star.getAttribute('data-value') <= value) {
                        star.classList.remove('bi-star');
                        star.classList.add('bi-star-fill');
                    } else {
                        star.classList.remove('bi-star-fill');
                        star.classList.add('bi-star');
                    }
                });
            }

            // Add event listener for DOMContentLoaded
            document.addEventListener("DOMContentLoaded", function () {
                // Get the initial value from the hidden input
                var value = parseInt(document.getElementById('ratingValue').value);
                // Call highlightStars with the initial value
                highlightStars(value);
            });

        </script>

        </div> <!-- close the middle section div -->

        <footer>
        <div class="footer">
            <div class="contact">
                <div style="text-align:center; ">
                    <p>Need Help?
                        <br>Contact us through any of these support channels
                    </p>
                </div>
                <div class="contact-us">
                    <div class="margin-right">
                        <p><a href="mailto:shopandshiponlinetrading@gmail.com" class="linkFooter">Contact us via
                                Mail<br>
                                <i class="bi-envelope-fill"></i></a></p>
                    </div>

                    <div class="margin-left">
                        <p><a href="https://wa.me/+96106388426" class="linkFooter">Whatsapp Us<br>
                                <i class="bi-whatsapp"></i></a></p>
                    </div>
                </div>
            </div>
            <p style="font-size:0.8rem; ">&copy; 2024 S&S. All Rights Reserved</p>
        </div>
    </footer>

<?php
//this code must be at last
if ($_SESSION['signed_in'] == true && $isClient == true) {
    echo '<div class="bottom-bar-phone">
                        <div class="bar-phone-items">
                            <div><h2 style="color:var(--navly-blue);"><div class="account-select">
                            <i class="bi bi-person-fill-gear"></i>
                            <select id="accountSelect" onchange="goToPage(this.value)">
                                    <option disabled selected>--choose--</option>
                                    <option value="manageAccount.php?forward=viewOrders">View Orders</option>
                                    <option value="manageAccount.php?forward=updateProfile">Update Profile</option>
                                    <option value="manageAccount.php?forward=changePassword">Change Password</option>
                                    <option value="delete">Delete Account</option>
                                </select>
                                </div></h2></div>
                                
                                <div><h4 style="color:var(--navy-blue);">Points:' . $client->getPoints() . '</h4></div>

                            <div><h2 ><div class="cart-icon"><a href="cart.php" style="color:var(--navy-blue);"><i class="bi bi-bag-fill"></i> </a>
                                <span id="nbItems" style="background-color:var(--bordo);">' . $cartItems . '</span></div></h2></div>

                            

                            <div><h2 ><a href="sign_out.php?isClient=1" style="color:var(--navy-blue);"><i class="bi bi-box-arrow-right"></i> </a>   </h2></div>
                            </div>
                    </div>';

} else {
    echo '<div class="bottom-bar-phone">
                        <div class="bar-phone-items">

                            <div><h2 ><div class="cart-icon"><a href="cart.php" style="color:var(--navy-blue);"><i class="bi bi-bag-fill"></i> </a>
                                <span id="nbItems" style="background-color:var(--bordo);">0</span></div></h2></div>

                            <div><h3><a href="sign_in.php" style="color:var(--navy-blue);">Sign In&nbsp<i class="bi bi-person-fill"></i></a></h3></h2></div>
                            </div>
                    </div>';
}


?>

<script>

    function showOptions() {
        var select = document.getElementById('accountSelect');
        select.focus(); // Focus on the select element to show its options
    }

    function goToPage(src) {
        if (src == "delete") {
            openPopUp();
        } else {
            window.location.href = src;
        }
    }

    // Function to close the popup
    function closePopUp() {
        document.getElementById('popup-overlay').style.display = 'none';
        // window.location.href="sign_in.php";
        //window.location.href=next;
    }

    // Function to open the popup when the page loads
    function openPopUp() {
        document.getElementById('popup-overlay').style.display = 'block';
    }

    function deleteAccount() {
        document.getElementById('popup-overlay').style.display = 'none';
        //window.location.href = "deleteAccount.php?del=1";
        window.location.href="manageAccount.php?forward=deleteAccount";
    }

</script>

</body>

</html>