<?php
session_start();

// MySQLデータベース接続設定
$host = 'localhost';  // データベースホスト
$dbname = 'gs_db_finalproduct';  // データベース名
$user = 'root';  // ユーザー名
$password = '';  // パスワード（必要に応じて設定）

// POSTデータからrandomNumber、testType、testIdを取得
$randomNumber = isset($_POST['randomNumber']) ? $_POST['randomNumber'] : null;
$testType = isset($_POST['testType']) ? $_POST['testType'] : null;
$testId = isset($_POST['testId']) ? $_POST['testId'] : null;

// POSTデータの確認
echo '<pre>';
print_r($_POST);
echo '</pre>';

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // passageIdが1から10の間のデータを取得するSQLクエリ
    $stmt = $pdo->prepare("SELECT passageId, passage FROM passage WHERE id = :randomNumber AND passageId BETWEEN 1 AND 10");
    $stmt->bindParam(':randomNumber', $randomNumber, PDO::PARAM_INT);
    $stmt->execute();

    // 結果を取得し、passageIdとpassage列を取得
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Passage Reader</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #ffffff; /* 背景色を白に設定 */
            color: #333;
            text-align: center;
            padding: 50px;
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #444;
        }

        #passageDisplay {
            font-size: 1.5em;
            margin: 20px 0;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        button {
            background-color: #007BFF; /* 青系のボタン */
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            margin: 10px;
        }

        button:hover {
            background-color: #0056b3; /* ホバー時に濃い青 */
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
        }

        form {
            display: inline-block;
        }

        footer {
            margin-top: 50px;
            font-size: 0.8em;
            color: #888;
        }
    </style>
</head>

<body>

    <h1>以下の10個の文章を記憶してください</h1>

    <div id="passageDisplay"></div>

    <!-- randomNumber、testType、およびtestIdをquestions.phpにPOSTするためのフォーム -->
    <form id="questionsForm" action="questions.php" method="POST">
        <input type="hidden" name="randomNumber" value="<?php echo htmlspecialchars($randomNumber, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="testType" value="<?php echo htmlspecialchars($testType, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="testId" value="<?php echo htmlspecialchars($testId, ENT_QUOTES, 'UTF-8'); ?>"> <!-- testIdを追加 -->
    </form>

    <script>
        // PHPから取得したpassageのリストをJavaScriptに渡す
        const passages = <?php echo json_encode(array_column($results, 'passage')); ?>;
        
        let index = 0;
        let intervalId;

        function displayPassage() {
            // passageを表示する
            document.getElementById('passageDisplay').textContent = passages[index];

            // インデックスを更新し、passageIdが10になったらストップして表示を消す
            index++;
            if (index >= passages.length) {
                clearInterval(intervalId); // setIntervalを停止
                document.getElementById('passageDisplay').textContent = '';  // 表示を消す

                // フォームを自動的に送信してquestions.phpにPOST
                document.getElementById('questionsForm').submit();
            }
        }

        // 3秒ごとにpassageを表示する
        if (passages.length > 0) {
            displayPassage();  // 初回表示
            intervalId = setInterval(displayPassage, 3000);  // 3秒間隔で次のpassageを表示
        } else {
            document.getElementById('passageDisplay').textContent = '該当するデータが見つかりませんでした。';
        }
    </script>

    <footer>
        <p>&copy; 2024 記憶特性診断. All rights reserved.</p>
    </footer>

</body>

</html>
