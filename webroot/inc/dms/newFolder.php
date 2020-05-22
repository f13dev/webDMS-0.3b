<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

if (isset($_POST['submit'])) {
  // Session token checking
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {
      // Session token is ok, continue with
      $title=htmlspecialchars($_POST['title'], ENT_QUOTES);
      $description=htmlspecialchars($_POST['description'], ENT_QUOTES);
      $category=htmlspecialchars($_POST['category'], ENT_QUOTES);
      // Check that the folder is valid
      if ((strlen($title)) > 2 && (strlen($title)) < 65) {
        // String is between 3 and 64 characters long
        if (substr($title, -1) != ' ' && (substr($title, 0, 1) != ' ')) {
          // Check if the title exists already
          $statement = $dbh->prepare('SELECT * FROM folders WHERE title=?');
          $statement->bindParam(1, $title, PDO::PARAM_STR);
          $statement->execute();
          $row=$statement->fetch(PDO::FETCH_ASSOC);
          if (! $row) {
            // The folder doesn't exist, create it
            $statement = $dbh->prepare('INSERT INTO folders(title, category, description) VALUES(:title, :category, :description)');
            $statement->execute(array(
              'title' => $title,
              'category' => $category,
              'description' => $description
            ));
            $id = $dbh->lastInsertId();
            $redirectURL = '?page=webdms&folder=' . $id;
            header("Location: $redirectURL");
          } else {
            $titleError = LANG_FOLDER_EXISTS . ': ' . $title;
          }
        } else {
          $titleError = LANG_NO_SPACE;
        }
      } else {
        $titleError = LANG_STRING_LENGTH;
      }
    } else {
      $titleError = LANG_SECURITY_TOKEN_NOT_VALID;
  }
}
?>

<form action="?page=newFolder" method="post">
<h2><?php echo LANG_CREATE_FOLDER; ?></h2>
<?php if(isset($titleError)) echo "<div class=\"error\">$titleError</div>"; ?>
<?php echo LANG_TITLE; ?>: <input type="text" name="title" value="<?php if (isset($title)) echo $title; ?>"><br>
<?php echo LANG_DESCRIPTION; ?>: <textarea name="description"><?php if (isset($description)) echo $description; ?></textarea><br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
<?php if(!isset($category)) $category = -1; ?>
<?php echo LANG_CATEGORY; ?>: <select name="category">
  <?php
  $statement = $dbh->prepare('SELECT * FROM categories');
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $eachCategory) {
    $categoryID = $eachCategory['ID'];
    $categoryTitle = $eachCategory['title'];
    if ($category == $categoryID) {
      $selected = ' selected';
    } else {
      $selected = '';
    }
    echo 'selected = ' . $selected;
    echo '<option value="' . $categoryID . '"' . $selected . '>' . $categoryTitle . '</option>';
  }
  ?>
</select>
<input type="submit" name="submit" value="<?php echo LANG_SUBMIT; ?>">
</form>
