<?php 
    include 'connect.php';
    session_start();

    $userdata = array();
    //Sets privilege to user by default, and pfp to null
    $userdata['privilege'] = 'user';
    $userdata['pfp'] = NULL;
    $userdata['id'] = -1;
    $loggedIn = false;
    if (isset($_SESSION['id'])) {$userdata = $conn->query("SELECT * FROM restoacc WHERE id=".$_SESSION['id'].";")->fetch_array(); $loggedIn = true;}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./styles/style.css"/>
    <link rel="stylesheet" href="./styles/transactions.css"/>

    <title>Modul Restoran - Transactions</title>
</head>
<body>
    <header>
        <h1 onclick="window.location = 'index.php';">Restaurant of things</h1>
        <div>
            <img src="./img/checkout.svg" onclick="window.location = './transactions.php'"/>
            <img src="./img/cart.svg" onclick="window.location = './checkout.php'" />
            <img src="<?php if ($userdata['pfp'] === null || !$loggedIn) {
                echo './img/nopfp.svg';
            }else {
                echo './uploads/'.$userdata['pfp'];
            }
            ?>" onclick="window.location = './account.php';" />
        </div>
    </header>

    <main>
        <?php if ($userdata['privilege'] === 'admin' || !$loggedIn) { ?>
            <div class="trans-search"><input id="search-input" placeholder="Enter transaction number" type="text"/><button onclick='window.location = "./transaction-detail.php?t="+document.querySelector("#search-input").value;'>Search</button></div>
        <?php } ?>
        
        <?php
            if ($userdata['privilege'] !== 'admin') {
            
            $query = "SELECT * FROM restoorders WHERE customerId=".$userdata['id']." ORDER BY ordertime DESC;";
            $data = $conn->query($query);
            if(!$loggedIn) {
                echo '<h2>Log in to save your order and transaction data</h2>';
            }else {
                if (mysqli_num_rows($data) === 0) {
                    echo '<h2>You have no transactions yet</h2>';
                }else {
        ?>     
            <h2>Your transactions</h2>
            <table class="table" style="text-align: center;">
                <thead class="thead-dark">
                    <tr>
                        <td scope="col" style="width: 18%">Transaction Number</td>
                        <td scope="col">Payment status</td>
                        <td scope="col">Order status</td>
                        <td scope="col">Order time</td>                        
                        <td scope="col">Actions</td>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    while($row = $data->fetch_assoc()) {
                        echo '<tr>';

                        echo '<td>'.$row['id'].'</td>';
                        echo '<td style="color:'.(($row['paystatus'] === 'unpaid') ? 'red' : 'limegreen').'">'.strtoupper($row['paystatus']).'</td>';
                        echo '<td style="color:'.(($row['status'] === 'pending') ? 'yellow' : (($row['status'] === 'accepted') ? 'limegreen' : 'red')).'">'.strtoupper($row['status']).'</td>';
                        echo '<td>'.$row['ordertime'].'</td>';
                        echo '<td><button class="btn btn-primary" onclick="window.location = `./transaction-detail.php?t='.$row['id'].'`">View details</button></td>';

                        echo '</tr>';
                    }
                ?>
                </tbody>
            </table>
        <?php } }
            }else {
                //Im sorry for the use of the above curly braces :')
                $query = "SELECT * FROM restoorders ORDER BY ordertime DESC;";
                $data = $conn->query($query);
                if(mysqli_num_rows($data) === 0) {
                    echo '<h2>No customers :(</h2>';
                }else { ?>
                    <!-- tbh i dont remember why i duplicated this piece but im too afraid to remove it -->
                    <table class="table" style="text-align:center">
                        <thead class="thead-dark">
                            <tr>
                                <td scope="col">Transaction Number</td>
                                <td scope="col">Payment status</td>
                                <td scope="col">Order status</td>
                                <td scope="col">Order time</td>
                                <td scope="col">Actions</td>
                            </tr>
                        </thead>

                        <?php
                            while($row = $data->fetch_assoc()) {
                                echo '<tr>';
                                
                                echo '<td>'.$row['id'].'</td>';
                                echo '<td style="color:'.(($row['paystatus'] === 'unpaid') ? 'red' : 'limegreen').'">'.strtoupper($row['paystatus']).'</td>';
                                echo '<td style="color:'.(($row['status'] === 'pending') ? 'yellow' : (($row['status'] === 'accepted') ? 'limegreen' : 'red')).'">'.strtoupper($row['status']).'</td>';
                                echo '<td>'.$row['ordertime'].'</td>';
                                echo '<td><button class="btn btn-primary" onclick="window.location = `./transaction-detail.php?t='.$row['id'].'`">View details</button></td>';

                                echo '</tr>';
                            }
                        ?>
                    </table>
                <?php }
            } 
        
        ?>
    </main>
</body>
</html>