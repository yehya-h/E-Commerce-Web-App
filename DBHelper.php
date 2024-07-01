<?php
include_once ("include_all.php");
class DBHelper extends PDO
{
    public function __construct($servername, $username, $password)
    {
        parent::__construct("mysql:host=" . $servername . ";port=3306;dbname=" . DB_NAME . "", $username, $password);
    }
    // getCategoryByName($name)
    // getCategoriesById($id)
    // getAllCategories()
    // addCategory(parameters)
    // updateCategory(parameters)
    // updateNbProducts($category_id,$n)
    // removeCategory($id)
    // category_unique($name)
    // getMaxCategoryId()
    public function getAllCategories($filter)
    {
        $filters = ["category_id", "name", "nbProducts"];
        if (!in_array($filter, $filters))
            $filter = "category_id";
        $sql = "SELECT * FROM category ORDER BY " . $filter;
        $res = $this->query($sql);
        $categories = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($row['category_id'], $row['name'], $row['nbProducts']);
        }
        return $categories;
    }
    public function getCategoriesByName($name)
    {
        $sql = "SELECT * FROM category WHERE name LIKE :name ORDER BY name";
        $res = $this->prepare($sql);
        $search = '%';
        for ($i = 0; $i < strlen($name); $i++)
            $search .= $name[$i] . '%';
        // or $search='%' . $name . '%';
        $res->bindParam(":name", $search);
        $res->execute();
        $categories = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category($row['category_id'], $row['name'], $row['nbProducts']);
        }
        return $categories;
    }
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM category WHERE category_id=:id";
        $res = $this->prepare($sql);
        $res->bindParam(":id", $id);
        $res->execute();
        $category = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $category = new Category($row['category_id'], $row['name'], $row['nbProducts']);
        return $category;
    }
    public function addCategory(Category $category)
    {
        try {
            $this->serializeTransaction();
            $sql = "INSERT INTO category(name,nbProducts) VALUES (:name,:nbProducts)";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':nbProducts', $category->getNbProducts());
            $stmt->bindValue(':name', $category->getName());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateCategory(Category $category)
    {
        try {
            $this->serializeTransaction();
            $sql = "UPDATE category SET name = :name WHERE category_id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $category->getCategoryId());
            $stmt->bindValue(':name', $category->getName());
            //$stmt->bindParam(':nbProducts', $nbProducts);
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    // public function updateNbProducts($category_id,$n)
    // {
    //     $sql = "UPDATE category SET nbProducts = nbProducts + :n WHERE category_id = :id";
    //     $stmt = $this->prepare($sql);
    //     $stmt->bindParam(':id', $category_id);
    //     $stmt->bindParam(':n', $n);
    //     $stmt->execute();
    // }
    public function removeCategory($id)
    {
        try {
            $this->serializeTransaction();
            $sql = "DELETE FROM category WHERE category_id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function category_unique($name)
    {
        $sql = "SELECT EXISTS(SELECT 1 FROM category WHERE name = :name) AS name_exists";
        $res = $this->prepare($sql);
        $res->bindParam(":name", $name);
        $res->execute();
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row['name_exists'];
    }
    public function getMaxCategoryId()
    {
        $sql = "SET information_schema_stats_expiry = 0;";
        $this->query($sql);
        $sql = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'projectv4' AND TABLE_NAME = 'category';";
        $res = $this->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $max = $row['AUTO_INCREMENT'];
        return $max;
    }

    // getAllProducts()
    // getProductsByName($name)
    // getProductById($id)
    // getProductsByCategory($category)
    // getProductsByPrice($low,$high) //between a and b
    // getProductsByStock($nb) 
    // getMostOrderedProducts()
    // /* $nb==0 out of stock yaane stock=0
    // $nb==1 low in stock yaane stock <100 $nb==2 high in stock yaane stock >=100*/
    // getMinPrice()
    // getMaxPrice()
    // getMaxProductId()
    // addProduct(Product $product)
    // updateProduct(Product $product)
    // updateNbImages(Product $product)
    // updateProductRating($productId)
    // removeProduct(Product $product)
    public function getAllProducts($filter,$sort = 'ASC')
    {
        $filters = ["product_id", "name", "category_id", "stock", "price", "discount", "rating"];
        if (!in_array($filter, $filters))
            $filter = "product_id";
        $sort = (strtoupper($sort) == 'DESC') ? $sort : 'ASC';
        $sql = "SELECT * FROM product ORDER BY " . $filter . " " . $sort;
        $res = $this->query($sql);
        $products = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row['product_id'], $row['name'], $row['category_id'], $row['description'], $row['stock'], $row['price'], $row['discount'], $row['rating'], $row['nb_images']);
        }
        return $products;
    }
    public function getProductsByName($name)
    {
        $sql = "SELECT * FROM product WHERE name LIKE :name ORDER BY name";
        $res = $this->prepare($sql);
        $search = '%';
        for ($i = 0; $i < strlen($name); $i++)
            $search .= $name[$i] . '%';
        // or $search='%' . $name . '%';
        $res->bindParam(":name", $search);
        $res->execute();
        $products = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row['product_id'], $row['name'], $row['category_id'], $row['description'], $row['stock'], $row['price'], $row['discount'], $row['rating'], $row['nb_images']);
        }
        return $products;
    }
    public function getProductById($id)
    {
        $sql = "SELECT * FROM product WHERE product_id=:id";
        $res = $this->prepare($sql);
        $res->bindParam(":id", $id);
        $res->execute();
        $product = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $product = new Product($row['product_id'], $row['name'], $row['category_id'], $row['description'], $row['stock'], $row['price'], $row['discount'], $row['rating'], $row['nb_images']);
        return $product;
    }
    public function getProductsByCategory($category_id)
    {
        $sql = "SELECT * FROM product WHERE category_id=:category_id";
        $res = $this->prepare($sql);
        $res->bindParam(":category_id", $category_id);
        $res->execute();
        $products = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row['product_id'], $row['name'], $row['category_id'], $row['description'], $row['stock'], $row['price'], $row['discount'], $row['rating'], $row['nb_images']);
        }
        return $products;
    }
    public function getProductsByPrice($low, $high)
    {
        $sql = "SELECT * FROM product WHERE price BETWEEN :low AND :high ORDER BY price";//default ASCENDING
        $res = $this->prepare($sql);
        $res->bindParam(":low", $low);
        $res->bindParam(":high", $high);
        $res->execute();
        $products = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row['product_id'], $row['name'], $row['category_id'], $row['description'], $row['stock'], $row['price'], $row['discount'], $row['rating'], $row['nb_images']);
        }
        return $products;
    }
    public function getProductsByStock($nb)
    /* $nb==0 out of stock yaane stock=0
    $nb==1 low in stock yaane stock <100 $nb==2 high in stock yaane stock >=100*/
    {

        switch ($nb) {
            case 0:
                $sql = "SELECT * FROM product WHERE stock = 0 ORDER BY stock";
                break;
            case 1:
                $sql = "SELECT * FROM product WHERE stock < (SELECT AVG(stock) FROM product) ORDER BY stock";
                break;
            case 2:
                $sql = "SELECT * FROM product WHERE stock >= (SELECT AVG(stock) FROM product) ORDER BY stock";
                break;
            default:
                return null;
        }
        $res = $this->query($sql);
        $products = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row['product_id'], $row['name'], $row['category_id'], $row['description'], $row['stock'], $row['price'], $row['discount'], $row['rating'], $row['nb_images']);
        }
        return $products;
    }
    public function getMostOrderedProducts()
    {
        $sql = "SELECT p.*, o.count FROM product p JOIN (SELECT product_id, COUNT(*) AS count FROM orderitem GROUP BY product_id ) o ON p.product_id = o.product_id ORDER BY o.count DESC";
        $res = $this->query($sql);
        $products = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $product = new Product($row['product_id'], $row['name'], $row['category_id'], $row['description'], $row['stock'], $row['price'], $row['discount'], $row['rating'], $row['nb_images']);
            $products[] = array($product, $row['count']);
        }
        return $products;
    }
    public function getMinPrice(int $category_id = null)// optional parameter
    {
        $sql = "SELECT MIN(price) AS minPrice FROM product";
        if (isset($category_id)) {
            $sql = "SELECT MIN(price) AS minPrice FROM product WHERE category_id = :category_id";
            $res = $this->prepare($sql);
            $res->bindParam(":category_id", $category_id);
            $res->execute();
        } else
            $res = $this->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $min = $row['minPrice'];
        if ($min == null)
            return 0;
        else
            return $min;
    }
    public function getMaxPrice()
    {
        $sql = "SELECT MAX(price) AS maxPrice FROM product";
        $res = $this->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $max = $row['maxPrice'];
        if ($max == null)
            return 0;
        else
            return $max;
    }
    public function getMaxProductId()
    {
        $sql = "SET information_schema_stats_expiry = 0;";
        $this->query($sql);
        $sql = "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'projectv4' AND TABLE_NAME = 'product'";
        $res = $this->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $max = $row['AUTO_INCREMENT'];
        // if ($max == null)
        //     return 0;
        // else
        return $max;
    }
    public function addProduct(Product $product)
    {
        try {
            $this->serializeTransaction();
            $sql = "INSERT INTO product(name,category_id,description,stock,price,discount,rating,nb_images) VALUES (:name,:category_id,:description,:stock,:price,:discount,:rating, :nb_images);
                UPDATE category SET nbProducts = nbProducts + 1 WHERE category_id = :category_id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':name', $product->getName());
            $stmt->bindValue(':category_id', $product->getCategoryId());
            $stmt->bindValue(':description', $product->getDescription());
            $stmt->bindValue(':stock', $product->getStock());
            $stmt->bindValue(':price', $product->getPrice());
            $stmt->bindValue(':discount', $product->getDiscount());
            $stmt->bindValue(':rating', $product->getRating());
            $stmt->bindValue(':nb_images', $product->getNbImages());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateProduct(Product $product)
    {
        try {
            $this->serializeTransaction();
            $sql = "UPDATE category SET nbProducts = nbProducts - 1 WHERE category_id = (SELECT category_id FROM product WHERE product_id = :id);
                UPDATE product SET name = :name,category_id = :category_id,description = :description,stock = :stock,price = :price,discount = :discount,rating = :rating WHERE product_id = :id;
                UPDATE category SET nbProducts = nbProducts + 1 WHERE category_id = :category_id;";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $product->getProductId());
            $stmt->bindValue(':name', $product->getName());
            $stmt->bindValue(':category_id', $product->getCategoryId());
            $stmt->bindValue(':description', $product->getDescription());
            $stmt->bindValue(':stock', $product->getStock());
            $stmt->bindValue(':price', $product->getPrice());
            $stmt->bindValue(':discount', $product->getDiscount());
            $stmt->bindValue(':rating', $product->getRating());
            //$stmt->bindValue(':nb_images',$product->getNbImages());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateNbImages(Product $product)
    {
        try {
            $this->serializeTransaction();
            $sql = "UPDATE product SET nb_images = :nb_images WHERE product_id = :id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':id', $product->getProductId());
            $stmt->bindValue(':nb_images', $product->getNbImages());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateProductRating($productId)
    {
        try {
            $this->serializeTransaction();
            $sql = "UPDATE product SET rating = (SELECT AVG(value) FROM rating WHERE product_id = :product_id) WHERE product_id = :product_id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(':product_id', $productId);
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function removeProduct(Product $product)
    {
        try {
            $this->serializeTransaction();
            $sql = "DELETE FROM product WHERE product_id = :product_id;
                UPDATE category SET nbProducts = nbProducts - 1 WHERE category_id = :category_id;";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':product_id', $product->getProductId());
            $stmt->bindValue(':category_id', $product->getCategoryId());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    // getAllCountries()
    // getCountriesByName($name)
    // country_unique($country_name)
    // addCountry(parameters)
    // updateCountry(parameters)
    // removeCountry($id)
    public function getAllCountries($filter)
    {
        $filters = ["country_name", "delivery_time"];
        if (!in_array($filter, $filters))
            $filter = "country_name";
        $sql = "SELECT * FROM country ORDER BY " . $filter;
        $res = $this->query($sql);
        $countries = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $countries[] = new Country($row['country_name'], $row['delivery_time'], $row['delivery_fees']);
        }
        return $countries;
    }
    public function getCountriesByName($name)
    {
        $sql = "SELECT * FROM country WHERE country_name LIKE :name ORDER BY country_name";
        $res = $this->prepare($sql);
        $search = '%';
        for ($i = 0; $i < strlen($name); $i++)
            $search .= $name[$i] . '%';
        // or $search='%' . $name . '%';
        $res->bindParam(":name", $search);
        $res->execute();
        $countries = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $countries[] = new Country($row['country_name'], $row['delivery_time'], $row['delivery_fees']);
        }
        return $countries;
    }
    public function getCountryByName($name)
    {
        $sql = "SELECT * FROM country WHERE country_name = :name";
        $res = $this->prepare($sql);
        $res->bindParam(":name", $name);
        $res->execute();
        $country = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $country = new Country($row['country_name'], $row['delivery_time'], $row['delivery_fees']);
        return $country;
    }
    public function country_unique($country_name)
    {
        $sql = "SELECT EXISTS(SELECT 1 FROM country WHERE country_name = :country_name) AS name_exists";
        $res = $this->prepare($sql);
        $res->bindParam(":country_name", $country_name);
        $res->execute();
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row['name_exists'];
    }
    public function addCountry(Country $country)
    {
        try {
            $this->serializeTransaction();
            $sql = "INSERT INTO country(country_name,delivery_time,delivery_fees) VALUES (:country_name,:delivery_time,:delivery_fees)";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':country_name', $country->getCountryName());
            $stmt->bindValue(':delivery_time', $country->getDeliveryTime());
            $stmt->bindValue(':delivery_fees', $country->getDeliveryFees());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateCountry(Country $country)//Update delivery_time
    {
        try {
            $this->serializeTransaction();
            $sql = "UPDATE country SET delivery_time = :delivery_time, delivery_fees = :delivery_fees WHERE country_name = :country_name";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':country_name', $country->getCountryName());
            $stmt->bindValue(':delivery_time', $country->getDeliveryTime());
            $stmt->bindValue(':delivery_fees', $country->getDeliveryFees());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function removeCountry($country_name)
    {
        try {
            $this->serializeTransaction();
            $sql = "DELETE FROM country WHERE country_name = :country_name";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(':country_name', $country_name);
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    // getAllOrders()
    // getOrdersByDate($date)
    // getOrdersByAmount($amount)
    // getOrdersByClientId($id)
    // getOrderById($id) //order 
    // getNbOrdersWithDate($days)
    // isOrderedProduct($product_id, $client_id)
    // addOrder(Order $order)
    public function getAllOrders($filter)
    {
        $filters = ["order_id", "client_id", "order_date", "selected_shipmentInfo_id", "total_amount"];
        if (!in_array($filter, $filters))
            $filter = "order_id";
        $sql = "SELECT * FROM projectv4.order ORDER BY " . $filter;
        $res = $this->query($sql);
        $orders = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = new Order($row['order_id'], $row['client_id'], $row['order_date'], $row['selected_shipmentInfo_id'], $row['total_amount']);
        }
        return $orders;
    }
    public function getOrderById($id)
    {
        $sql = "SELECT * FROM projectv4.order WHERE order_id=:id";
        $res = $this->prepare($sql);
        $res->bindParam(":id", $id);
        $res->execute();
        $order = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $order = new Order($row['order_id'], $row['client_id'], $row['order_date'], $row['selected_shipmentInfo_id'], $row['total_amount']);
        return $order;
    }
    public function getOrdersByAmount($amount)
    {
        $sql = "SELECT * FROM projectv4.order WHERE total_amount=:amount ORDER BY total_amount";
        $res = $this->prepare($sql);
        $res->bindParam(":amount", $amount);
        $res->execute();
        $orders = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = new Order($row['order_id'], $row['client_id'], $row['order_date'], $row['selected_shipmentInfo_id'], $row['total_amount']);
        }
        return $orders;
    }
    public function getOrdersByDate($date)
    {
        $sql = "SELECT * FROM projectv4.order WHERE order_date=:date ORDER BY order_date";
        $res = $this->prepare($sql);
       // $date=date($date);
        //date_for
        //date_format()
         $res->bindValue(":date", $date->format('Y-m-d'));
        //$d=new DateTime()
        //$res->bindValue(":date", date_format($date,'Y-m-d'));
        $res->execute();
        $orders = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = new Order($row['order_id'], $row['client_id'], $row['order_date'], $row['selected_shipmentInfo_id'], $row['total_amount']);
        }
        return $orders;
    }
    public function getOrdersByClientId($id)
    {
        $sql = "SELECT * FROM projectv4.order WHERE client_id=:id ORDER BY client_id";
        $res = $this->prepare($sql);
        $res->bindParam(":id", $id);
        $res->execute();
        $orders = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $orders[] = new Order($row['order_id'], $row['client_id'], $row['order_date'], $row['selected_shipmentInfo_id'], $row['total_amount']);
        }
        return $orders;
    }
    public function getNbOrdersWithDate($format, DateTime $startDate, DateTime $endDate = null, $sort = 'ASC')
    {
        $formats = ['day', 'week', 'month', 'year'];
        $format = strtolower($format);
        if (!in_array($format, $formats))
            return null;
        if ($endDate === null)
            $endDate = new DateTime();
        $startDateStr = $startDate->format('Y-m-d');
        $endDateStr = $endDate->format('Y-m-d');
        $sort = strtoupper($sort);
        $sort = ($sort == 'ASC' || $sort == 'DESC') ? $sort : 'ASC';
        switch ($format) {
            case 'day':
                $sql = "SELECT order_date,  COUNT(order_id) AS num_orders FROM projectv4.order WHERE order_date BETWEEN :startDate AND :endDate GROUP BY order_date ORDER BY order_date " . $sort;
                break;
            case 'week':
                $sql = "SELECT WEEK(order_date) AS week_number, COUNT(order_id) AS num_orders FROM projectv4.order WHERE order_date BETWEEN :startDate AND :endDate GROUP BY WEEK(order_date) ORDER BY WEEK(order_date) " . $sort;
                break;
            case 'month':
                $sql = "SELECT YEAR(order_date) AS year, MONTH(order_date) AS month, COUNT(order_id) AS num_orders FROM projectv4.order WHERE order_date BETWEEN :startDate AND :endDate GROUP BY YEAR(order_date), MONTH(order_date) ORDER BY YEAR(order_date), MONTH(order_date) " . $sort;
                break;
            case 'year':
                $sql = "SELECT YEAR(order_date) AS year, COUNT(order_id) AS num_orders FROM projectv4
                .order WHERE order_date BETWEEN :startDate AND :endDate GROUP BY YEAR(order_date) ORDER BY YEAR(order_date) " . $sort;
                break;
            default:
                return null;
        }
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':startDate', $startDateStr);
        $stmt->bindValue(':endDate', $endDateStr);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $interval = $startDate->diff($endDate);
        $daysDiff = $interval->days;
        $res = null;
        switch ($format) {
            case 'day':
                for ($i = 0; $i <= $daysDiff; $i++) {
                    $availableDates = array_column($results, 'order_date');
                    $day = date('Y-m-d', strtotime($startDateStr . ' +' . ($i) . ' days'));
                    $res[$i] = array('order_date' => $day, 'num_orders' => (in_array($day, $availableDates)) ? $results[array_search($day, $availableDates)]['num_orders'] : 0);
                }
                break;
            case 'week':
                for ($i = 0; $i <= $daysDiff; $i = $i + 7) {
                    $availableWeeks = array_column($results, 'week_number');
                    $week = date('W', strtotime($startDateStr . ' +' . ($i) . ' days'));
                    $res[$i / 7] = array('week_number' => $week, 'num_orders' => (in_array($week, $availableWeeks)) ? $results[array_search($week, $availableWeeks)]['num_orders'] : 0);
                }
                break;
            case 'month':
                // to be continued
                return $results;
            //break;
            case 'year':
                // to be continued
                return $results;
            //break;
            default:
        }
        return $res;
    }
    public function isOrderedProduct($product_id, $client_id)
    {
        $sql = "SELECT EXISTS(SELECT 1 FROM orderitem WHERE order_id IN (SELECT order_id FROM projectv4.order WHERE client_id = :client_id) AND product_id = :product_id) AS is_ordered";
        $res = $this->prepare($sql);
        $res->bindParam(":product_id", $product_id);
        $res->bindParam(":client_id", $client_id);
        $res->execute();
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row['is_ordered'];// 1 if exists, 0 otherwise
    }
    // public function addOrder(Order $order)
    // {
    //     try {
    //         $this->serializeTransaction();
    //         $sql = "INSERT INTO project.order(client_id,order_date,selected_shipmentInfo_id,tolal_amount) VALUES (:client_id,:order_date,:selected_shipmentInfo_id,:tolal_amount);";
    //         $stmt = $this->prepare($sql);
    //         $stmt->bindValue(':client_id', $order->getClientId());
    //         $stmt->bindValue(':order_date', $order->getOrderDate());
    //         $stmt->bindValue(':selected_shipmentInfo_id', $order->getSelectedShipmentInfoId());
    //         $stmt->bindValue(':tolal_amount', $order->getTotalAmount());
    //         $stmt->execute();
    //         $stmt->closeCursor();
    //         $done = $this->commit();
    //     } catch (PDOException $e) {
    //         $this->rollBack();
    //         die("Connection failed: " . $e->getMessage());
    //     }
    //     return $done;
    // }
    public function addOrder($clientId, array $cartItemId, $selectedShipInfoId, int $points)
    {
        $id=-1;
        $subTotal_amount = 0;
        $html = "";
        foreach ($cartItemId as $itemId) {
            $item = $this->getCartItemByItemId($itemId);
            $product = $this->getProductById($item->getProductId());
            if ($product->getStock() < $item->getQuantity())
                return false;// or header("Location: cart.php");
            $productId = $product->getProductId();
            $price = ($product->getPrice() * ((100 - $product->getDiscount()) / 100)) * $item->getQuantity();
            $subTotal_amount += $price;
            $html .= "<tr>
                        <td label=\"Item\">" . $product->getName() . "</td>
                        <td label=\"Quantity\">" . $item->getQuantity() . "</td>
                        <td label=\"Price (Per Unit)\">$" . $product->getPrice() * ((100 - $product->getDiscount()) / 100) . "</td>
                        <td label=\"Total Price\">$" . $price . "</td>
                    </tr>";
            $orderItems[] = new OrderItem(null, null, $productId, $item->getQuantity());
        }
        $selectedShipInfo = $this->getShipmentInfoByShipmentId($selectedShipInfoId);
        $country = $this->getCountryByName($selectedShipInfo->getCountryName());
        $total_amount = $subTotal_amount + $country->getDeliveryFees() - ($points / 10);
        $extraPoints = (int) ($subTotal_amount / 20) * 10;
        $order = new Order(null, $clientId, date('Y-m-d'), $selectedShipInfoId, $total_amount);
        try {
            $sql_1 = "";//".."
            $sql_2 = "";
            $sql_3 = "";
            $sql_4 = "";
            foreach ($orderItems as $key => $value) {
                $sql_1 .= "(:item_ido" . $key . ", @orderId,:product_ido" . $key . ",:quantityo" . $key . ")";
                $sql_2 .= " WHEN product_id = :product_ido" . $key . " THEN stock - :quantityo" . $key . " ";
                $sql_3 .= ":product_ido" . $key;
                if ($key != array_key_last($orderItems)) {
                    $sql_1 .= ", ";
                    $sql_3 .= ", ";
                }
            }
            foreach ($cartItemId as $key => $value) {
                $sql_4 .= ":item_idc" . $key;
                if ($key != array_key_last($orderItems))
                    $sql_4 .= ", ";
            }

            $invoice='   <div class=\'order-table-container\'> <table><thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price (per unit)</th>
                <th>Total Price</th>
                </tr></thead><tbody>
                ' . $html . '
                <tr class="total">
                <td colspan="3"><b>SubTotal</b></td>
                <td>$' . $subTotal_amount . '</td>
                </tr>
                <tr class="total">
                <td colspan="3"><b>DeliveryFees</b></td>
                <td>$' . $country->getDeliveryFees() . '</td>
                </tr>
                <tr class="total">
                <td colspan="3"><b>Coupon</b></td>
                <td>$' . ($points / 10) . '</td>
                </tr>
                <tr class="total">
                    <td colspan="3"><b>Total Price</b></td>
                    <td>$<b>' . number_format($total_amount, 2) . '</b></td>
                </tr></tbody>
            </table></div>';

            $this->serializeTransaction();
            // DECLARE orderId INT;
            // SELECT MAX(order_id) INTO orderId FROM project.order WHERE client_id = :client_id;
            // SET @orderId = LAST_INSERT_ID();
            $sql = "INSERT INTO projectv4.order(client_id,order_date,selected_shipmentInfo_id,total_amount) VALUES (:client_id,:order_date,:selected_shipmentInfo_id,:tolal_amount);
                    SET @orderId = (SELECT MAX(order_id) FROM `projectv4`.`order` WHERE client_id = :client_id);
                    INSERT INTO orderitem(item_id,order_id,product_id,quantity) VALUES " . $sql_1 . ";
                    UPDATE product SET stock = CASE " . $sql_2 . " ELSE stock END WHERE product_id IN ( " . $sql_3 . " );
                    DELETE FROM cartitem WHERE item_id IN ( " . $sql_4 . " );
                    UPDATE client SET points = points - :points + :extraPoints WHERE client_id = :client_id;
                    INSERT INTO invoice(order_id,text) VALUES (@orderId,:text) ";

            $stmt = $this->prepare($sql);
            $stmt->bindValue(':client_id', $order->getClientId());
            $stmt->bindValue(':order_date', $order->getOrderDate());
            $stmt->bindValue(':selected_shipmentInfo_id', $order->getSelectedShipmentInfoId());
            $stmt->bindValue(':tolal_amount', $order->getTotalAmount());
            $stmt->bindValue(':points', $points);
            $stmt->bindValue(':extraPoints', $extraPoints);
            foreach ($orderItems as $key => $orderItem) {
                $stmt->bindValue(':item_ido' . $key, $orderItem->getItemId());
                $stmt->bindValue(':product_ido' . $key, $orderItem->getProductId());
                $stmt->bindValue(':quantityo' . $key, $orderItem->getQuantity());
            }
            foreach ($cartItemId as $key => $value)
                $stmt->bindValue(':item_idc' . $key, $value);
            $stmt->bindValue(":text",$invoice);
            $stmt->execute();
            //$id=$this->lastInsertId();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        if ($done) {
            $body = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Order Summary</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                        margin: 0;
                        padding: 20px;
                        background-color: #f4f4f4;
                    }
                    .container {
                        max-width: 600px;
                        margin: auto;
                        background: #fff;
                        padding: 20px;
                        border-radius: 5px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    }
                    h1, h2 {
                        text-align: center;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    .total {
                        text-align: right;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Order Summary</h1>
                    <p>Hello ' . $selectedShipInfo->getFullName() . ',</p>
                    <p>Thank you for your purchase from our S&S website. Below is a summary of your order:</p>
            
                    <table>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price (per unit)</th>
                        <th>Total Price</th>
                        </tr>
                        ' . $html . '
                        <tr class="total">
                        <td colspan="3">SubTotal</td>
                        <td>$' . $subTotal_amount . '</td>
                        </tr>
                        <tr class="total">
                        <td colspan="3">DeliveryFees</td>
                        <td>$' . $country->getDeliveryFees() . '</td>
                        </tr>
                        <tr class="total">
                        <td colspan="3">Coupon</td>
                        <td>$' . ($points / 10) . '</td>
                        </tr>
                        <tr class="total">
                            <td colspan="3">Total Price</td>
                            <td>$' . number_format($total_amount, 2) . '</td>
                        </tr>
                    </table>

                    <strong>You earned ' . $extraPoints . ' points.</strong>
                   <p>Order will be delivered within '.($country->getDeliveryTime()-2).' to '.($country->getDeliveryTime()+2).' days.</p>
                    <p>If you have any questions about your order, please feel free to contact us.</p>
            
                    <p>Thank you,<br>
                    S&S Team</p>
                </div>
            </body>
            </html>';
            $done = Functions::sendMail(
                COMPANY_MAIL,
                MAIL_APP_PASSWORD,
                ($this->getAccountById(($this->getClientByClientId($clientId))->getAccountId()))->getEmail(),
                "Order Summary",
                $body
            );

            // if($sent){
            //     try{
            //     $this->serializeTransaction();
            //     $sql="SELECT MAX(order_id) FROM projectv4.order";
            //     $res=$this->prepare($sql);
            //     $res->execute();
            //     $id=($res->fetch(PDO::FETCH_ASSOC))['order_id'];
            //     $this->commit();
            //     }
            //     catch(PDOException $e){
            //         $this->rollBack();
            //         die("Connection Failed: ".$e->getMessage());
            //     }

                // $invoice='    <table>
                //     <tr>
                //         <th>Item</th>
                //         <th>Quantity</th>
                //         <th>Price (per unit)</th>
                //         <th>Total Price</th>
                //         </tr>
                //         ' . $html . '
                //         <tr class="total">
                //         <td colspan="3">SubTotal</td>
                //         <td>$' . $subTotal_amount . '</td>
                //         </tr>
                //         <tr class="total">
                //         <td colspan="3">DeliveryFees</td>
                //         <td>$' . $country->getDeliveryFees() . '</td>
                //         </tr>
                //         <tr class="total">
                //         <td colspan="3">Coupon</td>
                //         <td>$' . ($points / 10) . '</td>
                //         </tr>
                //         <tr class="total">
                //             <td colspan="3">Total Price</td>
                //             <td>$' . number_format($total_amount, 2) . '</td>
                //         </tr>
                //     </table>';

            //     $done=$this->addInvoice($invoice,$id);
            // }

             //send an email for the store keeper
        }
        return $done;
    }
    public function addOrderItem(OrderItem $orderItem)
    {
        try {
            $this->serializeTransaction();
            $sql = "INSERT INTO orderitem(item_id,order_id,product_id,quantity) VALUES (:item_id,:order_id,:product_id,:quantity);
                    UPDATE product SET stock -= :quantity WHERE product_id = :product_id;";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':item_id', $orderItem->getItemId());
            $stmt->bindValue(':order_id', $orderItem->getOrderId());
            $stmt->bindValue(':product_id', $orderItem->getProductId());
            $stmt->bindValue(':quantity', $orderItem->getQuantity());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    //getShipmentInfoByShipmentId($id)
    //getShippmentInfoByClientId($client_id)
    //getSelectedShipmentInfo($client_id , $order_id) // make sure orderid= orderid
    public function getShipmentInfoByShipmentId($id)
    {
        $sql = "SELECT * FROM shipmentinfo WHERE shipmentInfo_id = :shipmentInfo_id";
        $res = $this->prepare($sql);
        $res->bindParam(":shipmentInfo_id", $id);
        $res->execute();
        $shipmentInfo = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $shipmentInfo = new ShipmentInfo($row['shipmentInfo_id'], $row['country_name'], $row['client_id'], $row['fullName'], $row['street_nb'], $row['building'], $row['city'], $row['state'], $row['zipCode'], $row['phoneNumber']);
        return $shipmentInfo;
    }
    public function getShipmentInfoByClientId($client_id)
    {
        $sql = "SELECT * FROM shipmentinfo WHERE client_id=:client_id ORDER BY client_id";
        $res = $this->prepare($sql);
        $res->bindParam(":client_id", $client_id);
        $res->execute();
        $shipmentInfo = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $shipmentInfo[] = new ShipmentInfo($row['shipmentInfo_id'], $row['country_name'], $row['client_id'], $row['fullName'], $row['street_nb'], $row['building'], $row['city'], $row['state'], $row['zipCode'], $row['phoneNumber']);
        }
        return $shipmentInfo;
    }
    public function getSelectedShipmentInfo($client_id, $order_id)
    {
        $sql = "SELECT * FROM shipmentinfo WHERE shipmentInfo_id = (SELECT selected_shipmentInfo_id FROM projectv4.order WHERE client_id=:client_id AND order_id=:order_id)";
        $res = $this->prepare($sql);
        $res->bindParam(":client_id", $client_id);
        $res->bindParam(":order_id", $order_id);
        $res->execute();
        $shipmentInfo = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $shipmentInfo = new ShipmentInfo($row['shipmentInfo_id'], $row['country_name'], $row['client_id'], $row['fullName'], $row['street_nb'], $row['building'], $row['city'], $row['state'], $row['zipCode'], $row['phoneNumber']);
        return $shipmentInfo;
    }
    // getClientInfoByAccountId($account_id)
    // getClientInfoByClientId($client_id)
    // getClientInfo() // need acc-id ,client_id,  first name,last name , phone nb , email 
    // (Make sure to "WHERE isAdmin=0")
    public function getClientInfoByAccountId($account_id)
    { // need acc-id ,client_id,  first name,last name , phone nb , email // (Make sure to "WHERE isAdmin=0")
        $sql = "SELECT a.account_id, c.client_id, c.firstName, c.lastName ,c.phone_number, a.email FROM account a ,client c WHERE  a.isAdmin = 0 AND a.account_id = :account_id AND a.account_id = c.account_id ORDER BY a.account_id";
        $res = $this->prepare($sql);
        $res->bindParam(":account_id", $account_id);
        $res->execute();
        $client = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $client[] = array('client_id' => $row['client_id'], 'account_id' => $row['account_id'], 'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'phone_number' => $row['phone_number'], 'email' => $row['email']);
        return $client;
    }
    public function getClientInfoByClientId($client_id)
    { // need acc-id ,client_id,  first name,last name , phone nb , email // (Make sure to "WHERE isAdmin=0")
        $sql = "SELECT a.account_id, c.client_id, c.firstName, c.lastName ,c.phone_number, a.email FROM account a ,client c WHERE c.client_id = :client_id AND a.isAdmin = 0 AND a.account_id = c.account_id ORDER BY c.client_id";
        $res = $this->prepare($sql);
        $res->bindParam(":client_id", $client_id);
        $res->execute();
        $client = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $client[] = array('client_id' => $row['client_id'], 'account_id' => $row['account_id'], 'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'phone_number' => $row['phone_number'], 'email' => $row['email']);
        return $client;
    }
    public function getClientInfo()
    { // need acc-id ,client_id,  first name,last name , phone nb , email // (Make sure to "WHERE isAdmin=0")
        $sql = "SELECT a.account_id, c.client_id, c.firstName, c.lastName ,c.phone_number, a.email FROM account a ,client c WHERE a.isAdmin = 0 AND a.account_id = c.account_id ORDER BY c.client_id";
        $res = $this->query($sql);
        $clients = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $clients[] = array('client_id' => $row['client_id'], 'account_id' => $row['account_id'], 'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'phone_number' => $row['phone_number'], 'email' => $row['email']);
        }
        return $clients;
    }
    // getClientByClientId($client_id)
    // getClientByToken($token)
    // getTotalClientsNumber()
    // getActiveClientsNumber()
    // getNbOrdersWithDate($days)
    // removeClient($client_id)
    public function getClientByClientId($client_id)
    {
        $sql = "SELECT * FROM client WHERE client_id=:client_id";
        $res = $this->prepare($sql);
        $res->bindParam(":client_id", $client_id);
        $res->execute();
        $client = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $client = new Client($row['client_id'], $row['account_id'], $row['firstName'], $row['lastName'], $row['phone_number'], $row['cart_id'], $row['paymentInfo_id'], $row['points']);
        return $client;
    }
    public function getClientByToken($token)
    {
        $sql = "SELECT c.* FROM account a, client c WHERE a.token = :token AND a.account_id = c.account_id";
        $res = $this->prepare($sql);
        $res->bindParam(":token", $token);
        $res->execute();
        $client = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $client = new Client($row['client_id'], $row['account_id'], $row['firstName'], $row['lastName'], $row['phone_number'], $row['cart_id'], $row['paymentInfo_id'], $row['points']);
        return $client;
    }
    public function getTotalClientsNumber()
    {
        $sql = "SELECT COUNT(client_id) AS clientsCount FROM client";
        $res = $this->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $count = $row['clientsCount'];
        if ($count == null)
            return 0;
        else
            return $count;
    }
    public function getActiveClientsNumber()
    {
        $sql = "SELECT COUNT(DISTINCT client_id) AS clientsCount FROM projectv4.order";
        $res = $this->query($sql);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $count = $row['clientsCount'];
        if ($count == null)
            return 0;
        else
            return $count;
    }
    public function removeClient($client_id)
    {
        try {
            $this->serializeTransaction();
            $sql = "DELETE FROM client WHERE client_id = :client_id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(':client_id', $client_id);
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    // getCartIdByToken($cart_id)
    // getCartItemsByCartId($cart_id)
    // getCartItemByproductId($cart_id, $product_id)
    // addCartItem($cartItem)
    // updateCartItemQuantity($cartItem)
    // updateQuantity($quantity)
    // removeCartItem($id)
    public function getCartIdByToken($token)
    {
        $sql = "SELECT c.cart_id FROM account a, client c WHERE a.token = :token AND a.account_id = c.account_id";
        $res = $this->prepare($sql);
        $res->bindParam(":token", $token);
        $res->execute();
        $cart_id = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $cart_id = $row['cart_id'];
        return $cart_id;
    }
    public function getCartItemsByCartId($cart_id)
    {
        $sql = "SELECT * FROM cartitem WHERE cart_id = :cart_id";
        $res = $this->prepare($sql);
        $res->bindParam(":cart_id", $cart_id);
        $res->execute();
        $cartItems = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $cartItems[] = new CartItem($row['item_id'], $row['cart_id'], $row['product_id'], $row['quantity']);
        }
        return $cartItems;
    }
    public function getCartItemByItemId($item_id)
    {
        $sql = "SELECT * FROM cartitem WHERE item_id = :item_id";
        $res = $this->prepare($sql);
        $res->bindParam(":item_id", $item_id);
        $res->execute();
        $cartItem = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $cartItem = new CartItem($row['item_id'], $row['cart_id'], $row['product_id'], $row['quantity']);
        return $cartItem;
    }
    public function getCartItemByCaPrId($cart_id, $product_id)
    {
        $sql = "SELECT * FROM cartitem WHERE cart_id = :cart_id AND product_id = :product_id";
        $res = $this->prepare($sql);
        $res->bindParam(":cart_id", $cart_id);
        $res->bindParam(":product_id", $product_id);
        $res->execute();
        $cartItem = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $cartItem = new CartItem($row['item_id'], $row['cart_id'], $row['product_id'], $row['quantity']);
        return $cartItem;
    }
    public function addCartItem(CartItem $cartItem)
    {
        try {
            $this->serializeTransaction();
            $sql = "INSERT INTO cartitem(cart_id,product_id,quantity) VALUES (:cart_id,:product_id,:quantity)";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':cart_id', $cartItem->getCartId());
            $stmt->bindValue(':product_id', $cartItem->getProductId());
            $stmt->bindValue(':quantity', $cartItem->getQuantity());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateCartItemQuantity(CartItem $cartItem)
    {
        try {
            $this->serializeTransaction();
            $sql = "UPDATE cartitem SET quantity = :quantity WHERE item_id = :item_id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':item_id', $cartItem->getItemId());
            $stmt->bindValue(':quantity', $cartItem->getQuantity());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateQuantities(array $quantities)
    {
        $sql = "UPDATE cartitem SET quantity = CASE ";
        foreach ($quantities as $id => $quantity)
            $sql .= " WHEN item_id = :id" . $id . " THEN :quantity" . $id . " ";
        $sql .= "ELSE quantity END WHERE item_id IN ( ";
        foreach ($quantities as $id => $quantity)
            $sql .= ":id" . $id . " , ";
        $sql .= "0);";
        $stmt = $this->prepare($sql);
        foreach ($quantities as $id => $quantity) {
            $stmt->bindValue(':id' . $id, $id);
            $stmt->bindValue(':quantity' . $id, $quantity);
        }
        $done = $stmt->execute();
        $stmt->closeCursor();
        return $done;
    }
    public function removeCartItem($id)
    {
        // try {
        //     $this->serializeTransaction();
        $sql = "DELETE FROM cartitem WHERE item_id = :item_id";
        $stmt = $this->prepare($sql);
        $stmt->bindParam(':item_id', $id);
        $done = $stmt->execute();
        //     $stmt->closeCursor();
        //     $done = $this->commit();
        // } catch (PDOException $e) {
        //     $this->rollBack();
        //     die("Connection failed: " . $e->getMessage());
        // }
        return $done;
    }
    // getAllAdmins($filter) 
    // getAdminByAdminID($admin_id)
    // getAdminByAccountId($account_id) 
    // getAdminsByFirstName($firstName) 
    // getAdminsByLastName($lastName) 
    // getAdminsByEmail($email)
    // removeAdmin($admin_id)
    public function getAllAdmins($filter)
    {
        $sql = "SELECT a.*, acc.email FROM admin a , account acc WHERE a.account_id = acc.account_id ORDER BY " . $filter;
        $res = $this->query($sql);
        $admins = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $admins[] = array('admin_id' => $row['admin_id'], 'account_id' => $row['account_id'], 'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'email' => $row['email'], 'address' => $row['address'], 'phone_number' => $row['phone_number']);
        }
        return $admins;
    }
    public function getAdminByAdminID($admin_id)
    {
        $sql = "SELECT a.*, acc.email FROM admin a , account acc WHERE a.admin_id = :admin_id AND a.account_id = acc.account_id";
        $res = $this->prepare($sql);
        $res->bindParam(":admin_id", $admin_id);
        $res->execute();
        $admin = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC)){

            $admin[$row['email']]=new Admin($row['admin_id'],$row['account_id'],$row['firstName'],$row['lastName'],$row['address'],$row['phone_number']);

        }
            //$admin = array('admin_id' => $row['admin_id'], 'account_id' => $row['account_id'], 'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'email' => $row['email'], 'address' => $row['address'], 'phone_number' => $row['phone_number']);
        return $admin;
    }
    public function getAdminByAccountId($account_id)
    {
        $sql = "SELECT a.*, acc.email FROM admin a , account acc WHERE a.account_id = :account_id AND a.account_id = acc.account_id";
        $res = $this->prepare($sql);
        $res->bindParam(":account_id", $account_id);
        $res->execute();
        $admin = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC)){

            $admin[$row['email']]=new Admin($row['admin_id'],$row['account_id'],$row['firstName'],$row['lastName'],$row['address'],$row['phone_number']);
            // $admin=array();
            // $admin['admin_id']=(!empty($row['admin_id']))? $row['admin_id'] : '';

            // $admin['account_id']=(!empty($row['account_id']))? $row['account_id'] : '';
            // $admin['firstName']=(!empty($row['firstName']))? $row['firstName'] : '';
            // $admin['lastName']=(!empty($row['lastName']))? $row['lastName'] : '';
            // $admin['email']=(!empty($row['email']))? $row['email'] : '';
            // $admin['address']=(!empty($row['address']))? $row['address'] : '';
            // $admin['phone_number']=(!empty($row['phone_number']))? $row['phone_number'] : '';

        //     $admin = array('admin_id' => $row['admin_id'], 'account_id' => $row['account_id'],
        //  'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'email' => $row['email'],
        //   'address' => $row['address'], 'phone_number' => $row['phone_number']);
        }
        return $admin;
    }
    public function getAdminsByFirstName($name)
    {
        $sql = "SELECT a.*, acc.email FROM admin a , account acc WHERE a.firstName LIKE :name AND a.account_id = acc.account_id ORDER BY firstName";
        $res = $this->prepare($sql);
        $search = '%';
        for ($i = 0; $i < strlen($name); $i++)
            $search .= $name[$i] . '%';
        // or $search='%' . $name . '%';
        $res->bindParam(":name", $search);
        $res->execute();
        $admins = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $admins[] = array('admin_id' => $row['admin_id'], 'account_id' => $row['account_id'], 'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'email' => $row['email'], 'address' => $row['address'], 'phone_number' => $row['phone_number']);
        }
        return $admins;
    }
    public function getAdminsByLastName($name)
    {
        $sql = "SELECT a.*, acc.email FROM admin a , account acc WHERE a.lastName LIKE :name AND a.account_id = acc.account_id ORDER BY lastName";
        $res = $this->prepare($sql);
        $search = '%';
        for ($i = 0; $i < strlen($name); $i++)
            $search .= $name[$i] . '%';
        // or $search='%' . $name . '%';
        $res->bindParam(":name", $search);
        $res->execute();
        $admins = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $admins[] = array('admin_id' => $row['admin_id'], 'account_id' => $row['account_id'], 'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'email' => $row['email'], 'address' => $row['address'], 'phone_number' => $row['phone_number']);
        }
        return $admins;
    }
    public function getAdminsByEmail($name)
    {
        $sql = "SELECT a.*, acc.email FROM admin a , account acc WHERE acc.email LIKE :name AND a.account_id = acc.account_id ORDER BY acc.email";
        $res = $this->prepare($sql);
        $search = '%';
        for ($i = 0; $i < strlen($name); $i++)
            $search .= $name[$i] . '%';
        // or $search='%' . $name . '%';
        $res->bindParam(":name", $search);
        $res->execute();
        $admins = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $admins[] = array('admin_id' => $row['admin_id'], 'account_id' => $row['account_id'], 'firstName' => $row['firstName'], 'lastName' => $row['lastName'], 'email' => $row['email'], 'address' => $row['address'], 'phone_number' => $row['phone_number']);
        }
        return $admins;
    }
    public function removeAdmin($admin_id)
    {
        try {
            $this->serializeTransaction();
            $sql = "DELETE FROM admin WHERE admin_id = :admin_id";
            $stmt = $this->prepare($sql);
            $stmt->bindParam(':admin_id', $admin_id);
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    // getRatingsByProductId($product_id)
    // getRatingByClPrId($product_id, $client_id)
    // getReviewByClPrId($client_id, $product_id)
    // getRatingsReviewsByProductId($product_id)
    // addRating($rating)
    // updateRating($rating)
    // addReview($review)
    // isRatedProduct($product_id, $client_id)
    // deleteReview($review)
    public function getRatingsByProductId($product_id)
    {
        $sql = "SELECT count(rating_id) AS count FROM rating WHERE product_id = :product_id";
        $res = $this->prepare($sql);
        $res->bindParam(":product_id", $product_id);
        $res->execute();
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $count = $row['count'];
        if ($count == null)
            return 0;
        else
            return $count;
    }
    public function getRatingByClPrId( $client_id,$product_id)
    {
        $sql = "SELECT * FROM rating WHERE product_id = :product_id AND client_id = :client_id";
        $res = $this->prepare($sql);
        $res->bindParam(":product_id", $product_id);
        $res->bindParam(":client_id", $client_id);
        $res->execute();
        $rating = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $rating = new Rating($row['rating_id'], $row['client_id'], $row['product_id'], $row['value']);
        return $rating;
    }
    public function getReviewByClPrId($client_id, $product_id)
    {
        $sql = "SELECT * FROM review WHERE product_id = :product_id AND client_id = :client_id";
        $res = $this->prepare($sql);
        $res->bindParam(":product_id", $product_id);
        $res->bindParam(":client_id", $client_id);
        $res->execute();
        $review = null;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $review = new Review($row['review_id'], $row['client_id'], $row['product_id'], $row['text']);
        return $review;
    }
    public function getRatingsReviewsByProductId($product_id, $sort = 'ASC')
    {
        $sort = strtoupper($sort);
        $sort = ($sort == 'ASC' || $sort == 'DESC') ? $sort : 'ASC';
        $sql = "SELECT c.firstName, c.lastName, ra.value, re.text
                FROM client c
                LEFT JOIN rating ra ON c.client_id = ra.client_id AND ra.product_id = :product_id
                LEFT JOIN review re ON c.client_id = re.client_id AND re.product_id = :product_id
                WHERE c.client_id = ra.client_id or c.client_id = re.client_id ORDER BY ra.value " . $sort;
        $res = $this->prepare($sql);
        $res->bindParam(":product_id", $product_id);
        $res->execute();
        $comments = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $comments[] = $row;
        }
        return $comments;
    }
    public function addRating(Rating $rating)
    {
        try {
            $this->serializeTransaction();
            $sql = "INSERT INTO rating(client_id,product_id,value) VALUES (:client_id,:product_id,:value);
                    UPDATE product SET rating = (SELECT AVG(value) FROM rating WHERE product_id = :product_id) WHERE product_id = :product_id;";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':client_id', $rating->getClientId());
            $stmt->bindValue(':product_id', $rating->getProductId());
            $stmt->bindValue(':value', $rating->getValue());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateRating(Rating $rating)
    {
        try {
            $this->serializeTransaction();
            $sql = "UPDATE rating SET value = :value WHERE rating_id = :rating_id ;
                    UPDATE product SET rating = (SELECT AVG(value) FROM rating WHERE product_id = :product_id) WHERE product_id = :product_id;";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':rating_id', $rating->getRatingId());
            $stmt->bindValue(':product_id', $rating->getProductId());
            $stmt->bindValue(':value', $rating->getValue());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function addReview(Review $review)
    {
        try {
            $this->serializeTransaction();
            $sql = "INSERT INTO review(client_id,product_id,text) VALUES (:client_id,:product_id,:text)";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':client_id', $review->getClientId());
            $stmt->bindValue(':product_id', $review->getProductId());
            $stmt->bindValue(':text', $review->getText());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function updateReview(Review $review)
    {
        try {
            $this->serializeTransaction();
            $sql = "UPDATE review SET text = :text WHERE review_id = :review_id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':review_id', $review->getReviewId());
            $stmt->bindValue(':text', $review->getText());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    public function isRatedProduct($product_id, $client_id)
    {
        $sql = "SELECT EXISTS(SELECT 1 FROM rating WHERE product_id = :product_id AND client_id = :client_id) AS is_rated";
        $res = $this->prepare($sql);
        $res->bindParam(":product_id", $product_id);
        $res->bindParam(":client_id", $client_id);
        $res->execute();
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row['is_rated'];// 1 if exists, 0 otherwise
    }
    public function deleteReview(Review $review)
    {
        try {
            $this->serializeTransaction();
            $sql = "DELETE FROM review WHERE review_id = :review_id";
            $stmt = $this->prepare($sql);
            $stmt->bindValue(':review_id', $review->getReviewId());
            $stmt->execute();
            $stmt->closeCursor();
            $done = $this->commit();
        } catch (PDOException $e) {
            $this->rollBack();
            die("Connection failed: " . $e->getMessage());
        }
        return $done;
    }
    // getPaymentInfoByClientId($client_id)
    public function getPaymentInfoByClientId($client_id)
    {
        $sql = "SELECT * FROM paymentinfo WHERE client_id=:client_id";
        $res = $this->prepare($sql);
        $res->bindParam(":client_id", $client_id);
        $res->execute();
        $paymentInfo = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {//client_id, cardNumber, nameOnCard, expiryDate, securityCode
            $paymentInfo[] = new PaymentInfo($row['paymentInfo_id'], $row['client_id'], $row['cardNumber'], $row['nameOnCard'], $row['expiryDate'], $row['securityCode']);
        }
        return $paymentInfo;
    }

    public function getPaymentInfoByPaymentId($paymentInfo_id){
        $sql = "SELECT * FROM paymentinfo WHERE paymentInfo_id=:paymentInfo_id";
        $res = $this->prepare($sql);
        $res->bindParam(":paymentInfo_id", $paymentInfo_id);
        $res->execute();
        $paymentInfo = null;
        if($res->rowCount()==1){
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $paymentInfo = new PaymentInfo($row['paymentInfo_id'], $row['client_id'], $row['cardNumber'], $row['nameOnCard'], $row['expiryDate'], $row['securityCode']);
        
    }
        return $paymentInfo;
    }

    public function isUniquePhoneNumber($phoneNumber){//modified
        $sql = "SELECT EXISTS(SELECT 1 FROM admin WHERE phone_number = :phone_number) AS phoneNb_exists";
        $res = $this->prepare($sql);
        $res->bindParam(":phone_number", $phoneNumber);
        $res->execute();
        $row = $res->fetch(PDO::FETCH_ASSOC);
        return $row['phoneNb_exists'];
    }


    public function getTopDeals() {
        $sql = "SELECT p.* FROM product p JOIN (SELECT product_id, COUNT(product_id) AS count FROM orderitem GROUP BY product_id ) o ON p.product_id = o.product_id WHERE p.stock > 0 ORDER BY o.count DESC";
        $res = $this->query($sql);
        $products = null;
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $products[] = new Product($row['product_id'], $row['name'], $row['category_id'], $row['description'], $row['stock'], $row['price'], $row['discount'], $row['rating'], $row['nb_images']);
        }
        return $products;
    }


    public function getNbCartItems($cart_id){
        $sql = "SELECT SUM(quantity) as nbCartItems FROM cartitem WHERE cart_id = :cart_id";
        $res = $this->prepare($sql);
        $res->bindParam(":cart_id", $cart_id);
        $res->execute();
        $nbCartItems = 0;
        if ($row = $res->fetch(PDO::FETCH_ASSOC))
            $nbCartItems = $row['nbCartItems'];
        return $nbCartItems;
    }
    /*------------------------------------------------------------------------------------------------------------------
   -------------------------------Functions Added (Omar)----------------------------------------------------*/

   public function authenticate($email, $password)
   {
       //returns the account id if succeeded and returns -1 in case of failure
       $sql = "SELECT * FROM account WHERE email=:email AND password=:password";
       $res = $this->prepare($sql);
       $res->bindParam(":email", $email);
       $hashed_passwd = hash('sha256', $password);
       $res->bindParam(":password", $hashed_passwd);
       $res->execute();
       if ($res->rowCount() == 1) {
           return ($res->fetch(PDO::FETCH_ASSOC))['account_id'];
       } else
           return -1;
   }

   public function getAccountById($id)
   {
       //returns the account object if found and returns null otherwise
       $sql = "SELECT * FROM account WHERE account_id=:id";
       $res = $this->prepare($sql);
       $res->bindParam(":id", $id);
       $res->execute();

       if ($res->rowCount() == 1) {
           $acc = $res->fetch(PDO::FETCH_ASSOC);
           return new Account($acc['account_id'], $acc['email'], $acc['password'], $acc['isAdmin'], $acc['token']);
       } else
           return null;
   }

   public function updateAccount($account)
   {
       //the password in the account object must be hashed!!!!
       //returns true in case of success and false otherwise
       try {
           $this->serializeTransaction();
           $sql = "UPDATE account SET email=:email, password=:password, isAdmin=:isAdmin, token=:token WHERE account_id=:id";
           $res = $this->prepare($sql);
           $email = $account->getEmail();
           $res->bindParam(":email", $email);
           $password = $account->getPassword();
           //if the value of the password in the function is hashed, keep it the same
           // otherwise, hash it
           // $password=($isHashedPasswd==true)?$password:hash('sha256',$password);
           $res->bindParam(":password", $password);
           $isAdmin = $account->getIsAdmin();
           $res->bindParam(":isAdmin", $isAdmin);
           $token = $account->getToken();
           $res->bindParam(":token", $token);
           $id = $account->getAccountId();
           $res->bindParam(":id", $id);
           $res->execute();
           $res->closeCursor();
           $done = $this->commit();
       } catch (PDOException $e) {
           $this->rollBack();
           die("Connection failed: " . $e->getMessage());
       }
       return $done;
   }

   public function accountExist($email)
   {
       //returns the account if found and returns null otherwise
       $sql = "SELECT * FROM account WHERE email=:email";
       $res = $this->prepare($sql);
       $res->bindParam(":email", $email);
       $res->execute();
       if ($res->rowCount() == 1) {
           $row = $res->fetch(PDO::FETCH_ASSOC);
           return new Account($row['account_id'], $row['email'], $row['password'], $row['isAdmin'], $row['token']);
       } else
           return null;
   }

   public function serializeTransaction()
   {
       //returns true if succeeded and false otherwise
       $this->exec("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE");
       return $this->beginTransaction();
   }



   /*---------------------------------------------------4/4/2024 (OMAR)-------------------------------------------------- */


public function addAccount($acc){

   //returns the account id (or -1 in case of error)
   try{

       $this->serializeTransaction();
       $sql="INSERT INTO account(email, password, isAdmin, token) VALUES(:email, :password, :isAdmin, :token)";
       $res=$this->prepare($sql);
       $res->bindValue(":email",$acc->getEmail());
       //here the password should be not hashed in the account object, it only should be hashed in the  database.
       //this is because we will need the plain password to use it in the authenticate function which will later
       //hash it.
       $hashed_passwd=hash("sha256",$acc->getPassword());
       $res->bindValue(":password",$hashed_passwd);
       $res->bindValue(":isAdmin",$acc->getIsAdmin());
       $res->bindValue(":token",$acc->getToken());
       $res->execute();
       $res->closeCursor();
       $this->commit();
   }
   catch(PDOException $e){
       $this->rollBack();
       die("Connection Failed: ". $e->getMessage());
   }

   return $this->authenticate($acc->getEmail(),$acc->getPassword());//here the password is not hashed !
}

public function addClient($client,$acc_id){
//returns the client id on success and -1 on failure
   $done=false;
   try{

       $this->serializeTransaction();
       $sql="INSERT INTO client(account_id,firstName,lastName,phone_number,cart_id,paymentInfo_id,points) 
       VALUES(:account_id, :firstName, :lastName, :phone_number, :cart_id, :paymentInfo_id, :points)";
       $res=$this->prepare($sql);
       $res->bindValue(":account_id",$acc_id);
       $res->bindValue(":firstName",$client->getFirstName());
       $res->bindValue(":lastName",$client->getLastName());
       $res->bindValue(":phone_number",$client->getPhoneNumber());
       $res->bindValue(":cart_id",$client->getCartId());
       $res->bindValue(":paymentInfo_id",$client->getPaymentInfoId());
       $res->bindValue(":points",$client->getPoints());
       $res->execute();
       $res->closeCursor();
       $done=$this->commit();
   }
   catch(PDOException $e){
       $this->rollBack();
       die("Connection Failed: ". $e->getMessage());
   }

   if($done==true){

       return ($this->getClientByAccountId($acc_id))->getClientId();
   }

   else return -1;
}

public function getClientByAccountId($acc_id){

   //returns the client object or null

   $sql="SELECT * FROM client WHERE account_id=:acc_id";
   $res=$this->prepare($sql);
   $res->bindValue(":acc_id",$acc_id);
   $res->execute();

   if($res->rowCount()==1){

       $client=$res->fetch(PDO::FETCH_ASSOC);
       return new Client($client['client_id'],$client['account_id'],$client['firstName'],
       $client['lastName'],$client['phone_number'],$client['cart_id'],$client['paymentInfo_id'],$client['points']);
   }

   else return null;

}

public function addCart($client_id){

   $done=false;

   try{

       $this->serializeTransaction();
       $sql="INSERT INTO cart(client_id) VALUES(:client_id)";
       $res=$this->prepare($sql);
       $res->bindValue(":client_id",$client_id);
       $res->execute();
       $res->closeCursor();
       $done=$this->commit();
   }
   catch(PDOException  $e){
       $this->rollBack();
       die("Connection Failed: ". $e->getMessage());
   }

   if($done==true){

       $client=$this->getClientByClientId($client_id);
       $sql="SELECT * FROM cart WHERE client_id=:client_id";
       $res=$this->prepare($sql);
       $res->bindValue(":client_id",$client_id);
       $res->execute();
       if($res->rowCount()==1){
           $row=$res->fetch(PDO::FETCH_ASSOC);
           $client->setCartId($row['cart_id']);
           $this->updateClient($client);

       }
       else $done=false;
   }
   return $done;
}

public function updateClient($client){

   $done=false;
   try{

       $this->serializeTransaction();
       $sql="UPDATE client SET account_id=:account_id, firstName=:firstName, lastName=:lastName,
        phone_number=:phone_number, cart_id=:cart_id, paymentInfo_id=:paymentInfo_id, points=:points
         WHERE client_id=:client_id";
         $res=$this->prepare($sql);
         $res->bindValue(":account_id",$client->getAccountId());
         $res->bindValue(":firstName",$client->getFirstName());
         $res->bindValue(":lastName",$client->getLastName());
         $res->bindValue(":phone_number",$client->getPhoneNumber());
         $res->bindValue(":cart_id",$client->getCartId());
         $res->bindValue(":paymentInfo_id",$client->getPaymentInfoId());
         $res->bindValue(":points",$client->getPoints());
         $res->bindValue(":client_id",$client->getClientId());
         $res->execute();
         $res->closeCursor();
         $done=$this->commit();
   }
   catch(PDOException $e){
       $this->rollBack();
       die("Connection Failed : ". $e->getMessage());
   }
   return $done;
}

public function addPaymentInfo($paymentInfo,$client_id){
//returns the id of the inserted payment info
   $done=false;
   $id=-1;
   try{

       $this->serializeTransaction();
       $sql="INSERT INTO paymentinfo(client_id, cardNumber, nameOnCard, expiryDate, securityCode)
        VALUES(:client_id, :cardNumber, :nameOnCard, :expiryDate, :securityCode)";
        $res=$this->prepare($sql);
        $res->bindValue(":client_id",$client_id);
        $res->bindValue(":cardNumber",$paymentInfo->getCardNumber());
        $res->bindValue(":nameOnCard",$paymentInfo->getNameOnCard());
        $res->bindValue(":expiryDate",$paymentInfo->getExpiryDate());
        $res->bindValue(":securityCode",$paymentInfo->getSecurityCode());//we can ecnrypt it for security reasons later
        $res->execute();
        $id=$this->lastInsertId();//added
        $res->closeCursor();
        $done=$this->commit();
        

   }
   catch(PDOException $e){
       $this->rollBack();
       die("Connection Failed : ". $e->getMessage());
   }
   if($done==true)
   return $id;
else return -1;
}

   //if($done==true){
  //  return $this->lastInsertId();
   //}
   //else return -1;
//}

//        try{
//            $this->serializeTransaction();
//            $client=$this->getClientByClientId($client_id);
//            $sql2="SELECT * FROM paymentinfo WHERE client_id=:client_id ORDER BY paymentInfo_id DESC";
//            //1st result will be the latest payment info for this client => save it in payment_info_id
//            $res2=$this->prepare($sql2);
//            $res2->bindValue(":client_id",$client_id);
//            $res2->execute();
//           // $res2->closeCursor();
//            $row=$res2->fetch(PDO::FETCH_ASSOC);
//            $client->setPaymentInfoId($row['paymentInfo_id']);
//            $res2->closeCursor();
//            $done=$this->commit();
//        }
//        catch(PDOException $e){
//             $this->rollBack();
//             die("Connection Failed : ". $e->getMessage()); 
//        }
//    }
//    return ($done && $this->updateClient($client));
// }


// public function addPaymentInfo($paymentInfo, $clientId) {
//     $success = false;
//     try {
//         // Serialize transaction
//         $this->serializeTransaction();

//         // Begin transaction
//         //$this->beginTransaction();

//         // Insert payment info
//         $sql = "INSERT INTO paymentinfo(client_id, cardNumber, nameOnCard, expiryDate, securityCode)
//                 VALUES(:client_id, :cardNumber, :nameOnCard, :expiryDate, :securityCode)";
//         $res = $this->prepare($sql);
//         $res->bindValue(":client_id", $clientId);
//         $res->bindValue(":cardNumber", $paymentInfo->getCardNumber());
//         $res->bindValue(":nameOnCard", $paymentInfo->getNameOnCard());
//         $res->bindValue(":expiryDate", $paymentInfo->getExpiryDate());
//         $res->bindValue(":securityCode", $paymentInfo->getSecurityCode());
//         $res->execute();
//         $res->closeCursor();

//         // Update client's paymentInfoId
//         $client = $this->getClientByClientId($clientId);
//         $sql2 = "SELECT * FROM paymentinfo WHERE client_id=:client_id ORDER BY paymentInfo_id DESC";
//         $res2 = $this->prepare($sql2);
//         $res2->bindValue(":client_id", $clientId);
//         $res2->execute();
//         $row = $res2->fetch(PDO::FETCH_ASSOC);
//         $client->setPaymentInfoId($row['paymentInfo_id']);
//         $res2->closeCursor();

//         // Update the client
//         //$success = $this->updateClient($client);
//         $success=$this->commit();

//         // Commit transaction
//         if ($success) {
//             $success = $this->updateClient($client);
//             //$this->commit();
//         } else {
//           // $this->rollBack();
//         }
//     } catch (PDOException $e) {
//         $this->rollBack();
//         error_log("Database Error: " . $e->getMessage());
//     }
//     return $success;
// }



public function addShipmentInfo($shipmentInfo,$client_id){

   $done=false;
   try{

       $this->serializeTransaction();
       $sql="INSERT INTO shipmentinfo(country_name, client_id, fullName, street_nb, building, city, state,
        zipCode, phoneNumber) VALUES(:country_name, :client_id, :fullName, :street_nb, :building, :city, :state,
         :zipCode, :phoneNumber)";
         $res=$this->prepare($sql);
         $res->bindValue(":country_name",$shipmentInfo->getCountryName());
        //  $res->bindValue(":client_id",$shipmentInfo->getClientId());
        $res->bindValue(":client_id",$client_id);
         $res->bindValue(":fullName", $shipmentInfo->getFullName());
         $res->bindValue(":street_nb",$shipmentInfo->getStreetNb());
         $res->bindValue(":building",$shipmentInfo->getBuilding());
         $res->bindValue(":city",$shipmentInfo->getCity());
         $res->bindValue(":state",$shipmentInfo->getState());
         $res->bindValue(":zipCode",$shipmentInfo->getZipCode());
         $res->bindValue(":phoneNumber",$shipmentInfo->getPhoneNumber());
         $res->execute();
         $res->closeCursor();
         $done=$this->commit();
   }
   catch(PDOException $e){
       $this->rollBack();
       die("Connection Failed: ". $e->getMessage());
   }

//    if($done==true){

//        try{

//            $this->serializeTransaction();
//            $client=$this->getClientByClientId($client_id);
//            $sql="SELECT * FROM shipmentinfo WHERE client_id=:client_id ORDER BY shipmentInfo_id DESC";
//            $res=$this->prepare($sql);
//            $res->bindValue(":client_id",$client_id);
//            $res->execute();
//            $res->closeCursor();
//            $row=$res->fetch(PDO::FETCH_ASSOC);
//            $s=$client->getShipmentInfo();
//            $s->setShipmentInfoId($row['shipmentInfo_id']);
//            $client->setShipmentInfo($s);
//            $done=$this->commit();

//        }
//        catch(PDOException $e){
//            $this->rollBack();
//            die("Connection Failed: ".$e->getMessage());
//        }
//    }

//    return ($done && $this->updateClient($client));
    return $done;

}

//probably will not be used:
public function getMaxClientId(){

   $sql="SET information_schema_stats_expiry=0";
   $this->query($sql);
   $sql="SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='projectv4' AND TABLE_NAME='client'";
   $res=$this->query($sql);
   $row=$res->fetch(PDO::FETCH_ASSOC);
   $max=$row['AUTO_INCREMENT'];
   return $max;
}


/*--------------------------------------5/4/2024---------------------------------------------------------------- */

public function addAdmin($admin,$acc_id){
//returns the admin id or -1
   $done=false;
   try{

       $this->serializeTransaction();
       $sql="INSERT INTO admin(account_id, firstName, lastName, address, phone_number) 
       VALUES(:account_id, :firstName, :lastName, :address, :phone_number)";
       $res=$this->prepare($sql);
       $res->bindValue(":account_id",$acc_id);
       $res->bindValue(":firstName", $admin->getFirstName());
       $res->bindValue(":lastName", $admin->getLastName());
       $res->bindValue(":address", $admin->getAddress());
       $res->bindValue(":phone_number", $admin->getPhoneNumber());
       $res->execute();
       $res->closeCursor();
       $done=$this->commit();
   }
   catch(PDOException $e){
       $this->rollBack();
       die("Connection Failed: ". $e->getMessage());
   }

   if($done==true){
       //return ($this->getAdminByAccountId($acc_id))['admin_id'];
       foreach($this->getAdminByAccountId($acc_id) as $key=>$value)
       return $value->getAdminId();
   }

   else return -1;


}

/**-------------------------------------------14/4/2024-------------------------------------------------- */

public function getAdminByToken($token){

    //returns the admin object or null
    $sql="SELECT a.* FROM admin a, account acc WHERE a.account_id=acc.account_id AND acc.token=:token";
    $res=$this->prepare($sql);
    $res->bindValue(":token",$token);
    $res->execute();
    if($res->rowCount()==1){

        $row=$res->fetch(PDO::FETCH_ASSOC);
        return new Admin($row['admin_id'],$row['account_id'],$row['firstName'],$row['lastName'],
        $row['address'],$row['phone_number']);
    }

    else return null;

}


/*----------------------------------------------------------21/4/2024---------------------------------------------- */

public function deletePaymentInfo($id){
    //delete it completely from the db.
    $done=false;
    try{
        $this->serializeTransaction();
        $sql="DELETE FROM paymentinfo WHERE paymentInfo_id=:id";
        $res=$this->prepare($sql);
        $res->bindValue(":id",$id);
        $res->execute();
        $res->closeCursor();
        $done=$this->commit();

    }catch(PDOException $e){
        $this->rollBack();
        die("Connection Failed: ". $e->getMessage());
    }

    return $done;
}

public function deleteShipmentInfo($id){
    //only set the client Id to null and keep it for order management and stats.
    $done=false;
    try{
        $this->serializeTransaction();
        $sql="UPDATE shipmentinfo SET client_id=null WHERE shipmentInfo_id=:id";
        $res=$this->prepare($sql);
        $res->bindValue(":id",$id);
        $res->execute();
        $res->closeCursor();
        $done=$this->commit();
    } 
    catch (PDOException $e){

        $this->rollBack();
        die("Connection Failed : ". $e->getMessage());
    }

    return $done;
}


public function updateAdmin($admin){

    $done=false;
    try{
        $this->serializeTransaction();
        $sql="UPDATE admin SET firstName=:firstName, lastName=:lastName, address=:address, phone_number=:phone_number WHERE admin_id=:admin_id";
        $res=$this->prepare($sql);
        $res->bindValue(":firstName",$admin->getFirstName());
        $res->bindValue(":lastName",$admin->getLastName());
        $res->bindValue(":address",$admin->getAddress());
        $res->bindValue(":phone_number",$admin->getPhoneNumber());
        $res->bindValue(":admin_id",$admin->getAdminId());
        $res->execute();
        $res->closeCursor();
        $done=$this->commit();
    }
    catch(PDOException $e){
        $this->rollBack();
        die("Connection Failed: ". $e->getMessage());
    }
    return $done;
}


public function getAccountByToken($token){
    //returns the object or null
    $acc=null;
    $sql="SELECT * FROM  account WHERE token=:token";
    $res=$this->prepare($sql);
    $res->bindValue(":token",$token);
    $res->execute();
    if($res->rowCount()==1){
        $row=$res->fetch(PDO::FETCH_ASSOC);
        $acc=new Account($row['account_id'],$row['email'],$row['password'],$row['isAdmin'],$row['token']);
    }

    return $acc;


}


public function deleteAccount($id){

    $done=false;
    try{
        $this->serializeTransaction();
        $sql="DELETE FROM account WHERE account_id=:id";
        $res=$this->prepare($sql);
        $res->bindValue(":id",$id);
        $res->execute();
        $res->closeCursor();
        $done=$this->commit();
    }
    catch(PDOException $e){
        $this->rollback();
        die("Connection Failed: ".$e->getMessage());
    }

    return $done;

}


/*------------------------------------------------------------------------------------------------------------ */

public function getOrderItemsByOrderId($orderId){

    $orderItems=null;
    $sql="SELECT * FROM orderitem WHERE order_id=:order_id";
    $res=$this->prepare($sql);
    $res->bindValue(":order_id",$orderId);
    $res->execute();
    while($row=$res->fetch(PDO::FETCH_ASSOC)){
        $orderItems[]=new OrderItem($row['item_id'],$row['order_id'],$row['product_id'],$row['quantity']);
    }

    return $orderItems;
}


/*-------------------------------------------------------------------------------------------------------------- */

public function addInvoice($invoiceText,$order_id){


        $done=false;
        try{

            $this->serializeTransaction();
            $sql="INSERT INTO invoice(order_id,text) VALUES(:order_id,:text)";
            $res=$this->prepare($sql);
            $res->bindValue(":order_id",$order_id);
            $res->bindValue(":text",$invoiceText);
            $res->execute();
            $res->closeCursor();
            $done=$this->commit();
        }
        catch(PDOException $e){

            $this->rollBack();
            die("Connection Failed :".$e->getMessage());
        }

        return $done;
}

public function getInvoiceByOrderId($order_id){
    
    $invoice=null;
    $sql="SELECT * FROM invoice WHERE order_id=:order_id";
    $res=$this->prepare($sql);
    $res->bindValue(":order_id",$order_id);
    $res->execute();
    while($row=$res->fetch(PDO::FETCH_ASSOC)){
        $invoice=$row['text'];
    }

    return $invoice;
}

}