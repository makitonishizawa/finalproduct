<?php
//funcs.phpの中の関数を呼び出し
require_once('user_funcs.php');

/**
 * [ここでやりたいこと]
 * 1. クエリパラメータの確認 = GETで取得している内容を確認する
 * 2. select.phpのPHP<?php ?>の中身をコピー、貼り付け
 * 3. SQL部分にwhereを追加
 * 4. データ取得の箇所を修正。
 */

$id = $_GET['id'];

// try {
//     $db_name = 'gs_db_class3';    //データベース名
//     $db_id   = 'root';      //アカウント名
//     $db_pw   = '';      //パスワード：MAMPは'root'
//     $db_host = 'localhost'; //DBホスト
//     $pdo = new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host, $db_id, $db_pw);
// } catch (PDOException $e) {
//     exit('DB Connection Error:' . $e->getMessage());
// }

//↓funcs.php内の関数
$pdo = db_con();

//２．データ登録SQL作成
// :idはいったん置き皿を作っている。
$stmt = $pdo->prepare('SELECT * FROM gs_bm_table WHERE id = :id;');
//:idに$idを入れる。PDO::PARAM_INTは型を指定
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

//３．データ表示
$view = '';
if ($status === false) {
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    $result = $stmt->fetch();
}

// var_dump($result);

?>
<!--
２．HTML
以下にindex.phpのHTMLをまるっと貼り付ける！
(入力項目は「登録/更新」はほぼ同じになるから)
※form要素 input type="hidden" name="id" を１項目追加（非表示項目）
※form要素 action="update.php"に変更
※input要素 value="ここに変数埋め込み"
-->

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>update</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .image-gallery {
            display: flex;
            flex-wrap: wrap; /* 画像が多い場合に折り返しを許可 */
            justify-content: flex-start; /* 画像を左寄せにする */
            gap: 10px; /* 画像の間隔を設定 */
    }
        .gallery-image {
            width: auto; /* 画像の幅を指定 */
            height: 200px; /*画像の高さを自動で調整し、縦横比を保持*/
            object-fit: contain; /*画像全体が見えるように調整 */
    }
        
        div {
            padding: 10px;
            font-size: 16px;
        }
        .navbar-default{
            background-color: #3366FF;
            border-color: #3366FF;
        }
        .nav-right{
            display: flex;
            justify-content: flex-end; /* アイテムを右端に寄せる */
            width: 100%;
        }
        .inputArea{
            width: 100%;
            margin:0 auto;
            text-align: center;
        }
        .shashin{
            margin:0 auto;
            display: flex;
            align-items: center;
            text-align: center;
            margin-left:39%;
            margin-bottom:10px;
        }


    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid nav-right">
                <div class="navbar-header"><a class="navbar-brand" href="bm_select.php">一覧</a></div>
                <div class="navbar-header"><a class="navbar-brand" href="index.php">トップ</a></div>
            </div>
        </nav>
    </header>

    <!-- method, action, 各inputのnameを確認してください。  -->
    <!-- textareはvalueは使えないのでタグ間に直接入れている -->
     <!-- idは書き変えられたくないので、type = hiddenにしている -->
    <form method="POST" action="bm_update.php" enctype="multipart/form-data">
        <div class="jumbotron">
            <fieldset>
            <div class="inputArea">
                <legend>更新</legend>
                <label>国名：
                    <select type="text" name="country" value="<?= $result['country'] ?>">
                        <option value="<?= $result['country'] ?>"><?= $result['country'] ?></option>                     
                    </select>
                </label><br>
                <label>期間：<input type="date" name="start" value="<?= $result['start'] ?>"> ~ <input type="date" name="end" value="<?= $result['end'] ?>"></label><br>
                <!-- <label>書籍URL：<input type="text" name="url" value="<?= $result['url'] ?>"></label><br> -->
                <label>評価：<input type="number" name="rate" min="1" max="5" value="<?= $result['rate'] ?>"></label><br>
                <label class="shashin" for="image">写真：<input type="file" name="image" value="<?= $result['image'] ?>"></label>
                <label>コメント：<input type="text" name="comment" value="<?= $result['comment'] ?>"></label><br>
                <input type="hidden" name="id" value="<?= $result['id'] ?>">
                <input type="submit" value="送信">
            </div>
            </fieldset>
        </div>
    </form>

    <h1>フォルダ内の画像</h1>
    <div class="image-gallery">
        <?php
        $folder = "img/{$id}/";

        if (is_dir($folder)) {
            $files = scandir($folder);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $filePath = $folder . $file;
                    if (is_file($filePath) && in_array(pathinfo($filePath, PATHINFO_EXTENSION), ['JPG', 'JPEG', 'PNG', 'GIF','jpg', 'jpeg', 'png', 'gif'])) {
                        echo '<img src="' . htmlspecialchars($filePath) . '" class="gallery-image">';
                    }
                }
        } else {
            echo "指定されたフォルダが存在しません。";
        }
        ?>
    </div>
</body>

</html>
