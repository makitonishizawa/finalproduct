<?php
session_start();

// MySQLデータベース接続設定
$host = 'localhost';  // データベースホスト
$dbname = 'gs_db_finalproduct';  // データベース名
$user = 'root';  // ユーザー名
$password = '';  // パスワード（必要に応じて設定）

// POSTデータからuserId, test, resultsを取得
$userId = isset($_POST['userId']) ? $_POST['userId'] : null;
$testType = isset($_POST['test']) ? $_POST['test'] : null;
$results = isset($_POST['results']) ? $_POST['results'] : null;

if (!$userId || !$testType || !$results) {
    echo "必要なデータが不足しています。";
    exit();
}

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 現在の日時を取得
    $currentDateTime = date('Y-m-d H:i:s');

    // データベースに結果を保存するSQLクエリ
    $stmt = $pdo->prepare("INSERT INTO results (userId, test, results, created_at) VALUES (:userId, :testType, :results, :created_at)");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':testType', $testType, PDO::PARAM_INT);
    $stmt->bindParam(':results', $results, PDO::PARAM_STR);
    $stmt->bindParam(':created_at', $currentDateTime, PDO::PARAM_STR);
    $stmt->execute();

    echo "結果が保存されました。";
} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
    exit();
}
?>
