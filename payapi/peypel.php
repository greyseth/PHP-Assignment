<?php 
    include "../connect.php";
    session_start();

    if (isset($_GET['pay'])) {
        $conn->query("UPDATE restoorders SET paystatus = 'paid' WHERE id = ".$_GET['pay'].";");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./peypel.css"/>

    <title>Peypel (not scam)</title>
</head>
<body>
    <header>
        <h1>Peypel.com</h1>
    </header>

    <main class="loading-main">
        <h2 id="info">Totally legit payment API is loading...</h2>
        <div class="balls">
            <div id="ball"></div>
            <div id="ball"></div>
            <div id="ball"></div>
            <div id="ball"></div>
            <div id="ball"></div>
            <div id="ball"></div>
            <div id="ball"></div>
            <div id="ball"></div>
            <div id="ball"></div>
        </div>

        <script>
            let i = 0;
            for (const child of document.querySelector('.balls').children) {
                setTimeout(() => {
                    // child.classList.remove("ball-bounce");
                    // window.requestAnimationFrame(() => {
                    //     child.classList.add("ball-bounce");
                    // });
                    child.classList.add("ball-bounce");
                }, 100 * i);

                i++;
            }

            setTimeout(() => {
                document.getElementById("info").textContent = "Money has been taken. Now redirecting...";
            }, 5000);

            setTimeout(() => {
                window.location = "../account.php";
            }, 7000);
        </script>
    </main>
</body>
</html>