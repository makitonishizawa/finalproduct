<?php
session_start();

// MySQLデータベース接続設定
$host = 'localhost';  // データベースホスト
$dbname = 'gs_db_finalproduct';  // データベース名
$user = 'root';  // ユーザー名
$password = '';  // パスワード（必要に応じて設定）

$questionId = isset($_POST['questionId']) ? $_POST['questionId'] : null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // optionsテーブルから選択肢を取得
    $optionsStmt = $pdo->prepare("SELECT optionId, description FROM options WHERE questionId = :questionId LIMIT 4");
    $optionsStmt->bindParam(':questionId', $questionId, PDO::PARAM_INT);
    $optionsStmt->execute();
    $options = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // solutionsテーブルから正解を取得
    $solutionStmt = $pdo->prepare("SELECT solutionId FROM solutions WHERE questionId = :questionId");
    $solutionStmt->bindParam(':questionId', $questionId, PDO::PARAM_INT);
    $solutionStmt->execute();
    $correctSolution = $solutionStmt->fetch(PDO::FETCH_ASSOC);

    // データをJSONで返す
    echo json_encode([
        'options' => $options,
        'correctSolutionId' => $correctSolution['solutionId']
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
