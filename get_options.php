<?php
session_start();

// MySQLデータベース接続設定
$host = 'localhost';  
$dbname = 'gs_db_finalproduct';  
$user = 'root';  
$password = '';  

$questionId = isset($_POST['questionId']) ? $_POST['questionId'] : null;
$randomNumber = isset($_POST['randomNumber']) ? $_POST['randomNumber'] : null;

if (!$questionId || !$randomNumber) {
    echo json_encode(['error' => '質問IDまたはランダム番号が設定されていません。']);
    exit();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // optionsテーブルから選択肢を取得 (id = randomNumber の条件を追加)
    $optionsStmt = $pdo->prepare("SELECT optionId, description 
                                  FROM options 
                                  WHERE questionId = :questionId AND id = :randomNumber LIMIT 4");
    $optionsStmt->bindParam(':questionId', $questionId, PDO::PARAM_INT);
    $optionsStmt->bindParam(':randomNumber', $randomNumber, PDO::PARAM_INT);
    $optionsStmt->execute();
    $options = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);

    // solutionsテーブルから正解を取得
    $solutionStmt = $pdo->prepare("SELECT solutionId 
                                   FROM solutions 
                                   WHERE questionId = :questionId AND id = :randomNumber");
    $solutionStmt->bindParam(':questionId', $questionId, PDO::PARAM_INT);
    $solutionStmt->bindParam(':randomNumber', $randomNumber, PDO::PARAM_INT);
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
