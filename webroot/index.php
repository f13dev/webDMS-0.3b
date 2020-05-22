<?php
/**
  *  ██╗    ██╗███████╗██████╗ ██████╗ ███╗   ███╗███████╗     ██████╗    ██████╗ ██████╗ ███████╗████████╗ █████╗
  *  ██║    ██║██╔════╝██╔══██╗██╔══██╗████╗ ████║██╔════╝    ██╔═████╗   ╚════██╗██╔══██╗██╔════╝╚══██╔══╝██╔══██╗
  *  ██║ █╗ ██║█████╗  ██████╔╝██║  ██║██╔████╔██║███████╗    ██║██╔██║    █████╔╝██████╔╝█████╗     ██║   ███████║
  *  ██║███╗██║██╔══╝  ██╔══██╗██║  ██║██║╚██╔╝██║╚════██║    ████╔╝██║    ╚═══██╗██╔══██╗██╔══╝     ██║   ██╔══██║
  *  ╚███╔███╔╝███████╗██████╔╝██████╔╝██║ ╚═╝ ██║███████║    ╚██████╔╝██╗██████╔╝██████╔╝███████╗   ██║   ██║  ██║
  *   ╚══╝╚══╝ ╚══════╝╚═════╝ ╚═════╝ ╚═╝     ╚═╝╚══════╝     ╚═════╝ ╚═╝╚═════╝ ╚═════╝ ╚══════╝   ╚═╝   ╚═╝  ╚═╝
  *
  * WebDMS 0.2 beta Copyright J. Valentine (f13dev.com) 2018
  *
  * ASCII Text: http://patorjk.com/software/taag/#p=display&f=ANSI%20Shadow
  **/

  // Load the configuration file
  require_once('inc/cfg.php');
  require_once('inc/lang/en.lang.php');

// Start the session (for login)
session_name(SESSION_UNIQUE_ID);
session_start();

// Show all errors for testing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



if ($_SESSION['login'] !== true) {
  // Generate URL to retain page after login
  $url = 'login.php?';
  if (isset($_GET['page'])) $url .= 'page=' . $_GET['page'] . '&';
  if (isset($_GET['folder'])) $url .= 'folder=' . $_GET['folder'] . '&';
  if (isset($_GET['file'])) $url .= 'file=' . $_GET['file'];
  header('Location: ' . $url);
}

// Generate session token for CSRF
if (empty($_SESSION['token'])) {
  $_SESSION['token'] = bin2hex(random_bytes(32));
}
// Post was sent without token
if (isset($_POST['submit']) && !isset($_POST['token'])) {
  echo LANG_SECURITY_TOKEN_NOT_RECEIVED . '. <a href="javascript:history.back()">' . LANG_GO_BACK . '</a>.';
  exit;
}
// Token posted, but it's not valid
if (isset($_POST['token']) && !hash_equals($_SESSION['oldToken'], $_POST['token'])) {
  echo LANG_SECURITY_TOKEN_NOT_VALID . '. <a href="javascript:history.back()">' . LANG_GO_BACK . '</a>';
  exit;
}


// Load the class files
include('inc/class/category.class.php');
include('inc/class/folder.class.php');
include('inc/class/file.class.php');

// Check if the page is set
if (isset($_GET['page'])) $page=$_GET['page']; else $page='webdms';

// Check if a folder is set
if (isset($_GET['folder']) && is_numeric($_GET['folder'])) {
  // Create a new folder object
  $currentFolder = new Folder($_GET['folder']);
}

// Check if a file is set
if (isset($_GET['file']) && is_numeric($_GET['file'])) {
  // Retreive the file details from the database
  $currentFile = new File($_GET['file']);
}

// Get the users details
$statement = $dbh->prepare('SELECT first_name, last_name FROM users WHERE email=?');
$statement->bindParam(1, $_SESSION['user'], PDO::PARAM_STR);
$statement->execute();
$user=$statement->fetch();

// If no user row was returned
if (!isset($user['first_name']) && !isset($user['last_name'])) {
  echo LANG_USER_SESSION_ERROR . '. <a href="javascript:history.back()">' . LANG_GO_BACK . '</a>';
  exit;
}

// Create base HTML template
echo "<!DOCTYPE HTML>
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />
  <title>webDMS</title>
  <link rel=\"stylesheet\" href=\"inc/webdms.css\">
  <link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"favicon-16x16.png\">
  <link rel=\"icon\" type=\"image/png\" sizes=\"96x96\" href=\"favicon-96x96.png\">
  <link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"favicon-32x32.png\">
  <meta charset=\"UTF-8\">
  <meta name=\"google\" content=\"notranslate\">
  <meta http-equiv=\"Content-Language\" content=\"en\">
  <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js\"></script>
</head>
<body>
<nav>
  <img id='logo' src='favicon-32x32.png'/>
  <a href=\"?page=webdms\">WebDMS</a> - <a href=\"?page=accountDetails\">" . LANG_ACCOUNT_DETAILS . "</a> - <a href=\"logout.php\">" . LANG_LOGOUT . "</a> (" . $user['first_name'][0] . ". " . $user['last_name'] . ")<br>";

  // Breadcrumb
  echo $page;
  if (isset($currentFolder)) echo " >> " . $currentFolder->getTitle();
  if (isset($currentFile)) echo " >> " . $currentFile->getTitle();

echo "</nav>
<section id=\"container\">\n";

  // Get the file to include
  $thePage = 'inc/dms/' . $page . '.php';
  include($thePage);

echo "</section>
<footer>
  webDMS 0.3  beta &copy 2018 <a href='http://f13dev.com'>James Valentine</a><br>";
  // Count documents
  $count = $dbh->prepare('SELECT ID FROM documents');
  $count->bindParam(1, $categoryID, PDO::PARAM_INT);
  $count->execute();
  $count = $count->rowCount();
  // Find disk usage
  $folder = SITE_WEBROOT . SITE_DOCUMENTS;
  $output = shell_exec("du  $folder");
  $size = explode('/', $output);
  // Create a footer note showing number of files and disk usage
  echo $count . ' ' . LANG_DOCUMENTS . ', ' . LANG_USING . ' ' . round(trim($size[0])/1024, 2) . 'MB of ' . round(disk_free_space("/var/www")/1024/1024/1024, 2) . 'GB';
echo "</footer>
</body>
</html>";

?>

<!-- Javascript to put scroll so selected file is visible -->
<script>
  $(document).ready(function(){
    document.getElementById('focused').scrollIntoView({block: "center"});
  });
</script>

<?php
// Change the token sessions
$_SESSION['oldToken'] = $_SESSION['token'];
unset($_SESSION['token']);
