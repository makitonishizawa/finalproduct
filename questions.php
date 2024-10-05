<?php
session_start();

// MySQLデータベース接続設定
$host = 'localhost';
$dbname = 'gs_db_finalproduct';
$user = 'root';
$password = '';

// POSTデータの取得
$randomNumber = isset($_POST['randomNumber']) ? $_POST['randomNumber'] : null;
$testType = isset($_POST['testType']) ? $_POST['testType'] : null;
$testId = isset($_POST['testId']) ? $_POST['testId'] : null;

// セッションからusernameを取得
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

// POSTデータの確認（開発用）
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';


try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 質問を取得するSQLクエリ
    $stmt = $pdo->prepare("SELECT questionId, description FROM questions WHERE id = :randomNumber");
    $stmt->bindParam(':randomNumber', $randomNumber, PDO::PARAM_INT);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
    exit();
}

// 回答後に送信された正解数を処理する部分
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['correctAnswers'])) {
    $correctAnswers = $_POST['correctAnswers'];
    $_SESSION['correctAnswers'] = isset($_SESSION['correctAnswers']) ? $_SESSION['correctAnswers'] + $correctAnswers : $correctAnswers;

    // 結果をsave_results.phpに送信する処理
    $userId = $username;  // ユーザーIDはusername
    $results = $correctAnswers;  // そのテストの正解数を結果として保存


    // save_results.phpにPOSTして結果を保存
    // 確認のため testType と testId の内容を表示（開発用）
    // echo 'Test ID: ' . htmlspecialchars($testId, ENT_QUOTES, 'UTF-8') . '<br>';
    // echo 'Test Type: ' . htmlspecialchars($testType, ENT_QUOTES, 'UTF-8') . '<br>';
    
    ?>
    <form id="saveResultsForm" action="save_results.php" method="POST">
        <input type="hidden" name="userId" value="<?php echo htmlspecialchars($userId, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="testId" value="<?php echo htmlspecialchars($testId, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="testType" value="<?php echo htmlspecialchars($testType, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="results" value="<?php echo htmlspecialchars($results, ENT_QUOTES, 'UTF-8'); ?>">
    </form>

    <script>
        // フォームを1秒後に自動送信してsave_results.phpへ
        setTimeout(function() {
            document.getElementById('saveResultsForm').submit();
        }, 1000);  // 1秒の遅延を挿入
    </script>
   
   <?php
    exit();
}
?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Questions</title>
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

        .options-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            margin: 10px;
            display: inline-block;
        }

        button:hover {
            background-color: #0056b3;
        }

        .result {
            margin-top: 20px;
            font-size: 1.5em;
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

        // 質問の表示
        document.getElementById('questionDisplay').innerHTML = `<h3>${questionDescription}</h3>`;

        // 選択肢を取得して表示 (randomNumberを渡す)
        fetchOptions(questionId, '<?php echo htmlspecialchars($randomNumber, ENT_QUOTES, "UTF-8"); ?>');
    }

    // 選択肢を取得して表示する関数
    function fetchOptions(questionId, randomNumber) {
    fetch('get_options.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `questionId=${questionId}&randomNumber=${randomNumber}`
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

    // 正解率を表示し、次のパートに移動
    function displayResults() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        // 正解数を送信するhiddenフィールドを作成
        const correctAnswersInput = document.createElement('input');
        correctAnswersInput.type = 'hidden';
        correctAnswersInput.name = 'correctAnswers';
        correctAnswersInput.value = correctAnswers;
        form.appendChild(correctAnswersInput);

        // testIdを送信するhiddenフィールドを作成
        const testIdInput = document.createElement('input');
        testIdInput.type = 'hidden';
        testIdInput.name = 'testId';
        testIdInput.value = '<?php echo htmlspecialchars($testId, ENT_QUOTES, "UTF-8"); ?>';
        form.appendChild(testIdInput);

        // testTypeを送信するhiddenフィールドを作成
        const testTypeInput = document.createElement('input');
        testTypeInput.type = 'hidden';
        testTypeInput.name = 'testType';
        testTypeInput.value = '<?php echo htmlspecialchars($testType, ENT_QUOTES, "UTF-8"); ?>';
        form.appendChild(testTypeInput);

        document.body.appendChild(form);
        form.submit();
    }

    // 最初の質問を表示
    displayNextQuestion();
</script>

</body>
</html>
