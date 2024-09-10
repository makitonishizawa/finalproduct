<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>main menu</title>
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
            width: 200px; /* ボタンの横幅を統一 */
        }

        button:hover {
            background-color: #0056b3; /* ホバー時により濃い青に */
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
        }

        form {
            display: inline-block;
        }

        .explanation {
            display: flex;
            flex-direction: column;
            align-items: center;
            line-height: 1.2; /* 行間を狭く */
        }

        /* フォームが中央に配置されるように */
        div.container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        footer {
            margin-top: 50px;
            font-size: 0.8em;
            color: #888;
        }
    </style>
</head>

<body>

    <h1>記憶特性測定テスト</h1>

    <div class="explanation">
        <p>このテストはあなたの記憶特性を測定するテストです。</p>
        <p>10個の文章が表示または読み上げられますので、内容を記憶し、その後表示される問題に解答してください。</p>
        <p>問題は全部で〇〇セットあります。</p>
    </div>

    <div class="container">
        <!-- 視覚スタートボタン -->
        <form id="randomNumberFormVisual" action="passage.php" method="POST">
            <input type="hidden" id="randomNumberInputVisual" name="randomNumber">
            <input type="hidden" id="testTypeVisual" name="testType">
            <button type="button" onclick="generateRandomNumberForVisual()">視覚スタート</button>
        </form>

        <!-- 聴覚スタートボタン -->
        <form id="randomNumberFormAudio" action="passage2.php" method="POST">
            <input type="hidden" id="randomNumberInputAudio" name="randomNumber">
            <input type="hidden" id="testTypeAudio" name="testType">
            <button type="button" onclick="generateRandomNumberForAudio()">聴覚スタート</button>
        </form>

        <!-- results.phpへの遷移ボタン -->
        <button onclick="location.href='results.php'">結果を見る</button>
    </div>

    <footer>
        <p>&copy; 2024 〇〇〇〇. All rights reserved.</p>
    </footer>

    <script>
        // 視覚用のランダム数値生成とpassage.phpへのPOST
        function generateRandomNumberForVisual() {
            const randomNumber = Math.floor(Math.random() * 5) + 1;
            document.getElementById('randomNumberInputVisual').value = randomNumber;
            document.getElementById('testTypeVisual').value = 1; // testType 1 (視覚テスト)
            document.getElementById('randomNumberFormVisual').submit(); // passage.php へPOST
        }

        // 聴覚用のランダム数値生成とpassage2.phpへのPOST
        function generateRandomNumberForAudio() {
            const randomNumber = Math.floor(Math.random() * 5) + 1;
            document.getElementById('randomNumberInputAudio').value = randomNumber;
            document.getElementById('testTypeAudio').value = 2; // testType 2 (聴覚テスト)
            document.getElementById('randomNumberFormAudio').submit(); // passage2.php へPOST
        }
    </script>

</body>

</html>
