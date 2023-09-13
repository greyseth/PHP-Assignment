<?php 
    if (isset($_POST['return'])) header("location:index.php");
    
    include "connect.php";
    session_start();

    $uplPath = "uploads/";
    
    $userdata = array();
    $userdata['privilege'] = 'user';
    $loggedIn = false;
    if (isset($_SESSION['id'])) {$userdata = $conn->query("SELECT * FROM restoacc WHERE id=".$_SESSION['id'].";")->fetch_array(); $loggedIn = true;}
    else header("location:login.php");

    if (isset($_POST['updacc'])) {
        if (is_uploaded_file($_FILES['updpfp']['tmp_name'])) $updQuery = "UPDATE restoacc SET username='".$_POST['updname']."', password='".$_POST['updpassword']."', pfp='".$_FILES['updpfp']['name']."', realname='".$_POST['updrealname']."', address='".$_POST['updaddress']."', phone='".$_POST['updphone']."', email='".$_POST['updemail']."', gender='".$_POST['updgender']."' WHERE id=".$_SESSION['id'].";";
        else $updQuery = "UPDATE restoacc SET username='".$_POST['updname']."', password='".$_POST['updpassword']."', realname='".$_POST['updrealname']."', address='".$_POST['updaddress']."', phone='".$_POST['updphone']."', email='".$_POST['updemail']."', gender='".$_POST['updgender']."' WHERE id=".$_SESSION['id'].";";

        if (is_uploaded_file($_FILES['updpfp']['tmp_name'])) move_uploaded_file($_FILES['updpfp']['tmp_name'], $uplPath.$_FILES['updpfp']['name']);

        if ($conn->query($updQuery)) $_SESSION['successmsg'] = "Successfully updated your account";
        else $_SESSION['errormsg'] = "A problem has occurred during account update";

        header("location:index.php");
        exit();
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header("location:index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./styles/style.css" />
    <link rel="stylesheet" href="./styles/account.css"/>
    <title>Modul Restoran - Account</title>
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
        <form method="post" enctype="multipart/form-data">
            <div>
                <img src="<?php if ($userdata['pfp'] === null) echo './img/nopfp.svg'; else echo './uploads/'.$userdata['pfp'].'' ?>" />
                <input type="file" name="updpfp" accept="image/*" />
            </div>
            <div>
                <input type="text" name="updname" value="<?php echo $userdata['username'] ?>" placeholder="New Username" required/>
                <div>
                    <input id="pwinput" value="<?php echo $userdata['password'] ?>" type="password" name="updpassword" placeholder="New Password" required/>
                    <img id="pwview" onclick="togglePwView()" src="./img/viewpassword.svg"/>
                </div>

                <input type="text" name="updrealname" value="<?php echo $userdata['realname'] ?>" placeholder="Real Name" />

                <input type="text" name="updaddress" value="<?php echo $userdata['address'] ?>" placeholder="Home Address" />

                <input type="text" name="updphone" value="<?php echo $userdata['phone'] ?>" placeholder="Phone Number" />
                <input type="text" name="updemail" value="<?php echo $userdata['email'] ?>" placeholder="Email Address" />

                <select name="updgender" value="<?php echo $userdata['gender'] ?>">
                    <option value="undef" <?php echo (($userdata['gender'] === 'undef') ? 'selected' : '') ?> >Gender:</option>
                    <option value="male" <?php echo (($userdata['gender'] === 'male') ? 'selected' : '') ?> >Male</option>
                    <option value="female" <?php echo (($userdata['gender'] === 'female') ? 'selected' : '') ?> >Female</option>
                </select>

                <div>
                    <input class="btn btn-primary" type="submit" name="updacc" value="Update Account" />
                    <input class="btn btn-danger" type="submit" name="logout" value="Log out" />
                    <input class="btn btn-warning" type="submit" name="return" value="Cancel"/>
                </div>
            </div>
        </form>

        <?php 
            $ordersData = $conn->query("SELECT id, payamount, paymethod, ordertime FROM restoorders WHERE customerId=".$userdata['id']." AND status='pending' AND paystatus='unpaid';");
            if (mysqli_num_rows($ordersData) === 0) {
                echo '<h2>You have no pending bills</h2>';
            }else {
        ?>
            <h2>Your Bills</h2>
            <div class="bill-display">
                <?php
                    while($row = $ordersData->fetch_assoc()) {
                        echo '
                            <div class="bill-details">
                                <p class="display-emp">Bill amount: <b>'.$row['payamount'].'</b></p>
                                <p>Pay with <b>'.$row['paymethod'].'</b></p>
                                <p>'.$row['ordertime'].'</p>
                            </div>
                        ';

                        echo '
                            <div class="bill-action">
                                <button onclick="window.location=`./payapi/peypel.php?pay='.$row['id'].'`">Pay now</button>
                            </div>
                        ';
                    }
                ?>
            </div>
        <?php } ?>

        <script>
            let pwHidden = true;
            function togglePwView() {
                pwHidden = !pwHidden;

                if (pwHidden) {
                    document.querySelector('#pwinput').type = 'password';
                    document.querySelector('#pwview').src = './img/viewpassword.svg';
                }else {
                    document.querySelector('#pwinput').type = 'text';
                    document.querySelector('#pwview').src = './img/noviewpassword.svg';
                }
            }
        </script>
    </main>
</body>
</html>