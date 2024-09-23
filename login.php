<?php
session_start();

// ログイン済みの場合はリダイレクト
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// MySQLデータベース接続設定
$host = 'localhost';
$dbname = 'gs_db_finalproduct';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("データベース接続エラー: " . $e->getMessage());
}

// サインアップ処理
$signupSuccess = '';
$signupError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ユーザー名の重複を確認
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $signupError = "このユーザー名は既に使用されています。";
    } else {
        // ユーザー登録
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        if ($stmt->execute()) {
            $signupSuccess = "登録が完了しました。ログインしてください。";
        } else {
            $signupError = "登録中にエラーが発生しました。";
        }
    }
}

// ログイン処理
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ユーザー名で検索
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // パスワードを検証
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");  // ログイン後のダッシュボードへリダイレクト
        exit();
    } else {
        $loginError = "ユーザー名またはパスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン・サインアップ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            width: 400px;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            margin: 10px 0;
        }

        .success {
            color: green;
            margin: 10px 0;
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

        footer {
            margin-top: 50px;
            font-size: 0.8em;
            color: #888;
        }

    </style>
</head>
<body>

    <!-- ナビゲーションバーを追加 -->
    <div class="navbar">
        <a href="index.php">トップ</a>
        <a href="instruction.php">認知特性とは</a>
    </div>

<div class="container">
    <h1>ログイン</h1>
    <!-- ログインエラーメッセージの表示 -->
    <?php if (!empty($loginError)): ?>
        <div class="error"><?php echo $loginError; ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="ユーザー名" required>
        <input type="password" name="password" placeholder="パスワード" required>
        <button type="submit" name="login">ログイン</button>
    </form>

    <h1>サインアップ</h1>
    <!-- サインアップエラーメッセージの表示 -->
    <?php if (!empty($signupError)): ?>
        <div class="error"><?php echo $signupError; ?></div>
    <?php endif; ?>
    <!-- サインアップ成功メッセージの表示 -->
    <?php if (!empty($signupSuccess)): ?>
        <div class="success"><?php echo $signupSuccess; ?></div>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <input type="text" name="username" placeholder="ユーザー名" required>
        <input type="password" name="password" placeholder="パスワード" required>
        <button type="submit" name="signup">サインアップ</button>
    </form>
</div>


</body>
</html>
