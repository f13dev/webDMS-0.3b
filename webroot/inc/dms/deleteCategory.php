<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

// Process delete
if (isset($_POST['submit'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {
    // Check number of files in folder
    $count = $dbh->prepare('SELECT ID FROM folders WHERE category=?');
    $count->bindParam(1, $_GET['id'], PDO::PARAM_INT);
    $count->execute();
    $count = $count->rowCount();
    if ($count != 0) {
      // The folder must be empty to delete it
      $errorText = LANG_CATEGORY_NOT_EMPTY;
    } elseif ($_GET['id'] == '-1') {
      $errorText = LANG_UNCATEGORISED;
    } else {
      // The folder is empty, delete it
      $statement = $dbh->prepare('DELETE FROM categories WHERE id=?');
      $statement->bindParam(1, $_GET['id'], PDO::PARAM_INT);
      $statement->execute();

      // Row deleted, now return to the folder view
      $redirectURL = '?page=webdms';
      header("Location: $redirectURL");
    }
  }
}

// Check if folder is empty
?>

<form action="?page=deleteCategory&id=<?php echo $_GET['id']; ?>" method="post">
<?php
// Get the category title
$statement = $dbh->prepare("SELECT title FROM categories WHERE id= :id");
$statement->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$statement->execute();
$row=$statement->fetch();
?>
<?php echo '<h2>' . LANG_DELETE_CATEGORY . ': ' . $row['title'] . '</h2>'; ?>
<?php if (isset($errorText)) echo '<div class="error">' . $errorText . '</div>'; ?>
<?php echo LANG_DEL_CATEGORY; ?>: <?php echo $row['title']; ?>.<br>
<?php echo LANG_DEL_CATEGORY_IRRIVERSABLE; ?><br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
<input type="submit" value="<?php echo LANG_CONFIRM_DELETE; ?>" name="submit">
</form>
