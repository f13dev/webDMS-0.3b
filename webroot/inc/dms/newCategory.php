<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

if (isset($_POST['submit'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {

    $title=htmlspecialchars($_POST['title'], ENT_QUOTES);
    // Check that the folder is valid
    if ((strlen($title)) > 2 && (strlen($title)) < 65) {
      // String is between 3 and 64 characters long
      if (substr($title, -1) != ' ' && (substr($title, 0, 1) != ' ')) {
        // Check if the title exists already
        $statement = $dbh->prepare('SELECT * FROM categories WHERE title=?');
        $statement->bindParam(1, $title, PDO::PARAM_STR);
        $statement->execute();
        $row=$statement->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
          // The folder doesn't exist, create it
          $statement = $dbh->prepare('INSERT INTO categories(title) VALUES(:title)');
          $statement->execute(array(
            'title' => $title
          ));
          $redirectURL = '?page=webdms';
          header("Location: $redirectURL");
        } else {
          $titleError = LANG_CATEGORY_EXISTS . ': ' . $title;
        }
      } else {
        $titleError = LANG_NO_SPACE;
      }
    } else {
      $titleError = LANG_STRING_LENGTH;
    }
  }
}
?>

<form action="?page=newCategory" method="post">
<h2><?php echo LANG_CREATE_CATEGORY; ?></h2>
<?php if(isset($titleError)) echo "<div class=\"error\">$titleError</div>"; ?>
<?php echo LANG_TITLE; ?>: <input type="text" name="title" value="<?php if (isset($title)) echo $title; ?>"><br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
<input type="submit" name="submit" value="<?php echo LANG_SUBMIT; ?>">
</form>
