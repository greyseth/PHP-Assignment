<?php
    include 'connect.php';
    session_start();

    $userdata = array();
    //Sets privilege to user by default, and pfp to null
    $userdata['privilege'] = 'user';
    $userdata['pfp'] = NULL;
    $loggedIn = false;
    if (isset($_SESSION['id'])) {$userdata = $conn->query("SELECT * FROM restoacc WHERE id=".$_SESSION['id'].";")->fetch_array(); $loggedIn = true;}

    //copy pasting this to every file is very inefficient, but i cant figure out a way to kind of have a template and just use that :/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./styles/style.css"/>
    <link rel="stylesheet" href="./styles/index.css"/>

    <title>Modul Restoran</title>
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
        <?php if(isset($_SESSION['successmsg']) || isset($_SESSION['errormsg'])) : ?>

            <div class="notif <?php echo (isset($_SESSION['errormsg']) ? 'notif-red' : ''); ?>">
                <div class="notif-header">
                    <p class="notif-header-text"><b><?php echo ((isset($_SESSION['errormsg'])) ? 'Failure' : 'Success') ?></b></p>
                    <p class="notif-close" onclick="document.querySelector('.notif').hidden = true;">Close</p>
                </div>
                <div>
                    <p><?php echo ((isset($_SESSION['errormsg'])) ? $_SESSION['errormsg'] : $_SESSION['successmsg']) ?></p>
                </div>
            </div>
            <?php unset($_SESSION['successmsg']); unset($_SESSION['errormsg']); ?>

        <?php endif ?>
        
        <?php                                
            if (isset($_POST['addorder'])) {
                //FIXME: Card doesn't update until page is refreshed
                //tis fixed
                $oldOrders = '';
                //Checks if user is logged in and saves to account orders
                if ($loggedIn) {
                    $oldOrders = $userdata['orders'];

                    $newOrders = $oldOrders."#".$_POST['addorder'].'q'.$_POST['orderqty'].'qPending';

                    $orderQuery = "UPDATE restoacc SET orders='".$newOrders."' WHERE id=".$userdata['id'].";";
                    if ($conn->query($orderQuery)) $_SESSION['successmsg'] = "Successfully added to cart";
                    else $_SESSION['errormsg'] = "An error has occurred";

                    header("location:index.php");
                    exit();
                }
                else {
                    if (isset($_SESSION['orders'])) $_SESSION['orders'] .= "#".$_POST['addorder'].'q'.$_POST['orderqty'];
                    else $_SESSION['orders'] = "#".$_POST['addorder'].'q'.$_POST['orderqty'];
                }
            }

            $data = $conn->query("SELECT * FROM resto");

            if (mysqli_num_rows($data) === 0) {
                echo '<h2>Data menu tidak ditemukan</h2>';
            }else {
        ?>
            <!-- Good luck reading this piece of shit :) -->
            <?php if ($userdata['privilege'] === 'admin') : ?>
                <div class="btn-container"><button onclick="window.location='./add.php'" class="btn btn-primary"><img src="./img/add.svg">Add menu</button></div>
            <?php endif ?>
            
            <section class="menu-show">
                <?php 
                    while($row = $data->fetch_assoc()) {
                        if ($row['status'] === 'Tersedia') {
                            $isOrdered = false;

                            $userOrders = array();
                            if ($loggedIn) $userOrders = explode("#", $userdata['orders']);
                            else {
                                if (isset($_SESSION['orders'])) $userOrders = explode("#", $_SESSION['orders']);
                            }

                            for($i = 0; $i < count($userOrders); $i++) {
                                if ($row['id'] === explode('q', $userOrders[$i])[0]) $isOrdered = true;
                            }
                            
                            if (!$isOrdered) {
                                //Not an efficient way to do this check
                                $userForm = '
                                    <form method="post" style="display:flex; flex-direction:column; gap:10px; margin-bottom:5%;">
                                        <input type="number" value="1" name="orderqty"/>
                                        <input type="text" name="addorder" value="'.$row['id'].'" hidden/>
                                        <input type="submit" class="btn btn-primary" value="Place order" />
                                    </form>
                                ';

                                $editLoc = "'./edit.php?item=".$row['id']."'";
                                $adminEdit = '
                                    <button class="btn btn-primary" onclick="window.location='.$editLoc.'">Edit details</button>
                                ';
                                
                                echo '
                                    <div class="card" style="width: 15rem;">
                                        <img class="card-img-top" style="width:125px; height:125px; margin: 0px auto;" src="'.$row['img'].'"/>
                                        <div class="card-body">
                                            <h5 class="card-title">'.$row['nama'].'</h5>
                                            <p class="card-text">'.number_format($row['harga']).' each</p>
                                            <p class="card-text">'.$row['type'].'</p>
                                            '.$userForm.'
                                            '.(($userdata['privilege'] === 'admin') ? $adminEdit : '').'
                                        </div>
                                    </div>
                                ';
                            }else {
                                //So not efficient...
                                echo '
                                    <div class="card" style="width: 15rem;">
                                        <img class="card-img-top" style="width:125px; height:125px; margin: 0px auto;" src="'.$row['img'].'"/>
                                        <div class="card-body">
                                            <h5 class="card-title">'.$row['nama'].'</h5>
                                            <p class="card-text">'.number_format($row['harga']).' each</p>
                                            <p class="card-text">'.$row['type'].'</p>
                                            <h5 class="card-title" style="color: green">Ordered!</h5>
                                        </div>
                                    </div>
                                ';
                            }
                        }else {
                            echo '
                                <div class="card" style="width: 15rem;">
                                    <img class="card-img-top" style="width:125px; height:125px; margin: 0px auto;" src="'.$row['img'].'"/>
                                    <div class="card-body">
                                        <h5 class="card-title">'.$row['nama'].'</h5>
                                        <p class="card-text">'.$row['harga'].' each</p>
                                        <p class="card-text">'.$row['type'].'</p>
                                        <h5 class="card-title" style="color: red;">UNAVAILABLE</h5>
                                    </div>
                                </div>
                            ';
                        }
                    }
                ?>            
            </section>
        <?php } ?>
    </main>

    <!-- <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/bootstrap.bundle.js"></script> -->

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