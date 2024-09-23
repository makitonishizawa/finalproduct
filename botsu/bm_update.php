<?php
session_start();
// funcs.phpの中の関数を呼び出し
require_once('user_funcs.php');

//PHP:コード記述/修正の流れ
//1. insert.phpの処理をマルっとコピー。
//2. $id = $_POST["id"]を追加
//3. SQL修正
//   "UPDATE テーブル名 SET 変更したいカラムを並べる WHERE 条件"
//   bindValueにも「id」の項目を追加
//4. header関数"Location"を「select.php」に変更

//1. POSTデータ取得
$country = $_POST['country'];
$start = $_POST['start'];
$end = $_POST['end'];
// $url = $_POST['url'];
$rate = $_POST['rate'];
$comment = $_POST['comment'];
$image = '';  // 初期値の設定
$id = $_POST['id'];

// 画像アップロードの処理
if (isset($_FILES['image'])&& $_FILES['image']['error'] == 0) {
    $upload_file = $_FILES['image']['tmp_name'];
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_name = uniqid() . '.' . $extension;  // 一時的な新しい名前を生成

            $image_path = 'img/' . $id . '/' . $new_name; // 保存先パス

            echo $id;
            
            if (!is_dir('img/' . $id)) {
                mkdir('img/' . $id, 0777, true); // ディレクトリがなければ作成
            }

            if (move_uploaded_file($upload_file, $image_path)) {
                $image = $image_path; // 新しい画像のパスを設定
            } else {
                exit('File Upload Error');
            }
        } else {
            echo $id;
            // 画像が新しくアップロードされていない場合、元の画像を保持
            // $image = $_POST['existingImage'];
        }

        try {
            $pdo = new PDO('mysql:dbname=gsa-deploy_php_kadai5;charset=utf8;host=mysql647.db.sakura.ne.jp', 'gsa-deploy', 'php_kadai2');
        } catch (PDOException $e) {
            exit('DB Connection Error:' . $e->getMessage());
}


// Google Maps APIを使用して位置情報を取得
$apiKey = 'AIzaSyDY1AbFC0KarZrysvfWBknwJcFDx5dTXnI'; // 実際のAPIキーをここに設定
$location = getLatLng($country, $apiKey);

if ($location) {
    $locationJson = json_encode($location); // 位置情報をJSON文字列に変換
    echo "緯度: " . $location['lat'] . ", 経度: " . $location['lng'];
} else {
    echo "位置情報を取得できませんでした。";
    $locationJson = json_encode([]); // 空のJSON文字列を設定
}


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
    'UPDATE gs_bm_table SET country = :country, start = :start, end = :end, image =:image, rate =:rate, comment = :comment WHERE id = :id;'
);

// 数値の場合 PDO::PARAM_INT
// 文字の場合 PDO::PARAM_STR
$stmt->bindValue(':country', $country, PDO::PARAM_STR);
$stmt->bindValue(':start', $start, PDO::PARAM_STR);
$stmt->bindValue(':end', $end, PDO::PARAM_STR);
// $stmt->bindValue(':url', $locationJson, PDO::PARAM_STR);
$stmt->bindValue(':rate', $rate, PDO::PARAM_STR);
$stmt->bindValue(':image', $image, PDO::PARAM_STR);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT); //PARAM_INTなので注意
$status = $stmt->execute(); //実行

//４．データ登録処理後
if ($status === false) {
    //*** function化する！******\
    $error = $stmt->errorInfo();
    exit('SQLError:' . print_r($error, true));
} else {


    //*** function化する！*****************
    header('Location: bm_select.php');
    exit();
}
