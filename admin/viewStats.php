<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once ($path . "/connection.php");
session_start();
//$back = "<button class='search-button'><a href='admin.php'>Back</a></button>";
$back = "<a href='admin.php' class='back-button'><button>Back</button></a>";
if (isset($_COOKIE['isOwner']) && $_COOKIE['isOwner'] == "true")
$back = "<a href='owner.php' class='back-button'><button>Back</button></a>";    
//$back = "<button class='search-button'><a href='owner.php'>Back</a></button>";
include_once("../check_login.php");
if($_SESSION['signed_in']==true){
if (
    (!isset($_COOKIE['isOwner']) || $_COOKIE['isOwner'] == "false") &&
    (!isset($_COOKIE['isAdmin']) || $_COOKIE['isAdmin'] == "false")
)
    header("location:../index.php");
}
else header("Location:../index.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/x-icon" href="../logos/primary_icon.jpeg" /> <!-- modified -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> <!--modified -->
    <script src="../package/dist/chart.min.js"></script>
    <title>Stats</title>
    <!--<script src="path/to/chartjs/dist/chart.umd.js">import Chart from 'chart.js/auto';</script>-->
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-direction: row;
            margin-top: 5px;
        }

        .buttonContainer {
            display: flex;
            flex-direction: column;
            margin-right: 20px;
            padding: 20px;
        }

        .statsContainer {
            /*display: flex;
            flex-direction: column;*/
            width: 25%;
            padding: 20px;
            /*border: 1px solid #ccc;*/
            text-align: center;
            transition: background-color 0.3s ease;
        }

        #myChart {
            width: 400px;
            height: 300px;
        }

        canvas {
            width: 100%;
            /* Make canvas elements fill their parent container */
            height: auto;
        }
    </style>
</head>
 <!-- -------------------------------------------------------------------------------------------------------------------------- -->
 <header>
        <nav class="nav-container">
            <div class='left-nav'>
                <div>
                    <a href="../index.php"><img src="../logos/primary_logo.png" alt="logo" width='220rem'
                            height='90rem'></a>
                </div>
            </div>

            <?php
            include_once ("../check_login.php");
            // if ($isAdmin == true)
            //     header("Location:admin.php");
            if ($isClient == true)
                header("Location:../index.php");

            if ($_SESSION['signed_in'] == true /*&& $isClient==true*/) {
                if ($isClient == true) {
                    $client = $dbHelper->getClientByToken($_COOKIE['token']);
                    if($client==null){
                        header("Location:../sign_out.php?isClient=1");
                    }
                    echo '<div><li>Hello ' . $client->getFirstName() . '<br>Points:  ' . $client->getPoints() . '</li>';
                    echo '<li><a href="../manageAccount.php?user=client">Account</a>/<a href="../sign_out.php?isClient=1">Sign Out</a></li>';
                    echo '<li><a href="#about">About</a></li>';
                    echo '<li><a href="../cart.php">Cart</a></li></div>';
                } else if ($isAdmin == true) {
                    $admin = $dbHelper->getAdminByToken($_COOKIE['token']);
                    if($admin==null){
                        header("Location:../sign_out.php?isAdmin=1");
                    }
                    echo '<div class="center-nav"><div><h1>Stat Panel</h1></div></div>
                    <div class="right-nav"><div><h3>Admin : ' . $admin->getFirstName() . ' </h3></div>';
                    // echo '<div><a href="../manageAccount.php?user=admin"><i class="bi bi-person-fill-gear"></i></a></div>
                    // <div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i></a></div></div>';
                    //echo '<p><a href="#about">About</a></p></div>';
                    // Account
                    // Sign Out
                    
                    echo '<div class="account-select">
                    <i class="bi bi-person-fill-gear"></i>
                    <select id="accountSelect" onchange="goToPage(this.value)">
                        <option disabled selected>--choose--</option>
                        <option value="../manageAccount.php?forward=updateProfile">Update Profile</option>
                        <option value="../manageAccount.php?forward=changePassword">Change Password</option>
                        <option value="delete">Delete Account</option>
                    </select></div>';
                    echo '<div><a href="../sign_out.php?isAdmin=1"><i class="bi bi-box-arrow-right"></i> </a></div></div>';
                } else if ($isOwner == true) {
                    echo '<div class="center-nav"><div><h1>Stat Panel</h1></div></div>
                        <div class="right-nav"><div><h3>Owner : ' . OWNER_NAME . ' </h3></div>';
                    echo '<div><p><a href="../sign_out.php?isOwner=1">
                    <i class="bi bi-box-arrow-right"></i> Sign Out</a></p></div></div>';
                }
            } else {
                echo '<p><a href="../sign_in.php">Sign in</a>/<a href="../createAccount.php">Create account</a></p>';
            }
            ?>
            <!-- <li><a href="#about">About</a></li>
                <li><a href="cart.php">Cart</a></li> -->
            </div>
        </nav>
    </header>
    <!-- -------------------------------------------------------------------------------------------------------------------------- -->
