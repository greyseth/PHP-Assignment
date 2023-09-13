<?php
    include "connect.php";
    session_start();

    $userdata = array();
    if (!isset($_SESSION['id'])) header("location: index.php");
    else $userdata = $conn->query("SELECT * FROM restoacc WHERE id=".$_SESSION['id'].";")->fetch_array();

    if($userdata['privilege'] === 'user') header("location: index.php");
    
    $error = NULL;    
    if(isset($_POST['newmenu'])) {
        //File upload handling
        $fileName = $_FILES['img']['name'];
        $tempFileName = $_FILES['img']['tmp_name'];
        $uploaded = move_uploaded_file($tempFileName, 'uploads/'.$fileName);
        
        if ($uploaded) {
            $query = "INSERT INTO resto(nama, harga, type, status, img) VALUES ('".$_POST['newname']."', ".$_POST['newprice'].", '".$_POST['newtype']."', '".$_POST['newstatus']."', '".('uploads/'.$fileName)."');";                
            if ($conn->query($query) === true) {
                $_SESSION['successmsg'] = 'Successfully added new menu "'.$_POST['newname'].'"';
                header("location:index.php");
                exit;
            }
            else $error = 'A problem has occurred during upload.';
        }else $error = 'A problem has occurred with the file.';
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
    <link rel="stylesheet" href="./styles/add.css"/>

    <title>Modul Restoran - Add New Menu</title>
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
        <?php if ($error !== NULL) : ?>

            <div class="notif notif-red">
                <div class="notif-header">
                    <p class="notif-header-text"><b>Failure</b></p>
                    <p class="notif-close" onclick="document.querySelector('.notif').hidden = true;">Close</p>
                </div>
                <div>
                    <p><?php echo $error ?></p>
                </div>
            </div>
            <?php unset($_SESSION['successmsg']); unset($_SESSION['errormsg']); ?>

        <?php endif ?>
    
        <section class="menu-add">            
            <h2>Tambahkan menu baru</h2>
            <form method="post" enctype="multipart/form-data">
                <label>Nama menu</label>
                <input type="text" name="newname"/>

                <label>Harga menu</label>
                <input type="number" name="newprice"/>

                <label>Tipe menu</label>
                <select name="newtype" value="Makanan">
                    <option value="Makanan">Makanan</option>
                    <option value="Minuman">Minuman</option>
                </select>

                <label>Status menu</label>
                <select name="newstatus" value="Tersedia">
                    <option value="Tersedia">Tersedia</option>
                    <option value="Tidak tersedia">Tidak tersedia</option>
                </select>

                <label>Image</label>
                <input type="file" accept="image/*" name="img"/>

                <input class="btn btn-primary" type="submit" name="newmenu" value="Tambahkan"/>
            </form>
        </section>
    </main>
</body>
</html>