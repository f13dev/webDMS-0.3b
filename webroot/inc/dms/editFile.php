<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

// Process form data
if (isset($_POST['submit'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
    $notes = htmlspecialchars($_POST['notes'], ENT_QUOTES);
    $date = htmlspecialchars($_POST['date'], ENT_QUOTES);
    $folder = htmlspecialchars($_POST['folder'], ENT_QUOTES);

    if (trim($title) != '') {
      // Passed test, update record
      $statement = $dbh->prepare('UPDATE documents SET title=?, notes=?, document_date=?, folder=? WHERE id=?');
      $statement->execute([$title, $notes, $date, $folder, $currentFile->getID()]);

      $redirectURL = '?page=webdms&folder=' . $currentFolder->getID() . '&file=' . $currentFile->getID();
      header("Location: $redirectURL");
    } else {
      $error = LANG_ENTER_TITLE;
    }
  }
}


?>


<form action="?page=editFile&folder=<?php echo $currentFolder->getID(); ?>&file=<?php echo $currentFile->getID(); ?>" method="post" enctype="multipart/form-data">
<?php echo '<h2>' . LANG_EDITING_FILE . ': ' . $currentFile->getTitle() . '</h2>';?>
<?php if (isset($error)) echo '<div class="error">' . $error . '</div>'; ?>
<?php if(isset($titleError)) echo "<div class=\"error\">$titleError</div>"; ?>
<?php echo LANG_TITLE; ?>: <input type="text" name="title" value="<?php if (isset($title)) echo $title; else echo $currentFile->getTitle(); ?>"><br>
<?php echo LANG_NOTES; ?>: <textarea name="notes"><?php if (isset($notes)) echo $notes; else echo $currentFile->getNotes(); ?></textarea><br>
<?php echo LANG_DOCUMENT_DATE; ?>: <input type="date" name="date" value="<?php if (isset($date)) { echo $date; } else { echo $currentFile->getDocumentDate(); }?>"><br>
<?php echo LANG_FOLDER; ?>:
<select name="folder">
  <?php

  $statement = $dbh->prepare('SELECT ID,title FROM folders ORDER BY title');
  $statement->execute();
  $result=$statement->fetchAll();
  foreach($result as $folder) {
    if ($folder['ID'] == $currentFolder->getID()) {
      echo '<option value="' . $folder['ID'] . '" selected>' . $folder['title'] . '</option>';
    } else {
      echo '<option value="' . $folder['ID'] . '">' . $folder['title'] . '</option>';
    }
  }

  ?>
</select>
<?php echo LANG_FILE_NAME; ?>: <?php echo $currentFile->getFile(); ?><br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
<input type="submit" name="submit" value="<?php echo LANG_SUBMIT; ?>">
</form>