<script>
    function getChartData(dataType) {
        console.log(dataType);
        switch (dataType) {
            case "categories":
                <?php
                $data = $dbHelper->getAllCategories("nbProducts");
                $categories = array();
                $categoryData = null;
                if ($data == null)
                    $categories = null;
                else {
                    foreach ($data as $category) {
                        $categoryData = array(
                            'category_id' => $category->getCategoryId(),
                            'name' => $category->getName(),
                            'nbProducts' => $category->getNbProducts()
                        );
                        $categories[] = $categoryData;
                    }
                    $categoryData = json_encode($categories);
                }
                ?>
                console.log(<?php echo $categoryData; ?>);
                return <?php echo $categoryData; ?>

            case "products":
                <?php
                $prods = $dbHelper->getAllProducts('product_id');
                if ($prods != null)
                    $count = count($prods);
                $data = $dbHelper->getMostOrderedProducts();
                $prodData = null;
                if ($data != null) {
                    $products = array();
                    foreach ($data as $item) {
                        $prod = $item[0];
                        $count = $item[1];
                        $prodData = array(
                            'product_id' => $prod->getProductId(),
                            'name' => $prod->getName(),
                            'count' => $count
                        );
                        $products[] = $prodData;
                    }
                    $prodData = json_encode($products);
                }
                ?>
                return <?php echo $prodData; ?>

            case "conversionRate":
                <?php
                $active = $total = null;
                $active = $dbHelper->getActiveClientsNumber();
                $total = $dbHelper->getTotalClientsNumber();
                $userData = null;
                if ($total != null && $active != null) {
                    $data = array(
                        'active' => $active,
                        'total' => $total
                    );
                    $userData = json_encode($data);
                }
                ?>
                return <?php echo $userData; ?>

            case "day":
                <?php
                $endDate = new DateTime();
                $endDate->sub(new DateInterval('P6D'));
                $sDate = $endDate->format('Y-m-d');
                $startDate = DateTime::createFromFormat('Y-m-d', $sDate);
                $data = $dbHelper->getNbOrdersWithDate('day', $startDate);
                $userData = null;
                if ($data != null) {
                    $userData = json_encode($data);
                }
                ?>
                return <?php echo $userData; ?>

            case "week":
                <?php
                $endDate = new DateTime();
                $endDate->sub(new DateInterval('P28D'));
                $sDate = $endDate->format('Y-m-d');
                $startDate = DateTime::createFromFormat('Y-m-d', $sDate);
                $data = $dbHelper->getNbOrdersWithDate('week', $startDate);
                $userData = null;
                if ($data != null) {
                    $userData = json_encode($data);
                }
                ?>
                return <?php echo $userData; ?>
        }
    }

    function toggleButtonColor(button) {

        if (!button || !button.parentElement) {
            console.error('Invalid button or parent element.');
            return;
        }
        const buttons = button.parentElement.querySelectorAll('.i-button-stat');

        // Reset all buttons in the same div
        buttons.forEach(btn => {
            if (btn !== button) {
                btn.classList.remove('active');
            }
        });

        // Toggle the clicked button's color
        button.classList.toggle('active');
    }

    function toggleButtonColorLine(button) {
        if (!button || !button.parentElement) {
            console.error('Invalid button or parent element.');
            return;
        }
        const buttons = button.parentElement.querySelectorAll('.i-button-stat-line');

        // Reset all buttons in the same div
        buttons.forEach(btn => {
            if (btn !== button) {
                btn.classList.remove('active');
            }
        });

        // Toggle the clicked button's color
        button.classList.toggle('active');
    }

    function createButtons(dataType) {
        if (dataType == "categories") {
            var buttonArea1 = document.getElementById('buttonAreaCategory');

            var button1 = document.createElement('button');
            button1.id = 'cat_b_1';
            button1.textContent = 'Bar Chart';
            button1.onclick = function () { displayStatsCategory('bar'); };
            button1.classList.add('i-button-stat');
            buttonArea1.appendChild(button1);

            var button2 = document.createElement('button');
            button2.id = 'cat_b_2';
            button2.textContent = 'Pie Chart';
            button2.onclick = function () { displayStatsCategory('pie'); };
            button2.classList.add('i-button-stat');
            buttonArea1.appendChild(button2);
        }
        else if (dataType == "products") {
            var buttonArea1 = document.getElementById('buttonAreaProduct');

            var button1 = document.createElement('button');
            button1.textContent = 'Bar Chart';
            button1.id = 'prod_b_1';
            button1.onclick = function () { displayStatsProduct('bar'); };
            button1.classList.add('i-button-stat');
            buttonArea1.appendChild(button1);

            var button2 = document.createElement('button');
            button2.textContent = 'Pie Chart';
            button2.id = 'prod_b_2';
            button2.onclick = function () { displayStatsProduct('pie'); };
            button2.classList.add('i-button-stat');
            buttonArea1.appendChild(button2);
        }
        else if (dataType == "users") {
            var buttonArea1 = document.getElementById('buttonAreaUser');

            var button2 = document.createElement('button');
            button2.textContent = 'Line Chart';
            button2.id = 'user_b_2';
            button2.onclick = function () { displayStatsUser('line', 'day'); };
            button2.classList.add('i-button-stat');
            buttonArea1.appendChild(button2);

            var button1 = document.createElement('button');
            button1.textContent = 'Conversion Rate';
            button1.id = 'user_b_1';
            button1.onclick = function () { displayStatsUser('doughnut', 'week'); };
            button1.classList.add('i-button-stat');
            buttonArea1.appendChild(button1);
        }
    }

    function createButtonsLineChart() {
        var buttonArea1 = document.getElementById('buttonAreaUserLineChart');

        var button1 = document.createElement('button');
        button1.textContent = 'Last Week';
        button1.id = 'user_line_b_1';
        button1.onclick = function () { displayStatsUser('line', 'day'); };
        button1.classList.add('i-button-stat-line');
        buttonArea1.appendChild(button1);

        var button2 = document.createElement('button');
        button2.textContent = 'Last Month';
        button2.id = 'user_line_b_2';
        button2.onclick = function () { displayStatsUser('line', 'week'); };
        button2.classList.add('i-button-stat-line');
        buttonArea1.appendChild(button2);
    }

    function displayStatsCategory(chartType) {
        var buttonArea = document.getElementById('buttonAreaCategory');
        if (buttonArea) buttonArea.innerHTML = '';


        //toggleButtonColor(cat_b_1);

        var jsonData = getChartData("categories");
        if (jsonData != null) {

            createButtons("categories");
            var cat_b_1 = document.getElementById('cat_b_1');
            var cat_b_2 = document.getElementById('cat_b_2');
            jsonData.sort((a, b) => b.nbProducts - a.nbProducts); //sorting

            //get first 5 and all others to others
            var labels = [];
            var values = [];
            var otherProductsCount = 0;

            for (var i = 0; i < jsonData.length; i++) {
                if (i < 5) {
                    labels.push(jsonData[i].name);
                    values.push(jsonData[i].nbProducts);
                } else {
                    otherProductsCount += jsonData[i].nbProducts;
                }
            }

            if (jsonData.length > 5) {
                labels.push('Others');
                values.push(otherProductsCount);
            }

            if (chartType == "bar") {
                var ctx = document.getElementById('myChartCategory').getContext('2d');
                //clear each time
                if (window.myChartCategory instanceof Chart) {
                    window.myChartCategory.destroy();
                }

                toggleButtonColor(cat_b_1);

                window.myChartCategory = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Products',
                            data: values,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true //begins values at 0 value
                            }
                        }
                    }
                });
                //toggleButtonColor(cat_b_1);
            } else if (chartType == "pie") {

                var ctx = document.getElementById('myChartCategory').getContext('2d');
                if (window.myChartCategory instanceof Chart) {
                    window.myChartCategory.destroy();
                }
                toggleButtonColor(cat_b_2);
                window.myChartCategory = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Products',
                            data: values,
                            backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(128, 0, 32)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)'
                            ],
                            hoverOffset: 4
                        }]
                    }
                });
                //toggleButtonColor(cat_b_2);
            }
            //createButtons("categories");
        } else {
            //no data 
            var doc = document.getElementById('errorCategory');
            var p = document.createElement("p");
            p.textContent = "No Data to Display!!!";
            doc.appendChild(p);
        }
    }

    function displayStatsProduct(chartType) {
        var buttonArea = document.getElementById('buttonAreaProduct');
        if (buttonArea) buttonArea.innerHTML = '';


        //toggleButtonColor(prod_b_2);

        var jsonData = getChartData("products");
        if (jsonData != null) {

            createButtons("products");
            var prod_b_1 = document.getElementById('prod_b_1');
            var prod_b_2 = document.getElementById('prod_b_2');
            var labels = [];
            var values = [];
            var otherCount = 0;
            for (var i = 0; i < jsonData.length; i++) {
                if (i < 5) {
                    labels.push((jsonData[i].name).substring(0,10));
                    values.push(jsonData[i].count);
                } else {
                    otherCount += jsonData[i].count;
                }
            }

            if (jsonData.length > 5) {
                labels.push('Others');
                values.push(otherCount);
            }

            if (chartType == "bar") {
                var ctx = document.getElementById('myChartProduct').getContext('2d');
                if (window.myChartProduct instanceof Chart) {
                    window.myChartProduct.destroy();
                }

                toggleButtonColor(prod_b_1);

                window.myChartProduct = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Orders per Products',
                            data: values,
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else if (chartType == "pie") {
                var ctx = document.getElementById('myChartProduct').getContext('2d');
                if (window.myChartProduct instanceof Chart) {
                    window.myChartProduct.destroy();
                }

                toggleButtonColor(prod_b_2);

                window.myChartProduct = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Number of Orders per Products',
                            data: values,
                            backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)',
                                'rgb(128, 0, 32)',
                                'rgb(255, 205, 86)',
                                'rgb(75, 192, 192)',
                                'rgb(153, 102, 255)'
                            ],
                            hoverOffset: 4
                        }]
                    }
                });
            }
            //createButtons("products");
        } else {
            // no data
            var doc = document.getElementById('errorProduct');
            var p = document.createElement("p");
            p.textContent = "No Data to Display!!!";
            doc.appendChild(p);
        }
    }

    function displayStatsUser(chartType, time) {
        var buttonArea = document.getElementById('buttonAreaUser');
        if (buttonArea) buttonArea.innerHTML = '';

        var buttonAreaUserLineChart = document.getElementById('buttonAreaUserLineChart');
        if (buttonAreaUserLineChart) buttonAreaUserLineChart.innerHTML = '';

        if (window.myChartUser instanceof Chart) {
            window.myChartUser.destroy();
        }

        createButtons("users");

        var user_b_1 = document.getElementById('user_b_1');
        var user_b_2 = document.getElementById('user_b_2');
        //toggleButtonColor(user_b_1);

        var empty = true; //no data

        if (chartType == "doughnut") {
            var jsonData = getChartData("conversionRate");
            var doc = document.getElementById('errorUser');
            toggleButtonColor(user_b_1);
            if (doc) doc.innerHTML = '';
            if (jsonData != null) {
                empty = false;
                console.log(jsonData);
                var rate = jsonData['active'] / jsonData['total'];
                var inActive = jsonData['total'] - jsonData['active'];
                console.log(jsonData['active']);
                var labels = ['Active Users', 'Inactive Users'];
                var values = [jsonData['active'], inActive];  // Array of values

                var ctx = document.getElementById('myChartUser').getContext('2d');
                if (window.myChartUser instanceof Chart) {
                    window.myChartUser.destroy();
                }
                //toggleButtonColor(user_b_1);
                //not working (percentage in the doughnut function)
                /*window.myChartUser.register({
                    id: 'doughnutLabel',
                    beforeDraw: function (myChartUser) {
                        var width = myChartUser.width,
                            height = myChartUser.height,
                            ctx = myChartUser.ctx;
    
                        ctx.restore();
                        ctx.font = '20px Times New Roman';
                        ctx.textBaseline = 'middle';
    
                        var activePercentage = (values[0] / jsonData['total']) * 100;
    
                        // Display active percentage inside the doughnut
                        var activeText = activePercentage.toFixed(2) + '%';
                        var activeTextX = Math.round((width - ctx.measureText(activeText).width) / 2);
                        var activeTextY = height / 1.75;
                        ctx.fillText(activeText, activeTextX, activeTextY);
    
                        ctx.save();
                    }
                });*/
                window.myChartUser = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Conversion Rate',
                            data: values,
                            backgroundColor: [
                                'rgb(255, 99, 132)',
                                'rgb(54, 162, 235)'
                            ],
                            hoverOffset: 4
                        }]
                    }
                    , options: {
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    //get the values (%) of inactives/actives
                                    label: function (tooltipItem) {
                                        var dataIndex = tooltipItem.dataIndex;
                                        var dataValue = values[dataIndex];
                                        var label = labels[dataIndex];
                                        var percentage = ((dataValue / jsonData['total']) * 100).toFixed(2);
                                        return `${label}: ${percentage}%`;
                                    }
                                }
                            },
                            legend: {
                                position: 'top'
                            }
                            //not working (percentage in the doughnut function)
                            /*, doughnutLabel: {
                                 labels: [
                                     `${((jsonData['active'] / jsonData['total']) * 100).toFixed(2)}%`, // Display active percentage
                                     `${((inActive / jsonData['total']) * 100).toFixed(2)}%` // Display inactive percentage
                                 ],
                                 font: {
                                     size: '20'
                                 },
                                 color: 'white',
                                 formatter: (value, context) => {
                                     return value;
                                 }
                             },
                            animation: {
                                onComplete: function () {
                                    var chartInstance = window.myChartUser;
                                    var ctx = chartInstance.ctx;
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'middle';
                                    var radius = chartInstance.innerRadius + (chartInstance.radius - chartInstance.innerRadius) / 2;
    
                                    // Display percentages inside each segment
                                    chartInstance.data.labels.forEach(function (label, i) {
                                        var angle = chartInstance._startAngle + (chartInstance._angleStep * i) - Math.PI / 2;
                                        var x = chartInstance.width / 2 + radius * Math.cos(angle);
                                        var y = chartInstance.height / 2 + radius * Math.sin(angle);
                                        ctx.fillStyle = 'white';
                                        ctx.font = '20px Arial';
                                        ctx.fillText(((chartInstance.data.datasets[0].data[i] / total) * 100).toFixed(2) + '%', x, y);
                                    });
                                }
                            }*/
                        }
                    }
                });
            } else {
                var doc = document.getElementById('errorUser');
                if (doc) doc.innerHTML = '';
                var p = document.createElement("p");
                p.textContent = "No Data to Display!!!";
                doc.appendChild(p);
            }
        } else if (chartType == "line") {
            var buttonAreaUserLineChart = document.getElementById('buttonAreaUserLineChart');
            if (buttonAreaUserLineChart) buttonAreaUserLineChart.innerHTML = '';

            var doc = document.getElementById('errorUser');
            if (doc) doc.innerHTML = '';
            toggleButtonColor(user_b_2);

            createButtonsLineChart();

            var line_b_1 = document.getElementById('user_line_b_1');
            var line_b_2 = document.getElementById('user_line_b_2');

            if (time == 'day') {
                var jsonData = getChartData("day");

                if (jsonData != null) {
                    empty = false;
                    console.log(jsonData);
                    var labels = jsonData.map(item => item.order_date);
                    var values = jsonData.map(item => item.num_orders);
                    const data = {
                        labels: labels,
                        datasets: [{
                            label: 'Last Week',
                            data: values,
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    };

                    var ctx = document.getElementById('myChartUser').getContext('2d');
                    if (window.myChartUser instanceof Chart) {
                        window.myChartUser.destroy();
                    }
                    toggleButtonColorLine(line_b_1);
                    window.myChartUser = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Number of Orders',
                                data: values,
                                backgroundColor: [
                                    'rgb(128, 0, 32)'
                                ],
                                hoverOffset: 4
                            }]
                        }, options: {
                            scales: {
                                y: {
                                    beginAtZero: true, // Start y-axis at zero
                                    ticks: {
                                        stepSize: 1, // Increment y-axis ticks by 1
                                        callback: function (value, index, values) {
                                            return value; // Customize tick labels as needed
                                        }
                                    }
                                }
                            }
                        }
                    });
                    //createButtonsLineChart();
                } else {
                    var doc = document.getElementById('errorUser');
                    var p = document.createElement("p");
                    p.textContent = "No Data to Display!!!";
                    doc.appendChild(p);
                }
            } else if (time == 'week') {
                // var buttonAreaUserLineChart = document.getElementById('buttonAreaUserLineChart');
                // if (buttonAreaUserLineChart) buttonAreaUserLineChart.innerHTML = '';
                var line_b_2 = document.getElementById('user_line_b_2');
                var jsonData = getChartData("week");
                if (jsonData != null) {
                    empty = false;
                    console.log(jsonData);
                    var labels = ['week 1', 'week 2', 'week 3', 'week 4'];
                    var values = jsonData.map(item => item.num_orders);
                    const data = {
                        labels: labels,
                        datasets: [{
                            label: 'Last Month',
                            data: values,
                            fill: false,
                            borderColor: 'rgb(75, 192, 192)',
                            tension: 0.1
                        }]
                    };

                    var ctx = document.getElementById('myChartUser').getContext('2d');
                    if (window.myChartUser instanceof Chart) {
                        window.myChartUser.destroy();
                    }
                    toggleButtonColorLine(line_b_2);
                    window.myChartUser = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Number of Orders',
                                data: values,
                                backgroundColor: [
                                    'rgb(128, 0, 32)'
                                ],
                                hoverOffset: 4
                            }]
                        }, options: {
                            scales: {
                                y: {
                                    beginAtZero: true, // Start y-axis at zero
                                    ticks: {
                                        stepSize: 5, // Increment y-axis ticks by 5
                                        callback: function (value, index, values) {
                                            return value; // Customize tick labels as needed
                                        }
                                    }
                                }
                            }
                        }
                    });
                    //createButtonsLineChart();
                } else {
                    var doc = document.getElementById('errorUser');
                    var p = document.createElement("p");
                    p.textContent = "No Data to Display!!!";
                    doc.appendChild(p);
                }
            }
            if (empty == true) {
                // no data
                var doc = document.getElementById('errorUser');
                var p = document.createElement("p");
                p.textContent = "No Data to Display!!!";
                doc.appendChild(p);
            }
        }
        //createButtons("users");
    }

