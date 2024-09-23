<?php
session_start()

?>



<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/main.css" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        div {
            padding: 10px;
            font-size: 16px;
        }
        .navbar-default{
            background-color: #3366FF;
            border-color: #3366FF;
        }
        .nav-right{
            display: flex;
            justify-content: flex-end; /* アイテムを右端に寄せる */
            width: 100%;
        }
        .loginInput{
            width: 100%;
            margin:0 auto;
            text-align: center;
        }
    </style>
    <title>ログイン</title>
</head>

<body>

    <!-- <header>
        <nav class="navbar navbar-default">LOGIN</nav>
    </header> -->
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid nav-right">
                <div class="navbar-header">
                    <a class="navbar-brand" href="index.php">トップ</a>
                </div>
            </div>
        </nav>
    </header>
    <!-- lLOGINogin_act.php は認証処理用のPHPです。 -->
    <form name="form1" action="login_act.php" method="post">
        <div class="loginInput">
        <p>ログイン</p>
        ID:<input type="text" name="lid" /><br><br>
        PW:<input type="password" name="lpw" /><br><br>
        <input type="submit" value="LOGIN" />
        </div>
    </form>


</body>

</html>
