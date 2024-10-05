<?php
session_start();


// ログイン状態の確認（セッションにusernameがある場合）
$loggedIn = isset($_SESSION['username']);
$username = $loggedIn ? $_SESSION['username'] : null;
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
            margin-top: 80px; /* メニューバー分の余白を確保 */
        }

        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #444;
        }

        img {
            max-width: 30%; /* 画像の幅を半分に設定 */
            height: auto; /* 画像のアスペクト比を保つ */
            margin: 20px 0; /* 上下にスペースを追加 */
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

        /* ナビゲーションバーのスタイル */
        .navbar {
            background-color: #003366; /* 暗い青に変更 */
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
            background-color: #cccccc; /* ホバー時は灰色に変更 */
            color: black;
        }

        /* ユーザー名とログオフを右側に表示するためのスタイル */
        /* .navbar .right { */
            /* float: right; */
        /* } */

        /* 診断をスタートボタンの位置を右下に固定 */
        #startButton {
            position: fixed;
            right: 20px;
            bottom: 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-size: 1.2em;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        #startButton:hover {
            background-color: #0056b3;
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>

    <!-- ナビゲーションバーを追加 -->
    <div class="navbar">
        <a href="index.php">トップ</a>
        <a href="instruction.php">認知特性とは</a>
        <a href="reset_test.php">診断</a> <!-- 診断ボタンをリセットを通過してから診断ページへ -->
        <a href="resultsmenu.php">診断結果</a>

        <!-- ログイン状態によって表示を切り替え -->
        <?php if ($loggedIn): ?>
            <a class="right" href="logout.php">ログオフ</a>
            <span class="right"><?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php else: ?>
            <!-- ログイン/サインアップを表示 -->
            <a class="right" href="login.php">log on/sign up</a>
        <?php endif; ?>
    </div>

    <h1>自分の認知特性を理解する　それはより効率的に学習を進める第一歩</h1>

    <p>教育機関の方へ</ｐ>

    <p>教育機関の方へ</ｐ>

    



    <!-- スクロールしても常に右下に表示される「診断をスタート」ボタン -->
    <a href="reset_test.php">
        <button id="startButton">診断をスタート</button> <!-- 診断をスタートボタンもリセットを通過 -->
    </a>

    <footer>
        <p>&copy; 2024 記憶特性診断. All rights reserved.</p>
    </footer>

</body>

</html>
