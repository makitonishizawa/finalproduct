<?php
session_start();

// ログイン確認: ログインしていない場合はlogin.phpへリダイレクト
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// MySQLデータベース接続設定
$host = 'localhost';
$dbname = 'gs_db_finalproduct';
$user = 'root';
$password = '';

// セッションからusernameを取得
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if (!$username) {
    echo 'ログインが必要です。';
    exit();
}

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 完了したテストのみ (testId に対応するエントリが 10 個存在する場合) を取得
    $stmt = $pdo->prepare("
        SELECT testId, MIN(created_at) as test_date
        FROM results
        WHERE userId = :username
        GROUP BY testId
        HAVING COUNT(*) = 10
        ORDER BY test_date DESC
    ");
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $testResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>結果一覧</title>
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #ffffff;
            color: #333;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .test-list {
            margin-top: 20px;
        }

        .test-list ul {
            list-style-type: none;
            padding: 0;
        }

        .test-list li {
            margin: 10px 0;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 1.1em;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* ナビゲーションバーのスタイル */
        .navbar {
            background-color: #003366;
            overflow: hidden;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar a, .navbar span {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 1.2em;
        }

        .navbar a:hover {
            background-color: #cccccc;
            color: black;
        }
    </style>
</head>
<body>
    <!-- ナビゲーションバーを追加 -->
    <div class="navbar">
        <a href="index.php">トップ</a>
        <a href="instruction.php">認知特性とは</a>
        <a href="reset_test.php">診断</a>
        <a href="resultsmenu.php">診断結果</a>

        <!-- ログイン状態によって表示を切り替え -->
        <?php if ($username): ?>
            <a class="right" href="logout.php">ログオフ</a>
            <span class="right"><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php else: ?>
            <a class="right" href="login.php">ログイン/サインアップ</a>
        <?php endif; ?>
    </div>

    <h1>診断結果一覧</h1>

    <?php if (!empty($testResults)): ?>
        <div class="test-list">
            <h2><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>さんの診断結果</h2>
            <ul>
                <?php foreach ($testResults as $test): ?>
                    <li>
                        <a href="results.php?testId=<?php echo htmlspecialchars($test['testId'], ENT_QUOTES, 'UTF-8'); ?>">
                            テストID: <?php echo htmlspecialchars($test['testId'], ENT_QUOTES, 'UTF-8'); ?> -
                            実施日: <?php echo htmlspecialchars(date('Y-m-d', strtotime($test['test_date'])), ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p>現在、表示できるテスト結果がありません。</p>
    <?php endif; ?>

</body>
</html>
