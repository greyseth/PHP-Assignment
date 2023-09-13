<?php 
    include "connect.php";
    session_start();

    $userdata = array();
    //Sets privilege to user by default, and pfp to null
    $userdata['privilege'] = 'user';
    $userdata['pfp'] = NULL;
    $userdata['orders'] = '';
    $loggedIn = false;
    if (isset($_SESSION['id'])) {$userdata = $conn->query("SELECT * FROM restoacc WHERE id=".$_SESSION['id'].";")->fetch_array(); $loggedIn = true;}

    $alertmsg = NULL;
    $errormsg = NULL;

    if (isset($_POST['delorder'])) {
        $userOrders = array();
        if ($loggedIn) $userOrders = explode('#', $userdata['orders']);
        else $userOrders = explode('#', $_SESSION['orders']);

        array_splice($userOrders, array_search($_POST['delorder'], $userOrders), 1);

        $orderString = '';
        //print_r($userOrders);
        for($i = 0; $i < count($userOrders); $i++) {
            if (!empty($userOrders[$i])) $orderString .= ('#'.$userOrders[$i]);
        }

        if ($loggedIn) {
            if ($conn->query("UPDATE restoacc SET orders='".$orderString."' WHERE id=".$_SESSION['id'].";") === true) {
                $alertmsg = 'Successfully removed a menu from your orders.';
                $userdata = $conn->query("SELECT * FROM restoacc WHERE id = ".$_SESSION['id'].";")->fetch_array();
            }else {
                $errormsg = 'An error has occurred during menu removal.';
            }
        }else {
            $_SESSION['orders'] = $orderString;
            $alertmsg = 'Successfully removed a menu from your orders.';
        }
    }

    $grandTotal = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./styles/style.css"/>
    <link rel="stylesheet" href="./styles/checkout.css"/>
    <title>Modul Restoran - Checkout</title>
</head>
<body>
    <?php //if ($alertMsg != '') echo '<p>'.$alertMsg.'</p>' ?>

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
        
        <?php 
            if (($loggedIn && !isset($userdata['orders']) || $loggedIn && $userdata['orders'] === '') || (!$loggedIn && !isset($_SESSION['orders']) || !$loggedIn && $_SESSION['orders'] === '')) {
        ?>
            <section class="no-orders">
                <h2>Nothing is ordered yet...</h2>
            </section>
        <?php }else { ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Menu Img</th>
                        <th scope="col">Details</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                    $data = $conn->query("SELECT * FROM resto;");
                    while($row = $data->fetch_assoc()) {
                        $isOrdered = false;
                        $orderData = '';
                        $noOrders = false;

                        $userOrders = array();
                        if ($loggedIn) $userOrders = explode('#', $userdata['orders']);
                        else $userOrders = explode('#', $_SESSION['orders']);

                        for($i = 0; $i < count($userOrders); $i++) {
                            if ($row['id'] === explode('q', $userOrders[$i])[0]) {
                                $isOrdered = true;
                                $orderData = $userOrders[$i];
                            }
                        }
                        
                        if ($isOrdered === true) {
                            echo '<tr>';
                            
                            echo '<td style="width: 30%;"><img src="'.$row['img'].'" /></td>';
                            echo '<td><div class="order-details"><p>'.$row['nama'].'</p><p><b>'.number_format($row['harga']).'</b> each</p><p>Qty: '.explode('q', $orderData)[1].'</p></div></td>';
                            echo '<td>
                                <form method="post" class="order-controls">                                    
                                    <input type="text" name="delorder" value="'.$orderData.'" hidden />
                                    <input class="btn btn-danger" type="submit" value="Remove" />
                                </form></td>';
                            
                            echo '</tr>';

                            $grandTotal += $row['harga'] * explode('q', $orderData)[1];
                        }
                    }
                ?> 
                </tbody>   
            </table>
            <section class="checkout-options">
                <div>
                    <h2>Grand total: <b id="grandtotaldisp"><?php echo number_format($grandTotal); ?></b></h2>
                </div>
                <form method="post" action="./payment-checkout.php">
                    <input class="btn btn-primary" type="submit" value="Continue to payment"/>
                </form>
            </section>
        <?php } ?>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) { 
            var scrollpos = sessionStorage.getItem('scrollpos');
            if (scrollpos) window.scrollTo(0, scrollpos);
        });

        window.onbeforeunload = function(e) {
            sessionStorage.setItem('scrollpos', window.scrollY);
        };
    </script>
</body>
</html>