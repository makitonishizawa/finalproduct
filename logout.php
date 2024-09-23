<?php
session_start();
session_destroy();  // セッションを終了する
header("Location: index.php");  // ログインページにリダイレクト
exit();
