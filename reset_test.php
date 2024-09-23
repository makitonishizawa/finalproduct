<?php
session_start();

// 新しいテストを開始するため、セッションのtestIdや関連データをリセット
unset($_SESSION['testId']);
unset($_SESSION['testCount']);
unset($_SESSION['usedNumbers']);
unset($_SESSION['currentPart']);
unset($_SESSION['correctAnswers']);

// testmenu.phpにリダイレクトして、新しいテストを開始する
header('Location: testmenu.php');
exit();
?>
