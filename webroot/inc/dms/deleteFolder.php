<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

// Process delete
if (isset($_POST['submit'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {
    // Check number of files in folder
    $count = $dbh->prepare('SELECT ID FROM documents WHERE folder=?');
    $count->bindParam(1, $currentFolder->getID(), PDO::PARAM_INT);
    $count->execute();
    $count = $count->rowCount();
    if ($count != 0) {
      // The folder must be empty to delete it
      $errorText = 'This folder cannot be deleted as it is not empty';
    } else {
      // The folder is empty, delete it
      $statement = $dbh->prepare('DELETE FROM folders WHERE id=?');
      $statement->execute([$currentFolder->getID()]);

      // Row deleted, now return to the folder view
      $redirectURL = '?page=webdms';
      header("Location: $redirectURL");
    }
  }
}

// Check if folder is empty
?>

<form action="?page=deleteFolder&folder=<?php echo $currentFolder->getID(); ?>" method="post">
<?php echo '<h2>' . LANG_DELETE_FOLDER . ': ' . $currentFolder->getTitle() . '</h2>'; ?>
<?php if (isset($errorText)) echo '<div class="error">' . $errorText . '</div>'; ?>
<?php echo LANG_DEL_FOLDER; ?>: <?php echo $currentFolder->getTitle(); ?>.<br>
<?php echo LANG_DEL_FOLDER_IRREVERSABLE; ?><br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
<input type="submit" value="<?php echo LANG_CONFIRM_DELETE; ?>" name="submit">
</form>