</script>

<body>
    <!-- <h3>Stats</h3> -->
    <div class="container">

        <div class="statsContainer">
            <h4>Categories Stat</h4>
            <div id="errorCategory"></div>
            <div id="buttonAreaCategory"></div>
            <br>
            <canvas id="myChartCategory"></canvas>
        </div>

        <div class="statsContainer"
            style="border-right:1px solid var(--navy-blue) ; border-left:1px solid var(--navy-blue)">
            <h4>Products Stat</h4>
            <div id="errorProduct"></div>
            <div id="buttonAreaProduct"></div>
            <br>
            <canvas id="myChartProduct"></canvas>
        </div>

        <div class="statsContainer">
            <h4>Users Stat</h4>
            <div id="errorUser"></div>
            <div id="buttonAreaUser"></div>
            <br>
            <canvas id="myChartUser"></canvas>
            <div id="buttonAreaUserLineChart"></div>
        </div>

    </div>

    <script>
        displayStatsCategory("bar");
        displayStatsProduct("pie");
        displayStatsUser("line", "day");
    </script>

    <?php
    echo "<br>" . $back; //button
    ?>
    <div class="popup-overlay" id="popup-overlay" style="display:none;">
            <div class="popup-content" id="popup-content">
                <h2>Are you sure you want to delete this account?</h2>
                <button class='add-button' onclick="deleteAccount()">Yes</button>
                <button class='add-button' onclick="closePopUp()">No</button>
            </div>
        </div>
        <script type="text/javascript" src="script.js"></script>

</body>
<footer>

</footer>

</html>