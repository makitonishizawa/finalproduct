<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Instruction</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
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
            color: #444;
        }

        p {
            font-size: 1.2em;
            line-height: 1.6;
            margin-bottom: 30px;
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
        }

        button:hover {
            background-color: #0056b3;
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
        }

        .container {
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

    <h1>はじめに</h1>

    <div class="container">
        <p>このテストはあなたの記憶特性を測定するテストです。</p>
        <p>10個の文章が表示または読み上げられますので、内容を記憶し、その後表示される問題に解答してください。</p>
        <p>問題は全部で〇〇セットあります。</p>

        <!-- トップ画面に戻るボタン -->
        <button onclick="location.href='index.php'">トップ画面に戻る</button>
    </div>

    <footer>
        <p>&copy; 2024 記憶テスト. All rights reserved.</p>
    </footer>

</body>

</html>
