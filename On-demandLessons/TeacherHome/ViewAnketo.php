<?php
require("../DBconnect.php");
session_start();
if(!isset($_SESSION['teachernumber'])){
    header('Location: ../TeacherHome/Home.php');
    exit;
}
if(empty($_GET['order'])){
    $_GET['order'] = "desc";
}
if(isset($_GET['jugyoid'])){
    $check = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=?");
    $check->execute(array($_GET['jugyoid']));
    $check = $check->fetch();
    if($check['cnt'] >= 1){
        $title = $db->prepare("SELECT * FROM video WHERE jugyoid=?");
        $title->execute(array($_GET['jugyoid']));
        $title = $title->fetch();

        $check2 = $db->prepare("SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=? AND teachernumber=?");
        $check2->execute(array($_GET['jugyoid'], $_SESSION['teachernumber']));
        $check2 = $check2->fetch();
        if($check2['cnt'] <= 0){
            header('Location: ../TeacherHome/Home.php');
            exit;
        }
        if(!empty($_GET['order']) && $_GET['order'] == "desc"){
            $anketo = $db->prepare("SELECT *, ((item1+item2+item3+item4+item5)/5) AS itemavg FROM anketo WHERE jugyoid=? ORDER BY itemavg DESC");
            $anketo->execute(array($_GET['jugyoid']));
        }else{
            $anketo = $db->prepare("SELECT *, ((item1+item2+item3+item4+item5)/5) AS itemavg FROM anketo WHERE jugyoid=? ORDER BY itemavg");
            $anketo->execute(array($_GET['jugyoid']));
        }
        
    }else{
        header('Location: ../TeacherHome/Home.php');
        exit;
    }

    $allusercount = $db->prepare("SELECT COUNT(*) AS cnt FROM account WHERE grade=? AND gakubu=?");
    $allusercount->execute(array($title['grade'], $title['gakubu']));
    $allusercount = $allusercount->fetch();

    $anketocount = $db->prepare("SELECT COUNT(*) AS cnt FROM anketo WHERE jugyoid=?");
    $anketocount->execute(array($_GET['jugyoid']));
    $anketocount = $anketocount->fetch();
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
    <title>アンケート結果</title>
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../hanbagu.css">
    <link rel="stylesheet" href="../reloadbutton.css">
    <link rel="stylesheet" href="./ViewAnketo.css">
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="./jquery.raty.js"></script>
    <!-- 星評価ライブラリの使い方は 参照: https://kodocode.net/design-js-raty/ -->
</head>
<body>
    <div class="header">
        <a href="../TeacherHome/Home.php" id="returnbutton"><i class="fa-solid fa-arrow-left"></i></a>
        <span class="headeritem1">「<?php echo h($title['title']);?>」のアンケート結果一覧</span>
        <a href="javascript: load()" id="reloadbutton"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        <span class="headeritem1">条件: </span>
        <select name="order" id="orderselect">
            <option value="desc" <?php if(!empty($_GET['order']) && $_GET['order'] == "desc"):?> selected <?php endif;?>>評価の高い順</option>
            <option value="asc" <?php if(!empty($_GET['order']) && $_GET['order'] == "asc"):?> selected <?php endif;?>>評価の低い順</option>
        </select>
        <?php include("../nav.php");?>
    </div>
    <div class="avgbox">
        <span class="anketocount">アンケート回答数: <?php echo $anketocount['cnt'];?>/<?php echo $allusercount['cnt'];?></span>
        <div class="subbox">
                <?php
                $itemavg = $db->prepare("SELECT 
                AVG(item1) AS item1, AVG(item2) AS item2, AVG(item3) AS item3, AVG(item4) AS item4, AVG(item5) AS item5
                 FROM anketo
                 WHERE jugyoid=?");
                $itemavg->execute(array($_GET['jugyoid']));
                $itemavg = $itemavg->fetch();

                $avg = 0;
                $sum = 0;
                $itemcount = 5;
                for($i = 0;$i<$itemcount;$i++){
                    if($itemavg["item". $i+1] == null){
                        $itemavg["item". $i+1] = 0;
                    }
                    $sum += $itemavg["item". $i+1];
                }
                $avg = $sum/$itemcount;
                ?>
                
                <div class="columnbox">
                    <span class="column">質問事項</span>
                    <span class="column">評価(全評価の平均)</span>
                </div>

                <div class="columnbox">
                    <span class="column">理解度:</span>
                    <span class="column">約<?php echo sprintf("%.1f", round($itemavg['item1'], 1));?>
                        <div class="starbox" id="starbox1"></div>
                    </span>
                </div>

                <div class="columnbox">
                    <span class="column">分かりやすさ:</span>
                    <span class="column">約<?php echo sprintf("%.1f", round($itemavg['item2'], 1));?>
                        <div class="starbox" id="starbox2"></div>
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">面白さ:</span>
                    <span class="column">約<?php echo sprintf("%.1f", round($itemavg['item3'], 1));?>
                        <div class="starbox" id="starbox3"></div>
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">聞き取りやすさ:</span>
                    <span class="column">約<?php echo sprintf("%.1f", round($itemavg['item4'], 1));?>
                        <div class="starbox" id="starbox4"></div>
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">今後も同じようなやり方でいいか:</span>
                    <span class="column">約<?php echo sprintf("%.1f", round($itemavg['item5'], 1));?>
                        <div class="starbox" id="starbox5"></div>
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">全体の平均:</span>
                    <span class="column">約<?php echo round($avg, 1);?>
                        <div class="starbox" id="starbox6"></div>
                    </span>
                        
                </div>
                <script>
                    <?php for($i = 0;$i<5;$i++):?>
                        $(`#starbox<?php echo $i+1;?>`).raty({
                            readOnly: true,
                            score : <?php echo sprintf("%.1f", round($itemavg['item'. $i+1], 1));?>
                        });
                    <?php endfor;?>
                    $('#starbox6').raty({
                        readOnly: true,
                        score: <?php echo round($avg, 1);?>
                    })
                </script>
        </div>
    </div>
    <div class="box">
        <?php $boxcount = 6;?>
        <?php foreach($anketo as $index):?>
            <div class="subbox">
                <?php
                $avg = 0;
                $sum = 0;
                $itemcount = 5;
                for($i = 0;$i<$itemcount;$i++){
                    $sum += $index["item". $i+1];
                }
                $avg = $sum/$itemcount;
                ?>
                
                <div class="columnbox">
                    <span class="column">質問事項</span>
                    <span class="column">評価</span>
                </div>

                <div class="columnbox">
                    <span class="column">理解度:</span>
                    <span class="column"><?php echo $index['item1'];?>
                        <div class="starbox" id="starbox<?php echo ++$boxcount;?>"></div>
                    </span>
                </div>

                <div class="columnbox">
                    <span class="column">分かりやすさ:</span>
                    <span class="column"><?php echo $index['item2'];?>
                        <div class="starbox" id="starbox<?php echo ++$boxcount;?>"></div>
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">面白さ:</span>
                    <span class="column"><?php echo $index['item3'];?>
                        <div class="starbox" id="starbox<?php echo ++$boxcount;?>"></div>
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">聞き取りやすさ:</span>
                    <span class="column"><?php echo $index['item4'];?>
                        <div class="starbox" id="starbox<?php echo ++$boxcount;?>"></div>
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">今後も同じようなやり方でいいか:</span>
                    <span class="column"><?php echo $index['item5'];?>
                        <div class="starbox" id="starbox<?php echo ++$boxcount;?>"></div>
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">全体の平均:</span>
                    <span class="column">約<?php echo round($avg, 1);?>
                        <div class="starbox" id="starbox<?php echo ++$boxcount;?>"></div>    
                    </span>
                </div>
                    
                <div class="columnbox">
                    <span class="column">要望や質問等:</span>
                    <span class="column"><?php echo h($index['question']);?></span>
                </div>
                
            </div>
            <script>
                <?php $cnt = 0;?>
                <?php for($i = $boxcount-5;$i<$boxcount;$i++):?>
                        $(`#starbox<?php echo $i;?>`).raty({
                            readOnly: true,
                            score : <?php echo sprintf("%.1f", round($index['item'. $cnt+1], 1));?>
                        });
                        <?php $cnt++;?>
                <?php endfor;?>
                    $('#starbox<?php echo $boxcount;?>').raty({
                        readOnly: true,
                        score: <?php echo round($avg, 1);?>
                    })
            </script>
        <?php endforeach;?>
    </div>
    <script src="../hanbagu.js"></script>
    <script>
        'use strict';

        const orderselect = document.getElementById('orderselect');
        orderselect.addEventListener("change", function(){
            location.href = "./ViewAnketo.php?jugyoid="+<?php echo $_GET['jugyoid'];?>+"&order="+orderselect.value;
        })
        function load(){
            location.reload();
        }
    </script>
</body>
</html>