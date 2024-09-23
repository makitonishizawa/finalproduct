<?php
session_start();

// ログイン確認: ログインしていない場合はlogin.phpへリダイレクト
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// セッションの初期化が行われているか確認
if (!isset($_SESSION['testCount'])) {
    $_SESSION['testCount'] = 0;  // テストの回数を初期化
    $_SESSION['currentPart'] = 'visual';  // 現在のパートを視覚パートに設定
    $_SESSION['usedNumbers'] = []; // 使用済みのrandomNumberを格納する配列を初期化
}

// testIdの初期化も、セッションに存在しない場合に生成する
if (!isset($_SESSION['testId'])) {
    $_SESSION['testId'] = uniqid();  // 一意のtestIdを生成
}

// デバッグ用: セッションの内容を確認
echo 'Test ID in session: ' . $_SESSION['testId'];

// 同じtestId内で同じrandomNumberが出ないようにする関数
function generateUniqueRandomNumber($min, $max) {
    if (!isset($_SESSION['usedNumbers'])) {
        $_SESSION['usedNumbers'] = []; // セッションが初期化されていなかった場合、初期化する
    }

    $availableNumbers = range($min, $max);
    $unusedNumbers = array_diff($availableNumbers, $_SESSION['usedNumbers']);  // 未使用の数値を取得

    if (empty($unusedNumbers)) {
        // すべての数が使用済みの場合は再度リセットする
        $_SESSION['usedNumbers'] = [];
        $unusedNumbers = $availableNumbers;
    }

    // 未使用の数値からランダムに1つを選択
    $randomNumber = $unusedNumbers[array_rand($unusedNumbers)];
    $_SESSION['usedNumbers'][] = $randomNumber;  // 使用済みリストに追加
    return $randomNumber;
}

// 何回目かを表示するためのカウンタ
$partNumber = floor($_SESSION['testCount'] / 2) + 1;
$partLabel = $_SESSION['currentPart'] === 'visual' ? '視覚パート' : '聴覚パート';

?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>診断メニュー</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #ffffff;
            color: #333;
            text-align: center;
            padding: 50px;
            margin-top: 80px; /* メニューバー分の余白を確保 */
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #444;
        }

        button {
            background-color: #007BFF; /* ボタンの背景色を青系に変更 */
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            margin: 10px;
            width: 300px; /* ボタンの横幅を統一 */
        }

        button:hover {
            background-color: #0056b3; /* ホバー時により濃い青に */
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
        }

        .part-info {
            font-size: 1.5em;
            margin-bottom: 20px;
            color: #333;
        }

        footer {
            margin-top: 50px;
            font-size: 0.8em;
            color: #888;
        }

        /* ナビゲーションバーのスタイル */
        .navbar {
            background-color: #003366; /* 暗い青に変更 */
            overflow: hidden;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 1.2em;
        }

        .navbar a:hover {
            background-color: #cccccc; /* ホバー時は灰色に変更 */
            color: black;
        }

        /* フォームが中央に配置されるように */
        div.container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>

<body>

    <!-- ナビゲーションバーを追加 -->
    <div class="navbar">
        <a href="index.php">トップ</a>
        <a href="instruction.php">認知特性とは</a>
        <a href="testmenu.php">診断</a>
        <a href="resultsmenu.php">診断結果</a>
    </div>

    <div class="container">
        <h1>診断をスタート</h1>

        <?php if ($_SESSION['testCount'] < 10): ?>
            <!-- 現在のパートと回数を表示 -->
            <div class="part-info">
                <p><?php echo $partLabel . ' ' . $partNumber; ?></p>
            </div>

            <!-- テストスタートボタン -->
            <form id="testForm" action="<?php echo $_SESSION['currentPart'] === 'visual' ? 'passage.php' : 'passage2.php'; ?>" method="POST">
                <input type="hidden" name="randomNumber" value="<?php echo generateUniqueRandomNumber(1, 10); ?>">
                <input type="hidden" name="testType" value="<?php echo $_SESSION['currentPart'] === 'visual' ? 1 : 2; ?>">
                <input type="hidden" name="testId" value="<?php echo $_SESSION['testId']; ?>"> <!-- testIdを付与 -->
                <button type="submit">
                    <?php echo $_SESSION['currentPart'] === 'visual' ? 'スタート' : 'スタート(音量に注意)'; ?>
                </button>
            </form>
        <?php else: ?>
            <button onclick="location.href='resultsmenu.php'">結果を見る</button>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 記憶特性診断. All rights reserved.</p>
    </footer>

</body>

</html>
