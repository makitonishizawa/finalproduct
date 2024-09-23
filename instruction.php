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

    <h1>認知特性とは</h1>

    <div class="container">
        <p>認知特性とは、外部からの刺激をどのように認知、記憶するかの各個人の特性です</p>
        <p>例えば、聴覚による刺激を視覚による刺激よりも記憶に残る人もいますし、その逆のケースもあります</p>
        <p>認知特性は人により個人差があります</p>
        <p>認知特性を知ることにより、今後の学習を効率的に進める手助けとなります</p>

        <!-- 診断画面に戻るボタン -->
        <button onclick="location.href='testmenu.php'">登録して診断をはじめる</button>
    </div>

    <footer>
        <p>&copy; 2024 記憶診断. All rights reserved.</p>
    </footer>

</body>

</html>
