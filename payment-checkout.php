<?php 
    include "connect.php";
    session_start();

    $userdata = array();
    //Sets privilege to user by default, and pfp to null
    $userdata['privilege'] = 'user';
    $userdata['pfp'] = NULL;
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
    <link rel="stylesheet" href="./styles/payment.css"/>

    <title>Modul Restoran - Confirm Payment</title>
</head>
<body>
    <header>
        <h1 onclick="window.location = 'index.php';">Restaurant of things</h1>
        <div>
            <img src="<?php if ($userdata['pfp'] === null || !$loggedIn) {
                echo './img/nopfp.svg';
            }else {
                echo './uploads/'.$userdata['pfp'];
            }
            ?>" onclick="window.location = './account.php';" />
        </div>
    </header>

    <main>
        <?php 
            //Calculates total pay amount

            $amount = 0;

            $userOrders = array();
            if ($loggedIn) $userOrders = explode('#', $userdata['orders']);
            else $userOrders = explode('#', $_SESSION['orders']);

            for($i = 0; $i < count($userOrders); $i++) {
                if (empty($userOrders[$i])) continue;
                
                $itemId = explode('q', $userOrders[$i])[0];
                $itemPrice = $conn->query("SELECT harga FROM resto WHERE id=".$itemId.";")->fetch_array();
                $amount += ($itemPrice['harga'] * explode('q', $userOrders[$i])[1]);
            }
        ?>
    
        <h2>Confirm your purchase</h2>
        <form action="process-checkout.php" method="post">
            <!-- This looks very empty. I'll have to put more stuff in later -->
            <label>Table number</label>
            <input name="tablenum" required type="number"/>

            <label>Payment method</label>
            <select name="paymethod" value="card">
                <option value="card">Credit/Debit Card</option>
                <option value="cash">Cash</option>
            </select>

            <input type="text" hidden value="<?php echo $amount ?>" name="payamount"/>
            <input type="text" hidden value="<?php echo (($loggedIn ? $userdata['orders'] : $_SESSION['orders'])) ?>" name="orders"/>
            <!-- <input type="datetime" hidden value="<?php $date = new DateTime('now'); echo $date->format('Y-m-d'); ?>" -->

            <div>
                <input class="btn btn-danger" type="submit" name="goback" value="Cancel and return"/>
                <input class="btn btn-primary" type="submit" name="confirm" value="Pay"/>
            </div>
        </form>
    </main>
</body>
</html>