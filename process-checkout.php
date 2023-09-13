<?php
    include "connect.php";
    session_start();

    $userdata = array();
    //Sets privilege to user by default, and pfp to null
    $userdata['privilege'] = 'user';
    $userdata['id'] = -1;
    $userdata['pfp'] = NULL;
    $loggedIn = false;
    if (isset($_SESSION['id'])) {$userdata = $conn->query("SELECT * FROM restoacc WHERE id=".$_SESSION['id'].";")->fetch_array(); $loggedIn = true;}

    if (isset($_POST['goback'])) header("location:index.php");
    else if (isset($_POST['confirm'])) {
        $transQuery = "INSERT INTO restoorders (paymethod, tablenum, ordertime, orders, customerId, payamount) VALUES ('".$_POST['paymethod']."', ".$_POST['tablenum'].", now(), '".$_POST['orders']."', ".$userdata['id'].", ".$_POST['payamount'].");";
        if ($conn->query($transQuery) === true) {
            //Biggest id doesnt work yet
            // $biggestId = 0;
            // $allTransIds = $conn->query("SELECT id FROM restoorders");
            // if (mysqli_num_rows($allTransIds) === 0) {
            //     $biggestId = 1;
            // }else {
            //     while($row = $allTransIds->fetch_assoc()) {
            //         if ((int)$row['id'] > $biggestId) $biggestId = ((int)$row['id']+1);
            //     }
            // }
            
            $lastId = $conn->insert_id;
            
            $transCodeSet = "UPDATE restoorders SET ordernum='tn_".$lastId."' WHERE id=".$lastId.";";
            $conn->query($transCodeSet);
            
            if ($loggedIn) {
                $orderResetQuery = "UPDATE restoacc SET orders='' WHERE id=".$userdata['id'].";";
                $conn->query($orderResetQuery);  
            }else {
                unset($_SESSION['orders']);
            }
        }
    }
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

    <title>Modul Restoran - Payment confirmed</title>
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

    <main class="confirmed-main">
        <h2>Payment confirmed!</h2>
        <div class="cross-container">
            <div class="cross-part part-left"></div>
            <div class="cross-part part-right"></div>
        </div>
        <?php echo ($loggedIn ? '<p>Check your account page to pay the bill</p>' : ''); ?>
        <p>Transaction id: <?php echo $lastId; ?></p>

        <button class="btn btn-primary" onclick="window.location='./transactions.php'">View your orders</button>
        <button class="btn btn-primary" onclick="window.location = './index.php'">Order more</button>
    </main>
</body>
</html>