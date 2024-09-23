<?php
session_start();

//session01.phpで格納した配列を呼び出す
$name = $_SESSION['name'];
$age = $_SESSION['age'];

echo $name. $age;

?>

