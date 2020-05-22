<?php

// Load the configuration file
require_once('inc/cfg.php');
require_once('inc/lang/en.lang.php');

session_name(SESSION_UNIQUE_ID);
session_start();

// Generate session token for CSRF
if (empty($_SESSION['token'])) {
  $_SESSION['token'] = bin2hex(random_bytes(32));
}
if (isset($_POST['sub'])) {
  if (!hash_equals($_SESSION['token'], $_POST['token'])) {
    echo LANG_SECURITY_TOKEN_NOT_VALID . '. <a href="javascript:history.back()">' . LANG_GO_BACK . '</a>';
    exit;
  }
}



// Create an empty error message
$errorMsg = "";
// Check if the form has been submitted
if(isset($_POST["sub"])) {
  // Check the token session is valid
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
    // Retrieve the user details from the database by the email
    $statement = $dbh->prepare('SELECT email, password FROM users WHERE email = ?');
    $statement->bindParam(1, $_POST['username'], PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetch();
    // Set validUser to true if the details are correct
    $validUser = $_POST["username"] == $result['email'] && sha1($_POST["password"]) == $result['password'];
    // If validUser = false, set an error message
    if(!$validUser) {
      $errorMsg = LANG_INVALID_USER;
    }
    // If validUser = true, set a session
    else {
      $_SESSION["login"] = true;
      $_SESSION['user'] = $result['email'];
    }
  }
}

if($validUser) {
    // User is valid, redirect them to the homepage, or the last page they were on
    $url = 'index.php?';
    if (isset($_GET['page'])) $url .= 'page=' . $_GET['page'] . '&';
    if (isset($_GET['folder'])) $url .= 'folder=' . $_GET['folder'] . '&';
    if (isset($_GET['file'])) $url .= 'file=' . $_GET['file'];
    header('Location: ' . $url); die();
}

// Check if password reset form has been submitted
if (isset($_POST['passReset'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
    // Get the users details from the db
    $statement = $dbh->prepare('SELECT email, password FROM users WHERE email = ?');
    $statement->bindParam(1, $_POST['username'], PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetch();
    // Check results are valid
    if (isset($result['email']) && isset($result['password'])) {
      // Create password reset code
      $resetCode = sha1(date('dmY') . SESSION_UNIQUE_ID . $result['email'] . $result['password']);
      // Generate an email message
      $message = LANG_EMAIL_HEAD . "\n\n";
      $message .= SITE_URL . 'reset.php?code=' . $resetCode . "\n\n";
      $message .= LANG_EXPIRE_AT . ' 23:59 on ' . date('jS F Y') . ', ' . LANG_CODE_TIMEOUT;
      // Generate email headers
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
      $headers .= "From: " . EMAIL_FROM . "\r\n";
      $headers .= "Reply-To: " . EMAIL_FROM . "\r\n";
      $headers .= "X-Mailer: PHP/" . phpversion();
      if (mail($result['email'],LANG_EMAIL_SUBJECT,$message,$headers)) {
        $errorMsg = LANG_EMAIL_SENT;
      }
      else {
        $errorMsg = LANG_EMAIL_ERROR;
      }

      //$errorMsg = $resetCode;
    }
    else {
      $errorMsg = LANG_NO_USER_FOUND . ' ' . $_POST['username'];
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <link rel="stylesheet" href="inc/webdms.css">
  <title><?php echo LANG_LOGIN; ?></title>
</head>
<body>
  <form name="input" method="post" autocomplete="off">
    <h2><?php echo LANG_LOGIN; ?></h2>
    <div class="error"><?= $errorMsg ?></div>
    <label for="username"><?php echo LANG_EMAIL; ?>:</label><input type="text" value="<?= $_POST["username"] ?>" id="username" name="username" />
    <label for="password"><?php echo LANG_PASSWORD; ?>:</label><input type="password" value="" id="password" name="password" />
    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
    <input type="submit" value="<?php echo LANG_LOGIN; ?>" name="sub" /> <input type="submit" value="<?php echo LANG_RESET_PASSWORD; ?>" name="passReset">
  </form>
</body>
</html>
