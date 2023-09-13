<?php
    if (!isset($_GET['item'])) header("location: index.php");
    
    include 'connect.php';
    session_start();

    $data = $conn->query("SELECT * FROM resto WHERE id='".$_GET['item']."';");
    $target = $data->fetch_array();

    $userdata = array();
    if (!isset($_SESSION['id'])) header("location: index.php");
    else $userdata = $conn->query("SELECT * FROM restoacc WHERE id=".$_SESSION['id'].";")->fetch_array();

    if (isset($_POST['updmenu'])) {
        if (is_uploaded_file($_FILES['updimg']['tmp_name'])) {
            $fileName = $_FILES['updimg']['name'];
            $tempFileName = $_FILES['updimg']['tmp_name'];
            $uploaded = move_uploaded_file($tempFileName, $uplPath.$fileName);
            
            $query = "UPDATE resto SET nama='".$_POST['updname']."', harga=".$_POST['updprice'].", type='".$_POST['updtype']."', status='".$_POST['updstatus']."', img='".$uplPath.$fileName."' WHERE id=".$_POST['updmenu'].";";
            if ($conn->query($query) === true) {
                $_SESSION['successmsg'] = 'Successfully updated menu "'.$_POST['updname'].'"';
                header("location:index.php");
                exit;
            }
            else {$_SESSION['errormsg'] = 'An error has occurred during menu update'; exit;}
        }else {
            $query = "UPDATE resto SET nama='".$_POST['updname']."', harga=".$_POST['updprice'].", type='".$_POST['updtype']."', status='".$_POST['updstatus']."' WHERE id=".$_POST['updmenu'].";";
            if ($conn->query($query) === true) {
                $_SESSION['successmsg'] = 'Successfully updated menu "'.$_POST['updname'].'"';
                header("location:index.php");
                exit;
            }
            else {$_SESSION['errormsg'] = 'An error has occurred during menu update'; exit;}
        }
    }

    if (isset($_POST['del'])) {
        $query = "DELETE FROM resto WHERE id=".$_GET['item'].";";
        if ($conn->query($query) === true) {
            $_SESSION['successmsg'] = 'Deleted a menu item';
            header("location: index.php");
            exit;
        }
        else {$_SESSION['errormsg'] = 'An error has occurred during menu deletion'; exit;}
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
    <link rel="stylesheet" href="./styles/edit.css"/>
    <title>Editing <?php echo $target['nama'] ?></title>
</head>
<body>
    <header>
        <h1 onclick="window.location = 'index.php';">Restaurant of things</h1>
        <div>
            <img src="./img/checkout.svg" onclick="window.location = './transactions.php'"/>
            <img src="./img/cart.svg" onclick="window.location = './checkout.php'" />
            <img src="<?php if ($userdata['pfp'] === null) {
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
    
        <section class="menu-upd">
            <h2>Edit data <?php echo $target['nama'] ?></h2>
            <form method="post" enctype="multipart/form-data">
                <label>Nama menu</label>
                <input type="text" name="updname" value="<?= $target['nama'] ?>"/>

                <label>Harga menu</label>
                <input type="number" name="updprice" value="<?= $target['harga'] ?>"/>

                <label>Tipe menu</label>
                <select name="updtype" value="<?= $target['type'] ?>">
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                </select>

                <label>Status menu</label>
                <select name="updstatus" value="<?= $target['status'] ?>">
                    <option value="Tersedia">Tersedia</option>
                    <option value="Tidak tersedia">Tidak tersedia</option>
                </select>

                <label>Image</label>
                <input type="file" name="updimg" accept="image/*"/>   

                <input type="text" name="updmenu" value="<?php echo $_GET['item'] ?>" hidden />

                <input type="submit" value="Ubah"/>
            </form>
        </section>

        <section class="menu-del">
            <h2>Hapus <?php echo $target['nama'] ?>?</h2>
            <form method="post">
                <input type="text" name="del" value="<?php echo $_GET['item'] ?>" hidden/>    
                <input type="submit" value="Hapus"/>
            </form>
        </section>
    </main>
</body>
</html>