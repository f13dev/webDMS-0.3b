<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

// Process post data
if (isset($_POST['submit'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {
    // The user has confirmed to delete the file
    // File name
    $file = SITE_DOCUMENTS . $currentFile->getFile();
    unlink($file);
      // Find out if the docuemnt is a PDF
      if ($currentFile->getExtension() != 'pdf') {
        // If it's not, remove the PDF as well
        unlink(SITE_DOCUMENTS . $currentFile->getPDF());
      }
      // Successfully deleted, remove from database
      $statement = $dbh->prepare('DELETE FROM documents WHERE id=?');
      $statement->execute([$currentFile->getID()]);

      // Row deleted, now return to the folder view
      $redirectURL = '?page=webdms&folder=' . $currentFolder->getID();
      header("Location: $redirectURL");

  }
}

?>
<form action="?page=deleteFile&folder=<?php echo $currentFolder->getID(); ?>&file=<?php echo $currentFile->getID(); ?>" method="post">
<?php echo '<h2>' . LANG_DELETE_FILE . ': ' . $currentFile->getTitle() . ' from ' . $currentFolder->getTitle() . '</h2>'; ?>
<?php if (isset($errorText)) echo '<div class="error">' . $errorText . '</div>'; ?>
<?php echo LANG_DEL_FILE; ?>: <?php echo $currentFile->getTitle(); ?> from the folder <?php echo $currentFolder->getTitle(); ?>.<br>
<?php echo LANG_DEL_FILE_IRREVERSABLE; ?><br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
<input type="submit" value="<?php echo LANG_CONFIRM_DELETE; ?>" name="submit">
</form>
