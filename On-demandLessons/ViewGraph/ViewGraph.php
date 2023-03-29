<?php
require("../DBconnect.php");
session_start();
if(!isset($_SESSION['teachernumber'])){
    header('Location: ../TeacherHome/Home.php');
    exit;
}else{
    $anketodata = $db->prepare("SELECT a.jugyoid,
    AVG(item1) AS item1,
    AVG(item2) AS item2,
    AVG(item3) AS item3,
    AVG(item4) AS item4,
    AVG(item5) AS item5 FROM anketo a, video v WHERE v.teachernumber=? AND
     EXISTS (SELECT jugyoid FROM video WHERE a.jugyoid=v.jugyoid)
     GROUP BY a.jugyoid");
    $anketodata->execute(array($_SESSION['teachernumber']));
}
function h($value){
    return htmlspecialchars($value, ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
    <link rel="stylesheet" href="./ViewGraph.css">
    <link rel="stylesheet" href="../hanbagu.css">
    <link rel="stylesheet" href="../reloadbutton.css">
    <title>アンケート結果のグラフ</title>
</head>
<body>
    <div class="header">
        <a href="../TeacherHome/Home.php" id="returnbutton"><i class="fa-solid fa-arrow-left"></i></a>
        <span class="headeritem1">アンケート結果の推移</span>
        <a href="javascript: load()" id="reloadbutton"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        <span class="headeritem1">条件: </span>
        <select name="order" id="orderselect">
            <option value="avg" <?php if(!empty($_GET['view']) && $_GET['view'] == "avg"):?> selected <?php endif;?>>授業ごとの平均値</option>
            <option value="sum" <?php if(!empty($_GET['view']) && $_GET['view'] == "sum"):?> selected <?php endif;?>>平均値の累計</option>
        </select>
        <?php include("../nav.php");?>
    </div>
<div id="graphbox">
    <canvas id="chart"></canvas>
</div>
<script src="../jquery-3.5.1.min.js"></script>
<script src="../hanbagu.js"></script>
<script>
    <?php
        $jugyoid = [];
        $item1 = [];
        $item2 = [];
        $item3 = [];
        $item4 = [];
        $item5 = [];
        $cnt = 0;
        $farst = true;
        if(!empty($_GET['view']) && $_GET['view'] == "sum"){
            foreach($anketodata as $index){
                if($farst){
                    itempush($jugyoid, $item1, $item2, $item3, $item4, $item5, $index);
                    $farst = false;
                }else{
                    array_push($jugyoid, $index['jugyoid']);
                    array_push($item1, $index['item1']+$item1[$cnt]);
                    array_push($item2, $index['item2']+$item2[$cnt]);
                    array_push($item3, $index['item3']+$item3[$cnt]);
                    array_push($item4, $index['item4']+$item4[$cnt]);
                    array_push($item5, $index['item5']+$item5[$cnt]);
                    $cnt++;
                }
                
            }
        }else{
            foreach($anketodata as $index){
                itempush($jugyoid, $item1, $item2, $item3, $item4, $item5, $index);
            }
        }
        function itempush(&$jugyoid, &$item1, &$item2, &$item3, &$item4, &$item5, &$index){
            array_push($jugyoid, $index['jugyoid']);
            array_push($item1, $index['item1']);
            array_push($item2, $index['item2']);
            array_push($item3, $index['item3']);
            array_push($item4, $index['item4']);
            array_push($item5, $index['item5']);
        }
    ?>
    //グラフの作成 参照: https://appsol-one.com/ui/chart-js-line/
    //Chart.jsリファレンス 参照: https://misc.0o0o.org/chartjs-doc-ja/
    var ctx = document.getElementById("chart");
    var myLineChart = new Chart(ctx, {
        // グラフの種類：折れ線グラフを指定
        type: 'line',
        data: {
        // x軸の各メモリ
        labels: <?php echo json_encode($jugyoid);?>,
        datasets: [
            {
            label: '理解度',
            data: <?php echo json_encode($item1);?>,
            borderColor: "#ec4343",
            backgroundColor: "#00000000",
            lineTension:0
            },
            {
            label: '分かりやすさ',
            data: <?php echo json_encode($item2);?>,
            borderColor: "#2260ea",
            backgroundColor: "#00000000",
            lineTension:0
            },
            {
            label: '面白さ',
            data: <?php echo json_encode($item3);?>,
            borderColor: "green",
            backgroundColor: "#00000000",
            lineTension:0
            },
            {
            label: '聞き取りやすさ',
            data: <?php echo json_encode($item4);?>,
            borderColor: "purple",
            backgroundColor: "#00000000",
            lineTension:0
            },
            {
            label: '今後も同じようなやり方がいいか',
            data: <?php echo json_encode($item5);?>,
            borderColor: "orange",
            backgroundColor: "#00000000",
            lineTension:0
            }
        ],
        },
        options: {
        title: {
            display: true,
            text: 'アンケート結果の推移',
            fontSize: 30
        },
        scales: {
            yAxes: [{
            ticks: {
                suggestedMax: 5,
                suggestedMin: 0,
                stepSize: 1,  // 縦メモリのステップ数
                callback: function(value, index, values){
                return  value +  ''  // 各メモリのステップごとの表記（valueは各ステップの値）
                }
            }
            }]
        },
        legend: {
                position: "bottom",
                labels: {
                    fontSize: 20
                }
        }
        }
    });

    const orderselect = document.getElementById('orderselect');
    orderselect.addEventListener("change", function(){
        location.href = "./ViewGraph.php?view="+orderselect.value;
    })
    function load(){
        location.reload();
    }
</script>
</body>
</html>