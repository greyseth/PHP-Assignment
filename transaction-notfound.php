<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./styles/style.css"/>
    <link rel="stylesheet" href="./styles/login.css"/>
    <title>Modul Restoran - Transaction Not Found</title>
</head>
<body>
    <header>
        <h1>Restaurant of things</h1>
    </header>  

    <main class="invalid-main">
        <h2>NOT FOUND</h2>
        <div class="cross-container">
            <div class="cross-part part-left"></div>
            <div class="cross-part part-right"></div>
        </div>

        <p>The transaction Id you entered was not found</p>

        <button class="btn btn-primary" onclick="window.location = './transactions.php'">Return</button>
    </main>
</body>
</html>