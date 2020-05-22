<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

//if (isset($_POST['submit'])) {
if (isset($_POST['submit'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {

    $title=htmlspecialchars($_POST['title'], ENT_QUOTES);
    $description=htmlspecialchars($_POST['description'], ENT_QUOTES);
    $category=htmlspecialchars($_POST['category'], ENT_QUOTES);

    if (htmlspecialchars($currentFolder->getTitle(), ENT_QUOTES) == $title && htmlspecialchars($currentFolder->getDescription(), ENT_QUOTES) == $description && $currentFolder->getCategory() == $category) {
      $titleError = LANG_NOTHING_TO_UPDATE;
    } else {
      // Check that the folder is valid
      if ((strlen($title)) > 2 && (strlen($title)) < 65) {
        // String is between 3 and 64 characters long
        if (substr($title, -1) != ' ' && (substr($title, 0, 1) != ' ')) {
            // Passed test, update record
            $statement = $dbh->prepare('UPDATE folders SET title=?, category=?, description=? WHERE id=?');
            $statement->execute([$title, $category, $description, $currentFolder->getID()]);

            $redirectURL = '?page=webdms&folder=' . $currentFolder->getID();
            header("Location: $redirectURL");
        } else {
          $titleError = LANG_NO_SPACE;
        }
      } else {
        $titleError = LANG_STRING_LENGTH;
      }
    }
  }
}
?>

<form action="?page=editFolder&folder=<?php echo $currentFolder->getID(); ?>" method="post">
<?php echo '<h2>' . LANG_EDITING_FOLDER . ': ' . $currentFolder->getTitle() . '</h2>'; ?>
<?php if(isset($titleError)) echo "<div class=\"error\">$titleError</div>"; ?>
<?php echo LANG_TITLE; ?>: <input type="text" name="title" value="<?php if (isset($title)) echo $title; else echo $currentFolder->getTitle(); ?>"><br>
<?php echo LANG_DESCRIPTION; ?>: <textarea name="description"><?php if (isset($description)) echo $description; else echo $currentFolder->getDescription(); ?></textarea><br>
<?php echo LANG_CATEGORY; ?>: <select name="category">
  <?php
  $statement = $dbh->prepare('SELECT * FROM categories');
  $statement->execute();
  $result = $statement->fetchAll();
  foreach($result as $category) {
    $categoryID = $category['ID'];
    $categoryTitle = $category['title'];
    if ($currentFolder->getCategory() == $categoryID) {
      $selected = ' selected';
    } else {
      $selected = '';
    }
    echo '<option value="' . $categoryID . '"' . $selected . '>' . $categoryTitle . '</option>';
  }
  ?>
</select>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
<input type="submit" name="submit" value="<?php echo LANG_SUBMIT; ?>">
</form>
