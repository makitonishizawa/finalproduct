<?php
session_start();

// MySQLデータベース接続設定
$host = 'localhost';  // データベースホスト
$dbname = 'gs_db_finalproduct';  // データベース名
$user = 'root';  // ユーザー名
$password = '';  // パスワード（必要に応じて設定）

// POSTデータからrandomNumberとtestTypeを取得
$randomNumber = isset($_POST['randomNumber']) ? $_POST['randomNumber'] : null;
$testType = isset($_POST['testType']) ? $_POST['testType'] : null;

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. questionsテーブルからrandomNumberに対応するdescriptionを取得
    $stmt = $pdo->prepare("SELECT questionId, description FROM questions WHERE id = :randomNumber");
    $stmt->bindParam(':randomNumber', $randomNumber, PDO::PARAM_INT);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. solutionsテーブルから正解情報を取得
    $solutionStmt = $pdo->prepare("SELECT solutionId FROM solutions WHERE questionId = :questionId");

    // 3. optionsテーブルから各questionIdに対応する4つのdescriptionを取得
    $optionsStmt = $pdo->prepare("SELECT optionId, description FROM options WHERE questionId = :questionId LIMIT 4");

} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Questions</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background-color: #ffffff;
            color: #333;
            text-align: center;
            padding: 50px;
        }

        h1, h3 {
            font-size: 2em;
            color: #444;
            margin-bottom: 20px;
        }

        #questionDisplay {
            font-size: 1.5em;
            margin: 20px 0;
            padding: 20px;
            background-color: #f1f1f1;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            margin: 10px;
            display: inline-block; /* ボタンをインラインブロックに設定 */
        }

        button:hover {
            background-color: #0056b3;
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
        }

        .options-container {
            display: flex;
            justify-content: center; /* 横方向に中央揃え */
            gap: 15px; /* ボタン間のスペース */
            flex-wrap: wrap; /* ボタンが画面外にはみ出さないように折り返す */
        }

        .result {
            margin-top: 20px;
            font-size: 1.5em;
            color: #333;
        }

        footer {
            margin-top: 50px;
            font-size: 0.8em;
            color: #888;
        }
    </style>
</head>

<body>

<h1>質問に答えてください</h1>

<div id="questionDisplay"></div>
<div id="resultDisplay" class="result"></div>

<script>
    const questions = <?php echo json_encode($questions); ?>;
    let currentQuestionIndex = 0;
    let correctSolutionId;
    let correctAnswers = 0;
    const testType = <?php echo json_encode($testType); ?>;  // testType を取得

    // 次の質問を表示
    function displayNextQuestion() {
        if (currentQuestionIndex >= questions.length) {
            displayResults();  // 正解率を表示
            return;
        }

        const currentQuestion = questions[currentQuestionIndex];
        const questionId = currentQuestion.questionId;
        const questionDescription = currentQuestion.description;
        document.getElementById('resultDisplay').textContent = ''; // 結果表示をクリア

        // 問題の表示
        document.getElementById('questionDisplay').innerHTML = `<h3>${questionDescription}</h3>`;

        // 選択肢を取得して表示
        fetchOptions(questionId);
    }

    // 選択肢を取得して表示する関数
    function fetchOptions(questionId) {
        fetch('get_options.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `questionId=${questionId}`
        })
        .then(response => response.json())
        .then(data => {
            correctSolutionId = data.correctSolutionId;
            const options = data.options;
            let buttonsHtml = '<div class="options-container">';
            options.forEach(option => {
                buttonsHtml += `<button onclick="checkAnswer(${option.optionId})">${option.description}</button>`;
            });
            buttonsHtml += '</div>';
            document.getElementById('questionDisplay').innerHTML += buttonsHtml;
        });
    }

    // 回答をチェックする関数
    function checkAnswer(selectedOptionId) {
        const resultDisplay = document.getElementById('resultDisplay');
        if (selectedOptionId == correctSolutionId) {
            resultDisplay.textContent = '正解！';
            correctAnswers++;  // 正解数をカウント
        } else {
            resultDisplay.textContent = '不正解！';
        }

        // 次の質問に進む
        currentQuestionIndex++;
        setTimeout(displayNextQuestion, 2000); // 2秒後に次の質問を表示
    }

    // 正解率を表示し、データベースに結果を保存する関数
    function displayResults() {
        const totalQuestions = questions.length;
        const correctRate = (correctAnswers / totalQuestions) * 100;

        // 正解率を表示
        document.getElementById('questionDisplay').innerHTML = `
            <h3>正解率: ${correctRate.toFixed(2)}%</h3>
            <button onclick="location.href='index.php'">トップに戻る</button>
        `;
        document.getElementById('resultDisplay').textContent = '';  // 結果表示をクリア

        // 正解率をデータベースに保存するためのリクエストを送信
        fetch('save_results.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `userId=makito&test=${testType}&results=${correctRate.toFixed(2)}`
        });
    }

    // 最初の質問を表示
    displayNextQuestion();
</script>

<footer>
    <p>&copy; 2024 Questions. All rights reserved.</p>
</footer>

</body>

</html>
