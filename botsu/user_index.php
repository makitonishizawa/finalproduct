<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザーデータ登録</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        div {
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header"><a class="navbar-brand" href="user_select.php">ユーザー一覧</a></div>
                <div class="navbar-header"><a class="navbar-brand" href="index.php">トップ</a></div>
            </div>
        </nav>
    </header>

    <!-- method, action, 各inputのnameを確認してください。  -->
    <form method="POST" action="user_insert.php">
        <div class="jumbotron">
            <fieldset>
                <legend>ユーザー管理</legend>
                <label>名前：<input type="text" name="name"></label><br>
                <label>ユーザーID：<input type="text" name="lid"></label><br>
                <label>パスワード：<input type="text" name="lpw"></label><br>
                <label>管理者フラグ(0:管理者, 1:スーパー管理者)：<select name="kanri_flg">
                        <option value = "1">スーパー管理者</option>
                        <option value = "0">管理者</option>
                </select>
                </label><br>
                <label>入社退社フラグ(0:退社, 1:入社)：<select name="life_flg">
                        <option value = "1">入社</option>
                        <option value = "0">退社</option>
                </select>
                </label><br>
                <input type="submit" value="送信">
            </fieldset>
        </div>
    </form>
</body>

</html>
