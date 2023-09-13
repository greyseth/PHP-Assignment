<?php
    if (!isset($_GET['t'])) header("location:transactions.php");
    
    include 'connect.php';
    session_start();

    $errormsg = NULL;
    $alertmsg = NULL;

    $userdata = array();
    //Sets privilege to user by default, and pfp to null
    $userdata['privilege'] = 'user';
    $userdata['pfp'] = NULL;
    $userdata['id'] = -1;
    $loggedIn = false;
    if (isset($_SESSION['id'])) {$userdata = $conn->query("SELECT * FROM restoacc WHERE id=".$_SESSION['id'].";")->fetch_array(); $loggedIn = true;}

    if (isset($_POST['accept'])) {
        $conn->query("UPDATE restoorders SET status='accepted', employeeId=".$_POST['employee']." WHERE id=".$_GET['t'].";");
    }else if (isset($_POST['reject'])) {
        $conn->query("UPDATE restoorders SET status='rejected', employeeId=".$_POST['employee']." WHERE id=".$_GET['t'].";");
    }

    $transdata = $conn->query("SELECT * FROM restoorders WHERE id=".$_GET['t'].";")->fetch_array();
    if (!$transdata) header("location:transaction-notfound.php");

    if (isset($_POST['setTarget'])) {
        for($i = 0; $i < count(explode('#', $transdata['orders'])); $i++) {
            $cur = explode('#', $transdata['orders'])[$i];
            $curData = explode('q', $cur);
            
            //0 - id, 1 - quantity, 2 - menu status
            if ($curData[0] === $_POST['setTarget']) {
                if (isset($_POST['setpending'])) $curData[2] = 'Pending';
                if (isset($_POST['setreject'])) $curData[2] = 'Rejected';
                if (isset($_POST['setready'])) $curData[2] = 'Ready';

                $newCurData = $curData[0].'q'.$curData[1].'q'.$curData[2];
                $newTransData = explode('#', $transdata['orders']);
                $newTransData[$i] = $newCurData;
                
                $newTransString = '';
                for($i = 0; $i < count($newTransData); $i++) {
                    $newTransString .= '#'.$newTransData[$i];
                }

                if ($conn->query("UPDATE restoorders SET orders='".$newTransString."' WHERE id=".$transdata['id'].";")) {
                    $transdata = $conn->query("SELECT * FROM restoorders WHERE id=".$_GET['t'].";")->fetch_array();
                    $alertmsg = 'Successfully updated menu status';
                }else $errormsg = 'An error has occurred';
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
    <link rel="stylesheet" href="./styles/transactions.css"/>

    <title>Modul Restoran - Transaction <?php echo $transdata['id'] ?></title>
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
    <?php if(isset($alertmsg) || isset($errormsg)) : ?>

        <div class="notif <?php echo (isset($errormsg) ? 'notif-red' : ''); ?>">
            <div class="notif-header">
                <p class="notif-header-text"><b><?php echo ((isset($errormsg)) ? 'Failure' : 'Success') ?></b></p>
                <p class="notif-close" onclick="document.querySelector('.notif').hidden = true;">Close</p>
            </div>
            <div>
                <p><?php echo ((isset($errormsg)) ? $errormsg : $alertmsg) ?></p>
            </div>
        </div>
        <?php unset($alertmsg); unset($errormsg); ?>

    <?php endif ?>
    
        <h2>Viewing transaction <?php echo $transdata['id'] ?></h2>

        <!-- Need to show status, payment status, table number, order date, orders, pay amount -->
        <section class="detail-details">
            <div>Transaction status: <b style="color: <?php echo (($transdata['status'] === 'pending') ? 'yellow' : (($transdata['status'] === 'accepted') ? 'limegreen' : 'red')) ?>"><?php echo strtoupper($transdata['status']); ?></b></div>
            <div>Payment status: <b style="color: <?php echo (($transdata['paystatus'] === 'unpaid') ? 'red' : 'green') ?>"><?php echo strtoupper($transdata['paystatus']) ?></b></div>
            <div>Table Number: <b><?php echo $transdata['tablenum'] ?></b></div>
            <div>Order date: <b><?php echo $transdata['ordertime'] ?></b></div>
        </section>

        <h2 class="left-h2">Ordered Items</h2>
        <table class="table" style="text-align:center">
            <thead class="thead-dark">
                <tr>
                    <td>Menu Id</td>
                    <td class="w-25">Menu Name</td>
                    <td>Menu Price</td>
                    <td>Menu status</td>
                    <td>Ordered menu status</td>
                    <td>Ordered Quantity</td>
                    <?php if($userdata['privilege'] === 'admin') : ?> <td>Action</td> <?php endif ?>
                </tr>
            </thead>
            <tbody>
            <?php 
                $totalPrice = 0;

                $orderedItems = explode('#', $transdata['orders']);
                for($i = 0; $i < count($orderedItems); $i++) {
                    $item = explode('q', $orderedItems[$i]);

                    if (count($item) <= 1) continue;

                    $itemId = $item[0];
                    $itemQty = $item[1];
                    $itemStatus = $item[2];

                    $itemData = $conn->query("SELECT * FROM resto WHERE id=".$itemId.";");
                    if (mysqli_num_rows($itemData) > 0) {
                        $itemDataF = $itemData->fetch_array();

                        echo '<tr>';
                        
                        echo '<td>'.$itemDataF['id'].'</td>';
                        echo '<td>'.$itemDataF['nama'].'</td>';
                        echo '<td>'.number_format($itemDataF['harga']).'</td>';
                        echo '<td style="color: '.(($itemDataF['status'] === 'Tersedia') ? 'green' : 'red').'">'.$itemDataF['status'].'</td>';
                        echo '<td style="color: '.(($itemStatus === 'Pending') ? 'yellow' : (($itemStatus === 'Rejected') ? 'red' : 'green')).'">'.$itemStatus.'</td>';
                        echo '<td>'.$itemQty.'</td>';
                        if ($userdata['privilege'] === 'admin') {
                            echo '<td><form method="post">';
                            echo '<input type="text" hidden value="'.$itemDataF['id'].'" name="setTarget"/>';

                            if ($itemStatus !== 'Pending') echo '<input class="btn btn-warning" type="submit" name="setpending" value="Pending"/>';
                            if ($itemStatus !== 'Rejected') echo '<input class="btn btn-danger" type="submit" name="setreject" value="Reject"/>';
                            if ($itemStatus !== 'Ready') echo '<input class="btn btn-primary" type="submit" name="setready" value="Ready"/>';

                            echo '</form></td>';
                        }

                        echo '</tr>';

                        $totalPrice += ((int)$itemDataF['harga'] * (int)$itemQty);
                    }else {
                        echo '<tr>';
                        
                        echo '<td>[MISSING MENU]</td>';
                        echo '<td>[MISSING MENU]</td>';
                        echo '<td>[MISSING MENU]</td>';
                        echo '<td>[MISSING MENU]</td>';

                        echo '</tr>';
                    }                 
                }
            ?>
            </tbody>
        </table>
        <h3>Total price: <b><?php echo number_format($totalPrice) ?></b></h3>

        <?php 
            if ($transdata['status'] !== 'pending' && $transdata['employeeId'] !== NULL) {
        ?>
            <?php $employeeName = $conn->query("SELECT username FROM restoacc WHERE id=".$transdata['employeeId'].";")->fetch_array(); ?>
            
            <h3>This transaction has been <?php echo $transdata['status'] ?> by <?php echo $employeeName['username'] ?></h3>
        <?php }else if ($transdata['status'] === 'pending' && $userdata['privilege'] === 'admin') {?>
            <form method="post" class="detail-admin">
                <input type="text" name="select" value="<?php echo $_GET['t'] ?>" hidden/>
                <input type="text" name="employee" value="<?php echo $userdata['id'] ?>" hidden />
                <input style="background-color:green" type="submit" name="accept" value="Accept Transaction"/>
                <input style="background-color:red" type="submit" name="reject" value="Reject Transaction"/>
            </form>
        <?php } ?>
    </main>
</body>
</html>