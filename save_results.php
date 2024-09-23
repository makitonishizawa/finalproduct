<?php
session_start();

// MySQLデータベース接続設定
$host = 'localhost';
$dbname = 'gs_db_finalproduct';
$user = 'root';
$password = '';

// POSTデータからuserId, testId, testType, correctAnswersを取得
$userId = isset($_POST['userId']) ? $_POST['userId'] : null;
$testId = isset($_POST['testId']) ? $_POST['testId'] : null;
$testType = isset($_POST['testType']) ? $_POST['testType'] : null;
$correctAnswers = isset($_POST['results']) ? $_POST['results'] : null;

// POSTデータの確認
echo '<pre>';
print_r($_POST);
echo '</pre>';


// 全体の問題数 (例えば 10 問と仮定)
$totalQuestions = 10;

// 正解率の計算 (正解数 / 全体の問題数 * 100)
$accuracyRate = ($correctAnswers / $totalQuestions) * 100;

// 必要なデータが揃っているか確認 ($correctAnswers === 0 でも問題なしにするためにnullをチェック)
if ($userId === null || $testId === null || $testType === null || $correctAnswers === null) {
    echo "必要なデータが不足しています。";
    exit();
}

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 現在の日時を取得
    $currentDateTime = date('Y-m-d H:i:s');

    // データベースに正解率を保存するSQLクエリ
    $stmt = $pdo->prepare("INSERT INTO results (userId, testId, testType, results, created_at) 
                           VALUES (:userId, :testId, :testType, :results, :created_at)");

    // パラメータをバインド
    $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindParam(':testId', $testId, PDO::PARAM_STR);
    $stmt->bindParam(':testType', $testType, PDO::PARAM_INT);
    $stmt->bindParam(':results', $accuracyRate, PDO::PARAM_STR);  // 正解率を保存
    $stmt->bindParam(':created_at', $currentDateTime, PDO::PARAM_STR);

    // クエリの実行
    if ($stmt->execute()) {
        // テスト回数をカウント
        $_SESSION['testCount']++;

        // テストの進行を管理（視覚・聴覚を交互に切り替える）
        if ($_SESSION['currentPart'] === 'visual') {
            $_SESSION['currentPart'] = 'audio';  // 次は聴覚テスト
        } else {
            $_SESSION['currentPart'] = 'visual';  // 次は視覚テスト
        }

        // 10回のテスト（視覚・聴覚それぞれ5回ずつ）が終わったら結果ページにリダイレクト
        if ($_SESSION['testCount'] >= 10) {
            header("Location: resultsmenu.php");
            exit();
        } else {
            // 次のテストに進む（testmenu.phpにリダイレクト）
            header("Location: testmenu.php");
            exit();
        }
    } else {
        echo "結果の保存中にエラーが発生しました。";
    }
} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
    exit();
}
?>
