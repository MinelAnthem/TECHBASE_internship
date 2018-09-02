<?php
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn,$user,$password);

$count=1;
$re=$pdo->query("SELECT * FROM mission_4");
while($result=$re->fetch()){
  
  $count++;
}
?>


<?php
$name = $_POST["名前"];
$comment = $_POST["コメント"];
$de_number = $_POST["削除対象番号"];
$edi_number = $_POST["編集対象番号"];
$password = $_POST["pass_name"];

$timestamp = time();
$date = date("Y/m/d/ H:i:s",$timestamp);//時間
?>

<?php
if( (!empty($_POST["編集対象番号"]))&& (is_numeric($_POST["編集対象番号"])) ){//入力した番号

  $re=$pdo->query("SELECT * FROM mission_4 WHERE id=$edi_number");
  $result=$re->fetch();
  $c[0] = $result['id'];
  $c[1] = $result['password'];
  $c[2] = $result['name'];
  $c[3] = $result['comment'];
     
    if(($c[0]==$edi_number) && ($_POST["pass_edit"]==$c[1]) ){
      $edi_name = $c[2];//編集する名前の取得
      $edi_comment = $c[3];//編集するコメントの取得
      $selected_number = $c[0];
      echo "編集したい内容を入力してください。<br>";
    }
    else if(($c[0]==$edi_number) &&($_POST["pass_edit"]!=$c[1])){
      echo "パスワードが違います。<br>";
    }
    else if($c[0]!=$edi_number){
      echo "該当するレコードが見つかりませんでした。<br>";
    }
}
?>

<!DOCTYPE html>
<html lang = "ja">
<html>
<head>
<meta charset = "UTF-8">
</head>
<body>
<div>

<form action = "mission_4.php" method = "post">

<input type = "text" name = "名前" value = "<?php echo $edi_name;?>" placeholder = "名前">
<br>
<input type = "text" name = "コメント" value = "<?php echo $edi_comment;?>" placeholder = "コメント" >
<br>
<input type = "test" name = "pass_name" placeholder = "パスワード" >
<input type = "submit" value = "送信"/>
<br>

<input type = "hidden" name = "編集指定番号" value = "<?php echo $selected_number; ?>" >
<br>

<input type = "text" name = "削除対象番号" placeholder = "削除対象番号">
<input type ="submit" value = "削除"/>
<br>
<input type = "text" name = "pass_delete" placeholder = "パスワード">
<br>

<br>
<input type = "text" name = "編集対象番号" placeholder = "編集対象番号"/>
<input type = "submit" value = "編集"/>
<br>
<input type = "test" name = "pass_edit" placeholder = "パスワード">

</form>
</div>
<body>
</html>

<?php


if( !empty($_POST["編集指定番号"]) ){
  //ファイルを編集
  $editting_number = $_POST["編集指定番号"];
  $sql_edi = "update mission_4 set name='$name',comment='$comment'where id=$editting_number";
  $result = $pdo->query($sql_edi);

  $sql_sel = 'SELECT * FROM mission_4 ORDER BY id';//表示
  $edi_result = $pdo -> query($sql_sel);
  foreach($edi_result as $row){
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['time'].'<br>';
  }
}
else if( (!empty($_POST["名前"])) && (!empty($_POST["コメント"])) && (!empty($_POST["pass_name"])) ){
  //書きこみ・新規投稿
  $sql = $pdo -> prepare("INSERT INTO mission_4(id,password,name,comment,time)VALUES('$count',:password,:name,:comment,:time)");
  $sql -> bindParam(':password',$password,PDO::PARAM_STR);
  $sql -> bindParam(':name',$name,PDO::PARAM_STR);
  $sql -> bindParam(':comment',$comment,PDO::PARAM_STR);
  $sql -> bindParam(':time',$date,PDO::PARAM_STR);

  $sql ->execute();

  $re=$pdo->query('SELECT * FROM mission_4 ORDER BY id');
  while($results = $re->fetch()){
   
    echo $results['id'].',';
    echo $results['name'].',';
    echo $results['comment'].',';
    echo $results['time'].'<br>';
  }
}
  else if( (!empty($_POST["名前"])) && (!empty($_POST["コメント"])) ){
   echo "パスワードを入力してください";
}
  else if( ( (empty($_POST["名前"])) || (empty($_POST["コメント"])) ) && (!empty($_POST["pass_name"])) ){
    echo "名前とコメントを入力してください";
}

if( (empty($_POST["名前"])) && (empty($_POST["コメント"])) && (empty($_POST["pass_name"]))&&(empty($_POST["削除対象番号"]))&&(empty($_POST["pass_delete"]))&&(empty($_POST["編集指定番号"]))&& (empty($_POST["編集対象番号"])) ){
    echo "新規投稿の場合：名前、コメント、設定するパスワードを入力してください。<br>";
    echo "編集の場合：編集したい投稿の番号とパスワードを入力ししてください。<br>";
    echo "削除の場合：削除したい投稿の番号とパスワードを入力してください。<br>";
}
?>


<?php
if ( (!empty($_POST["削除対象番号"]))&&(!empty($_POST["pass_delete"]))&& (is_numeric($_POST["削除対象番号"])) ){
//削除
   $re=$pdo->query("SELECT * FROM mission_4 WHERE id=$de_number");
  $results=$re->fetch();
  $c[0] = $results['id'];
  $c[1] = $results['password'];

  if( ($_POST["pass_delete"] == $c[1]) && ($de_number==$c[0]) ){
      $id=$de_number;
      $sql = "delete from mission_4 where id=$id";
      $result = $pdo->query($sql);
  }
  else if( ($_POST["pass_delete"] != $c[1]) && ($de_number==$c[0]) ){
      echo "パスワードが違います。<br>";
  }
  else if($de_number != $c[0]){
    echo "該当するレコードが見つかりませんでした。<br>";
  }

  $re = $pdo->query('SELECT * FROM mission_4 ORDER BY id');
  while($results=$re->fetch()){
    echo $results['id'].',';
    echo $results['name'].',';
    echo $results['comment'].',';
    echo $results['time'].'<br>';
  }
}
?>
