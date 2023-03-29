<?php
require('../DBconnect.php');
session_start();
if(!isset($_SESSION['teachernumber'])){
    header('Location: ../Login/Login.php');
    exit;
}
if(isset($_GET['jugyoid'])){
    $check = $db->prepare('SELECT COUNT(*) AS cnt FROM video WHERE jugyoid=?');
    $check->execute(array($_GET['jugyoid']));
    $check = $check->fetch();
    if($check['cnt'] >= 1){
        $title = $db->prepare('SELECT * FROM video WHERE jugyoid=?');
        $title->execute(array($_GET['jugyoid']));
        $title = $title->fetch();
    }else{
        header('Location: ../Home/Home.php');
        exit;
    }

    $questions = $db->prepare('SELECT * FROM question WHERE jugyoid=?');
    $questions->execute(array($_GET['jugyoid']));

    $questioncount = $db->prepare("SELECT COUNT(*) AS cnt FROM question WHERE jugyoid=?");
    $questioncount->execute(array($_GET['jugyoid']));
    $questioncount = $questioncount->fetch();

    $nullcount = $db->prepare("SELECT COUNT(*) AS cnt FROM question WHERE answer IS NULL AND jugyoid=?");
    $nullcount->execute(array($_GET['jugyoid']));
    $nullcount = $nullcount->fetch();
}else{
    header('Location: ../Home/Home.php');
    exit;
}

if(!empty($_POST)){
    $updata = $db->prepare('UPDATE question SET teachernumber=?, answer=?, answertime=NOW() WHERE questionid=?');
    $updata->execute(array($_SESSION['teachernumber'], $_POST['text'], $_POST['questionid']));
    header('Location: ./QuestionAnswer.php?jugyoid='. $_GET['jugyoid']);
    exit;
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
    <link rel="stylesheet" href="../hanbagu.css">
    <link rel="stylesheet" href="./QuestionAnswer.css">
    <link rel="stylesheet" href="../reloadbutton.css">
    <title>質問と回答一覧</title>
</head>
<body>
    <div class="header">
        <a href="../TeacherHome/Home.php" id="returnbutton"><i class="fa-solid fa-arrow-left"></i></a>
        <span class="headeritem1">「<?php echo $title['title'];?>」の質問と回答一覧</span>
        <a href="javascript: load()" id="reloadbutton"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        <?php include("../nav.php");?>
    </div>
    <div class="countbox">
        <span class="counttitle">質問数: <?php echo $questioncount['cnt'];?>件</span><br>
        <span class="counttitle">未回答数: <?php echo $nullcount['cnt'];?>件</span>
    </div>
    <div class="box">
        <?php foreach($questions as $index):?>
            <?php
            $username = $db->prepare('SELECT * FROM account WHERE studentnumber=?');
            $username->execute(array($index['studentnumber']));
            $username = $username->fetch();
            ?>
            <div class="questionbox">
                <span class="studentnumber">学籍番号: <?php echo h($index['studentnumber']);?></span>
                <span class="username">氏名: <?php echo h($username['username']);?></span><br>
                <span class="questiontitle">質問:</span>
                <span class="question"><?php echo h($index['question']);?></span><br>
                <span class="time"><?php echo h($index['questiontime']);?></span>
            </div>
            <div class="answerbox">
                <span class="answertitle">回答: </span>
                <?php if($index['answer'] == NULL):?>
                    <span class="answer notanswer">まだ回答が入力されていません</span>
                    <form action="" method="POST" id="questionform" onsubmit="return answersubmitcheck()">
                        <input type="hidden" name="questionid" value="<?php echo h($index['questionid']);?>">
                        <textarea id="typequestion" name="text" maxlength="500" placeholder="回答を入力" required></textarea><br>
                        <button id="questionsubmit" type="submit"><i class="fa-solid fa-arrow-right"></i></button>
                    </form>
                <?php else:?>
                    <span class="answer"><?php echo h($index['answer']);?></span>
                    <span class="time"><?php echo h($index['answertime']);?></span>
                    <?php if($_SESSION['teachernumber'] == $index['teachernumber']):?>
                        <span class="deletebutton"><a onclick="return deletecheck()" href="./DeleteQuestion.php?questionid=<?php echo h($index['questionid']);?>">削除する</a></span>
                    <?php endif;?>
                <?php endif;?>
            </div>
        <?php endforeach;?>
    </div>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="../hanbagu.js"></script>
    <script>
        'use strict';

        function answersubmitcheck(){
            return confirm("回答を投稿しますか?");
        }
        
        function deletecheck(){
            return confirm("本当に回答を削除しますか?");
        }

        function load(){
            location.reload();
        }
    </script>
</body>
</html>