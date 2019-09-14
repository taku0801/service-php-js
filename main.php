<?php

error_reporting(E_ALL); //E_STRICTレベル以外のエラーを報告する
ini_set('display_errors','On'); //画面にエラーを表示させるか

//1.post送信されていた場合
if(!empty($_POST)){

  //エラーメッセージを定数に設定
  define('MSG01','入力必須です');
  define('MSG02', 'Emailの形式で入力してください');
  define('MSG03','パスワード（再入力）が合っていません');
  define('MSG04','半角英数字のみご利用いただけます');
  define('MSG05','6文字以上で入力してください');

  //配列$err_msgを用意
  $err_msg = array();

  //2.フォームが入力されていない場合
  if(empty($_POST['email'])){

    $err_msg['email'] = MSG01;

  }
  if(empty($_POST['pass'])){

    $err_msg['pass'] = MSG01;

  }
  if(empty($_POST['pass_retype'])){

    $err_msg['pass_retype'] = MSG01;

  }

  if(empty($err_msg)){

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_retype'];

    //3.emailの形式でない場合
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)){
      $err_msg['email'] = MSG02;
    }

    //4.パスワードとパスワード再入力が合っていない場合
    if($pass !== $pass_re){
      $err_msg['pass'] = MSG03;
    }

    if(empty($err_msg)){

      //5.パスワードとパスワード再入力が半角英数字でない場合
      if(!preg_match("/^[a-zA-Z0-9]+$/", $pass)){
        $err_msg['pass'] = MSG04;

      }elseif(mb_strlen($pass) < 6){
      //6.パスワードとパスワード再入力が6文字以上でない場合

        $err_msg['pass'] = MSG05;
      }

      if(empty($err_msg)){

        //DBへの接続準備
        $dsn = 'mysql:dbname=php_sample01;host=localhost;charset=utf8';
        $user = 'root';
        $password = 'root';
        $options = array(
                // SQL実行失敗時に例外をスロー
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // デフォルトフェッチモードを連想配列形式に設定
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
                // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            );

        // PDOオブジェクト生成（DBへ接続）
        $dbh = new PDO($dsn, $user, $password, $options);

        //SQL文（クエリー作成）
        $stmt = $dbh->prepare('INSERT INTO users (email,pass,login_time) VALUES (:email,:pass,:login_time)');

        //プレースホルダに値をセットし、SQL文を実行
        $stmt->execute(array(':email' => $email, ':pass' => $pass, ':login_time' => date('Y-m-d H:i:s')));

        header("Location:mypage.php"); //マイページへ
      }

    }
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>cloudsales</title>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>
  <header>
    <div class="container">
      <div class="header-left">
        <img class="logo" src="img/logo.png">
      </div>
      <div class="header-right">
        <div class="login" id="login-show"><a href="login.php">ログイン</a></div>
      </div>
    </div>
  </header>
  <div class="signup-modal-wrapper" id="signup-modal">
    <div class="modal">
      <div class="close-modal">
        <i class="fa fa-2x fa-times"></i>
      </div>
      <div id="signup-form">
        <h2>Emailで新規登録</h2>
        <form action="#" method="post">
          <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></span></br>
          <input class="form-control" type="text" name="email" placeholder="メールアドレス" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>"></br>
          <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span></br>
          <input class="form-control" type="password" name="pass" placeholder="パスワード(半角英数字6文字以上)" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>"></br>
          <span class="err_msg"><?php if(!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype']; ?></span></br>
          <input class="form-control" type="password" name="pass_retype" placeholder="パスワードを再入力してください" value="<?php if(!empty($_POST['pass_retype'])) echo $_POST['pass_retype'];?>">
          <br>
          <label for="check">
          <input id="check" type="checkbox">個人情報の取り扱いについて同意する</label>

          <input type="submit" value="新規登録" id="submit-btn">
          <div ></div>
        </form>
      </div>
    </div>
  </div>
  
  <div class="top-wrapper">
    <div class="container">
      <h1>LEARN TO SALES.<br>LEARN TO BE CREATIVE.</h1>
      <p>cloudsalesはクラウド型営業学習サービスです。<br>未経験者にもやさしい動画とスライドで、実践しながらセールスを学んでいきましょう。</p>
      <div class="btn signup signup-show">新規登録はこちら</div>
      <p>or</p>

      
      <div class="btn facebook"><span class="fa fa-facebook"></span>Facebookで登録</div>
      <div class="btn twitter"><span class="fa fa-twitter"></span>Twitterで登録</div>
    </div>
  </div>
  
  <div class="faq-wrapper">
    <div class="container">
      <div class="heading">
        <h2>FAQ</h2>
      </div>
      <div class="faq">
        <ul id="faq-list">
          <li class="faq-list-item">
            <h3 class="question">cloudsalesとはなんですか？</h3>
            <span>+</span>
            <div class="answer">
              <p>cloudsalesはクラウド型営業学習サービスです。動画とスライドで、法人営業を学ぶことができます。</p>
            </div>
          </li>
          <li class="faq-list-item">
            <h3 class="question">利用料金はいくらですか？</h3>
            <span>+</span>
            <div class="answer">
              <p>プランによって異なります。詳細はお問い合わせください。</p>
            </div>
          </li>
          <li class="faq-list-item">
            <h3 class="question">導入事例はどれくらいありますか？</h3>
            <span>+</span>
            <div class="answer">
              <p>現在〇〇社でご導入いただいております（2019年7月現在）</p>
            </div>
          </li>
        </ul>

      </div>
    </div>
  </div>
  <div class="message-wrapper">
    <div class="container">
      <div class="heading">
        <h2>さぁ、あなたもcloudsalesで営業を学んでみませんか?</h2>
        <h3 id="tagline">Let's learn to sales, learn to be creative!</h3>
      </div>
      <div class="btn message signup-show">さっそく体験する</div>
    </div>
  </div>
  <footer>
    <div class="container">
      <img src="img/logo.png">
      <p>Learn to sales,Learn to be Creative.</p>
    </div>
  </footer>
  <script src="script.js"></script>
</body>
</html>
