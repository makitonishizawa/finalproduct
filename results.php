<?php
session_start();

// MySQLデータベース接続設定
$host = 'localhost';  // データベースホスト
$dbname = 'gs_db_finalproduct';  // データベース名
$user = 'root';  // ユーザー名
$password = '';  // パスワード（必要に応じて設定）

try {
    // データベースに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // test = 1（視覚テスト）の平均resultsを取得
    $stmt1 = $pdo->prepare("SELECT AVG(results) as avg_visual FROM results WHERE test = 1");
    $stmt1->execute();
    $avg_visual = $stmt1->fetch(PDO::FETCH_ASSOC)['avg_visual'] / 100;  // 100で割る

    // test = 2（聴覚テスト）の平均resultsを取得
    $stmt2 = $pdo->prepare("SELECT AVG(results) as avg_audio FROM results WHERE test = 2");
    $stmt2->execute();
    $avg_audio = $stmt2->fetch(PDO::FETCH_ASSOC)['avg_audio'] / 100;  // 100で割る

} catch (PDOException $e) {
    echo 'データベース接続エラー: ' . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Test Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.jsの読み込み -->
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

        h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
            color: #444;
        }

        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 50px; /* チャート間のスペース */
        }

        .chart-box {
            width: 400px;
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
            margin: 20px;
        }

        button:hover {
            background-color: #0056b3;
            box-shadow: 0px 6px 8px rgba(0, 0, 0, 0.15);
        }

    </style>
</head>

<body>

    <h1>テスト別平均正解率</h1>

    <div class="chart-container">
        <!-- 視覚テスト パイチャート -->
        <div class="chart-box">
            <h2>視覚テスト</h2>
            <canvas id="visualChart" width="400" height="400"></canvas>
        </div>

        <!-- 聴覚テスト パイチャート -->
        <div class="chart-box">
            <h2>聴覚テスト</h2>
            <canvas id="audioChart" width="400" height="400"></canvas>
        </div>
    </div>

    <!-- index.phpに戻るボタン -->
    <button onclick="location.href='index.php'">トップに戻る</button>

    <script>
        // PHPから取得した平均値をJavaScriptに渡す
        const avgVisual = <?php echo json_encode($avg_visual); ?>;
        const avgAudio = <?php echo json_encode($avg_audio); ?>;

        const ctxVisual = document.getElementById('visualChart').getContext('2d');
        const ctxAudio = document.getElementById('audioChart').getContext('2d');

        // 視覚テストのパイチャートを描画
        const visualChart = new Chart(ctxVisual, {
            type: 'pie',
            data: {
                labels: ['正解率', '不正解率'],
                datasets: [{
                    label: '視覚テスト結果',
                    data: [avgVisual, 1 - avgVisual],  // 正解率と不正解率
                    backgroundColor: ['#007BFF', '#FF6384'], // 正解率は青、不正解率は赤
                    hoverBackgroundColor: ['#0056b3', '#ff4567']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + (context.raw * 100).toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });

        // 聴覚テストのパイチャートを描画
        const audioChart = new Chart(ctxAudio, {
            type: 'pie',
            data: {
                labels: ['正解率', '不正解率'],
                datasets: [{
                    label: '聴覚テスト結果',
                    data: [avgAudio, 1 - avgAudio],  // 正解率と不正解率
                    backgroundColor: ['#007BFF', '#FF6384'], // 正解率は青、不正解率は赤
                    hoverBackgroundColor: ['#0056b3', '#ff4567']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + (context.raw * 100).toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>
