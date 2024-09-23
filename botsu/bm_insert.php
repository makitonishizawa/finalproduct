<?php
session_start();

// funcs.phpの中の関数を呼び出し
require_once('user_funcs.php');

// POSTデータ取得
$country = $_POST['country'];
$start = $_POST['start'];
$end = $_POST['end'];
$url = $_POST['url'];
$rate = $_POST['rate'];
$comment = $_POST['comment'];
$image = '';  // 初期値の設定

// 画像アップロードの処理
if (isset($_FILES['image'])) {
    $upload_file = $_FILES['image']['tmp_name'];
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_name = uniqid() . '.' . $extension;  // 一時的な新しい名前を生成
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

// DB接続
try {
    $pdo = new PDO('mysql:dbname=gsa-deploy_php_kadai5;charset=utf8;host=mysql647.db.sakura.ne.jp', 'gsa-deploy', 'php_kadai2');
} catch (PDOException $e) {
    exit('DBConnectError:'.$e->getMessage());
}

// データ登録SQL作成
$stmt = $pdo->prepare('INSERT INTO gs_bm_table(country, start, end, url, image, rate, comment, date)
                       VALUES(:country, :start, :end, :url, :image, :rate, :comment, now())');
$stmt->bindValue(':country', $country, PDO::PARAM_STR);
$stmt->bindValue(':start', $start, PDO::PARAM_STR);
$stmt->bindValue(':end', $end, PDO::PARAM_STR);
$stmt->bindValue(':url', $locationJson, PDO::PARAM_STR);// 位置情報をJSONとしてバインド
$stmt->bindValue(':image', $new_name, PDO::PARAM_STR);
$stmt->bindValue(':rate', $rate, PDO::PARAM_INT);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
$status = $stmt->execute();

if ($status === false) {
    $error = $stmt->errorInfo();
    exit('ErrorMessage:'.$error[2]);
} else {
    $last_id = $pdo->lastInsertId(); // 挿入されたレコードのIDを取得

    // "id" 名前のフォルダをimg直下に作成（フォルダが存在しない場合のみ）
    $id_path = 'img/' . $last_id;
    if (!file_exists($id_path)) {
        mkdir($id_path, 0777, true);
    }

    // 生成したファイルの保存先パス
    $image_path = $id_path . '/' . $new_name;

    // 一時保存先から生成したファイルの保存先に移動
    if (move_uploaded_file($upload_file, $image_path)) {
        // 正しいパスで更新
        $stmt = $pdo->prepare('UPDATE gs_bm_table SET image = :image WHERE id = :id');
        $stmt->bindValue(':image', $image_path, PDO::PARAM_STR);
        $stmt->bindValue(':id', $last_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // index.phpへリダイレクト
    header('Location: bm_select.php');
}
?>