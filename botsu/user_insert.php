<?php
session_start();

//1. POSTデータ取得
$name   = $_POST['name'];
$lid  = $_POST['lid'];
$lpw    = $_POST['lpw'];
$kanri_flg    = $_POST['kanri_flg'];
$life_flg    = $_POST['life_flg'];


//2. DB接続します
//*** function化する！  *****************
try {
    $db_name = 'gsa-deploy_php_kadai5'; //データベース名
    $db_id   = 'gsa-deploy'; //アカウント名
    $db_pw   = 'php_kadai2'; //パスワード：MAMPは'root'
    $db_host = 'mysql647.db.sakura.ne.jp'; //DBホスト
    $pdo = new PDO('mysql:dbname=' . $db_name . ';charset=utf8;host=' . $db_host, $db_id, $db_pw);
} catch (PDOException $e) {
    exit('DB Connection Error:' . $e->getMessage());
}

//３．データ登録SQL作成
$stmt = $pdo->prepare(
    'INSERT INTO
        gs_user_table(
            name, lid, lpw ,kanri_flg, life_flg
        )
    VALUES (
            :name, :lid, :lpw, :kanri_flg, :life_flg
        );'
);

// 数値の場合 PDO::PARAM_INT
// 文字の場合 PDO::PARAM_STR
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':lid', $lid, PDO::PARAM_STR);
$stmt->bindValue(':lpw', $lpw, PDO::PARAM_STR);
$stmt->bindValue(':kanri_flg', $kanri_flg, PDO::PARAM_INT);
$stmt->bindValue(':life_flg', $life_flg, PDO::PARAM_INT);
$status = $stmt->execute(); //実行

//４．データ登録処理後
if ($status === false) {
    //*** function化する！******\
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {
    //*** function化する！*****************
    header('Location: index.php');
    exit();
}

//Indexに名前を送るためセッション関数に代入
$_SESSION[':name'] = $val[':name'];