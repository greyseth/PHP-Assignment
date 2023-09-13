<?php 
    include "connect.php";
    session_start();

    $alertmsg = NULL;
    $errormsg = NULL;

    if (isset($_POST['login'])) {
        $accFound = false;
                    
        $accounts = $conn->query("SELECT * FROM restoacc;");
        while($row = $accounts->fetch_assoc()) {
            if ($_POST['username'] === $row['username'] && $_POST['password'] === $row['password']) {
                $accFound = true;                
                $_SESSION['id'] = $row['id'];
            }
        }
    
        if (!$accFound) header("location: invalid.php");
        else header("location:index.php");
    }else if (isset($_POST['signup'])) {
        $query = "INSERT INTO restoacc(username, password, privilege) VALUES('".$_POST['username']."', '".$_POST['password']."', 'user');";
        if ($conn->query($query) === true) {
            $_SESSION['id'] = $conn->insert_id;
        }else {
            $errormsg = 'A problem has occurred during the signup process';
        }
    
        header("location: index.php");
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
    <link rel="stylesheet" href="./styles/login.css"/>
    <title>Modul Restoran - Login</title>
</head>
<body>
    <header>
        <h1 onclick="window.location = './index.php';">Restaurant of things</h1>
    </header>

    <main class="login-main">
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
    
        <form method="post">
            <input type="text" name="username" placeholder="Username"/>
            <div>
                <input id="pwinput" type="password" name="password" placeholder="Password"/>
                <img id="pwview" onclick="togglePwView()" src="./img/viewpassword.svg"/>
            </div>

            <div>
                <input type="submit" name="login" value="Login" />
                <input type="submit" name="signup" value="Signup" />
            </div>
        </form>
    </main>

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
</body>
</html>