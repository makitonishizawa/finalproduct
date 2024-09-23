<?php

session_start();

//セキュリティのため、クロスサイトスクリプティングを防ぐため、文字列化の関数作る
//セキュリティについてはIPA(情報処理推進機構）のサイトを参照するのがよい。もしくは「とくまるぼん」
//funcs.phpに関数が保存されているので、それを呼び出す


require_once('funcs.php');


//1.  DB接続します
//intert.ppからコピペ
try {
  //ID:'root', Password: xamppは 空白 ''
  $pdo = new PDO('mysql:dbname=gsa-deploy_php_kadai5;charset=utf8;host=mysql647.db.sakura.ne.jp', 'gsa-deploy', 'php_kadai2');
} catch (PDOException $e) {
  exit('DBConnectError:'.$e->getMessage());
}

//２．データ取得SQL作成
$stmt = $pdo->prepare("SELECT * FROM gs_bm_table");
$status = $stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);


//３．データ表示
$view="";
$table="";
if ($status==false) {
    //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("ErrorQuery:".$error[2]);

}else{
  //Selectデータの数だけ自動でループしてくれる
  //FETCH_ASSOC=http://php.net/manual/ja/pdostatement.fetch.php
  //".="は上書きせずに追記することができる。
  //h関数で文字列化している
  // while( $result = $stmt->fetch(PDO::FETCH_ASSOC)){
  //   $view .= '<p>';
  //   $view .= h($result['date']) . ' ' . h($result['name']) . ' ' . h($result['url']) . ' ' . h($result['comment']); 
  //   $view .= '</p>';
  
    // $viewDate = h($result['date']);
    // $viewName = h($result['name']);
    // $viewUrl = h($result['url']);
    // $viewComment = h($result['comment']);
  
  // }
    // HTMLテーブルの開始タグ
    $table .= '<table border="1" class="table">';
    $table .= '<thead><tr><th>ID</th><th>国</th><th>期間</th><th>位置</th><th>写真フォルダ</th><th>評価</th><th>コメント</th><th>編集・写真</th></tr></thead>';

    // データベースから取得した各行をテーブル行として追加
    foreach ($results as $result) {
      $table .= '<tbody><tr>';
      $table .= '<td class="id">' . htmlspecialchars($result['id']) . '</td>';
      $table .= '<td class="country">' . htmlspecialchars($result['country']) . '</td>';
      $table .= '<td class="period">' . htmlspecialchars($result['start']) .'~'. htmlspecialchars($result['end']) .'</td>';
      $table .= '<td class="url">' . htmlspecialchars($result['url']) . '</td>';
      $imagePath = 'img/' . htmlspecialchars($result['id']) . '/';
      $table .= '<td class="image"><a href="' . $imagePath . '">' . $imagePath . '</a></td>';
      $table .= '<td class="rate">' . htmlspecialchars($result['rate']) . '</td>';
      $table .= '<td class="comment">' . htmlspecialchars($result['comment']) . '</td>';
      $table .= '<td><a href="bm_detail.php?id=' . htmlspecialchars($result['id']) . '">更新・写真</a> | ' .
              '<a href="bm_delete.php?id=' . htmlspecialchars($result['id']) . '" onclick="return confirm(\'本当に削除しますか？\')">削除</a></td>';
      //$table .= '<td class="date">' . htmlspecialchars($result['date']) . '</td>';
      $table .= '</tr></tbody>';
    }
    // HTMLテーブルの終了タグ
    $table .= '</table>';

}

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>一覧</title>
<link rel="stylesheet" href="css/range.css">
<link href="css/bootstrap.min.css" rel="stylesheet">
<style>div{padding: 10px;font-size:16px;}
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 18px;
        text-align: left;
        table-layout: fixed;
        word-wrap: break-word;
      }

    th, td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
      }
      th {
        background-color: #003366;
        color:white;
      }
      .container {
        max-width: 1200px;
        margin: auto;
      }
      .navbar {
        margin-bottom: 20px;
      }

      #map {
        height: 800px; /* 地図の高さを設定 */
        width: 98%;  /* 地図の幅を設定 */
        margin:0% auto;
      }
      .navbar-default{
        background-color: #3366FF;
        border-color:#3366FF;
      }
      .nav-right{
        display: flex;
        justify-content: flex-end; /* アイテムを右端に寄せる */
        width: 100%;
      }
      .newRegister{
        display: flex;
        justify-content: center; /* 水平方向に中央揃え */
      }
      .newRegister button {
        background-color: #003366; /* 緑色の背景 */
        color: white; /* 白色のテキスト */
        border: none; /* ボーダーなし */
        padding: 10px 20px; /* 上下10px、左右20pxのパディング */
        text-align: center; /* テキストを中央揃え */
        text-decoration: none; /* テキストの下線を消す */
        display: inline-block; /* インラインブロック要素として表示 */
        font-size: 16px; /* フォントサイズ */
        cursor: pointer; /* カーソルをポインターに */
        border-radius: 5px; /* 角の丸み */
        transition: background-color 0.3s; /* 背景色の変化を滑らかに */
      }

      .newRegister button:hover {
        background-color: #000066; /* ホバー時に背景色を濃い緑色に変更 */
      }


</style>
</head>
<body id="main" onload="initMap()">
<!-- Head[Start] -->
<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid nav-right">
      <div class="navbar-header">
      <!-- <a class="navbar-brand" href="bm_select2.php">編集</a> -->
      <!-- <a class="navbar-brand" href="bm_index.php">新規登録</a> -->
      <a class="navbar-brand" href="index.php">トップ</a>
      </div>
    </div>
  </nav>
</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<!-- <div>
    <div class="container jumbotron"><?= $view ?></div>
</div> -->

<div class="newRegister">
  <button onclick="location.href='bm_index.php';">新しい国を追加</button>
</div>
<div>
  <div class="table"><?= $table ?></div>
</div>


<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDY1AbFC0KarZrysvfWBknwJcFDx5dTXnI&callback=initMap">
</script>


<script>
function initMap() {
    var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 20, lng: 139.7670516},
        zoom: 2.45
    });

    var locations = <?php echo json_encode($results); ?>;
    console.log(locations);  // ここでPHPからのデータ全体を確認

    locations.forEach(function(result) {
        console.log('Country: ' + result.country);  // 各国の名前をログに出力
        if (result.url && result.country) {  // URLと国名が存在する場合のみ処理を行う
            var location = JSON.parse(result.url);
            console.log('Parsed Location: ', location);  // パースした位置情報を出力

            var marker = new google.maps.Marker({
                position: {lat: location.lat, lng: location.lng},
                map: map,
                title: result.country  // 国名をマーカーのタイトルとして設定
            });

            // InfoWindow の作成
            var infoWindow = new google.maps.InfoWindow({
                content: '<div><strong>' + result.country + '</strong></div>' // 表示したい内容
            });

            // マーカーをクリックしたら InfoWindow を表示
            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });

            // マップがロードされたときに InfoWindow を開く
            infoWindow.open(map, marker);


        }
    });
}
</script>

<div id="map"></div> <!-- 地図を表示するためのdivタグ -->

<!-- Main[End] -->

</body>
</html>
