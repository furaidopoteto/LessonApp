<?php
require('../DBconnect.php');
session_start();
if(!isset($_SESSION['studentnumber'])){
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
}else{
    header('Location: ../Home/Home.php');
    exit;
}

if(!empty($_POST)){
    $insert = $db->prepare('INSERT INTO question (studentnumber, question, jugyoid, questiontime) 
    VALUES (?, ?, ?, NOW())');
    $insert->execute(array($_SESSION['studentnumber'], $_POST['text'], $_GET['jugyoid']));
    header('Location: ./Question.php?jugyoid='. $_GET['jugyoid']);
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
    <link rel="stylesheet" href="./Question.css">
    <link rel="stylesheet" href="../reloadbutton.css">
    <title>質問と回答一覧</title>
</head>
<body>
    <div class="header">
        <a href="../Home/Home.php" id="returnbutton"><i class="fa-solid fa-arrow-left"></i></a>
        <span class="headeritem1">「<?php echo $title['title'];?>」の質問と回答一覧</span>
        <a href="javascript: load()" id="reloadbutton"><i class="fa-solid fa-arrow-rotate-right"></i></a>
        <div class="warpper">
            <div class="hanbagubutton">
                <span class="top"></span>
                <span class="center"></span>
                <span class="bottom"></span>
            </div>
            <ul class="nav">
                <li class="navlist"><?php echo h($_SESSION['studentnumber']);?>でログイン中</li>
                <li class="navlist"><a class="navlist" href="../Logout/Logout.php">ログアウト</a></li>
            </ul>
        </div>
    </div>
    <div class="box">
        <?php foreach($questions as $index):?>
            <div class="questionbox">
                <span class="questiontitle">質問:</span>
                <span class="question"><?php echo h($index['question']);?></span><br>
                <span class="time"><?php echo h($index['questiontime']);?></span>
                <?php if($_SESSION['studentnumber'] == $index['studentnumber']):?>
                    <span class="deletebutton"><a onclick="return deletecheck()" href="./DeleteQuestion.php?questionid=<?php echo h($index['questionid']);?>">削除する</a></span>
                <?php endif;?>
            </div>
            <div class="answerbox">
                <span class="answertitle">回答: </span>
                <?php if($index['answer'] == NULL):?>
                    <span class="answer notanswer">まだ回答が入力されていません</span>
                <?php else:?>
                    <span class="answer"><?php echo h($index['answer']);?></span>
                    <span class="time"><?php echo h($index['answertime']);?></span>
                <?php endif;?>
            </div>
        <?php endforeach;?>
    </div>
    <form action="" method="POST" id="questionform" onsubmit="return questionsubmitcheck()">
        <textarea id="typequestion" name="text" maxlength="500" placeholder="質問を入力" required></textarea><br>
        <button id="questionsubmit" type="submit"><i class="fa-solid fa-arrow-right"></i></button>
    </form>
    <script src="../jquery-3.5.1.min.js"></script>
    <script src="../hanbagu.js"></script>
    <script>
        'use strict';

        function questionsubmitcheck(){
            return confirm("質問を投稿しますか?");
        }
        
        function deletecheck(){
            return confirm("本当に質問を削除しますか?");
        }

        function load(){
            location.reload();
        }
    </script>
</body>
</html>