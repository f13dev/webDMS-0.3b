<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

// Check that a valid folder is provided, if not direct to main page

// Process form data
if (isset($_POST['submit'])) {
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['oldToken']) {

    $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
    $notes = htmlspecialchars($_POST['notes'], ENT_QUOTES);
    $date = htmlspecialchars($_POST['date'], ENT_QUOTES);
    //$file = $_POST['file'];

    if (trim($title) != '') {

      // Array of allowed extensions
      $allowedExt = array(
        "pdf",
        "doc",
        "docx",
        "xls",
        "xlsx",
        "odt",
        "ods",
        "jpg",
        "jpeg",
        "png",
        "tiff",
        "tif"
      );

      // Array of allowed mime types
      $allowedMimeType = array(
        'application/pdf',
        'application/msword',
        'application/x-msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/excel',
        'application/vnd.ms-excel',
        'application/x-excel',
        'application/x-msexcel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
        'image/jpg',
        'image/jpeg',
        'image/png',
        'image/tiff'
      );

      // Get the file extension and mime types
      $extension = explode('.', $_FILES['file']['name']);
      $extension = end($extension);
      $fileType = $_FILES['file']['type'];

      //if ($fileType=="application/pdf") {
      if (in_array($fileType, $allowedMimeType) && in_array($extension, $allowedExt)) {
      //if (in_array($extension, $allowedExt)) {
        /* Upload the file */
        // Get the unix time for filename
        $time = time();
        // Get the file info and extension
        $info = pathinfo($_FILES['file']['name']);
        $ext = $info['extension'];
        // Set the name
        $newName = $time . '.' . $ext;

        $target = SITE_WEBROOT . SITE_DOCUMENTS . $newName;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
          echo LANG_ERROR_UPLOAD . '. ' . LANG_GO_BACK;
          exit;
        }

        /* Add an entry in the database */
        // The folder doesn't exist, create it
        $statement = $dbh->prepare('INSERT INTO documents(title, notes, folder, document_date, file) VALUES(:title, :notes, :folder, :document_date, :file)');
        $statement->execute(array(
          'title' => $title,
          'notes' => $notes,
          'folder' => $currentFolder->getID(),
          'document_date' => $date,
          'file' => $newName
        ));
        // Create redirect URL
        $id = $dbh->lastInsertId();
        $redirectURL = '?page=webdms&folder=' . $currentFolder->getID() . '&file=' . $id;
        header("Location: $redirectURL");

      } else {
        $error = LANG_VALID_FILE . '. (PDF, DOC, DOCX, ODT, XLS, XLSX ODS)';
      }

    } else {
      $error = LANG_ENTER_TITLE;
    }
  }
}
?>

<form action="?page=newFile&folder=<?php echo $currentFolder->getID(); ?>" method="post" enctype="multipart/form-data">
<?php echo '<h2>' . LANG_UPLOAD_FILE . ' ' . $currentFolder->getTitle() . '</h2>'; ?>
<?php if (isset($error)) echo '<div class="error">' . $error . '</div>'; ?>
<?php if(isset($titleError)) echo "<div class=\"error\">$titleError</div>"; ?>
<?php echo LANG_TITLE; ?>: <input type="text" name="title" value="<?php if (isset($title)) echo $title; ?>"><br>
<?php echo LANG_NOTES; ?>: <textarea name="notes"><?php if (isset($notes)) echo $notes; ?></textarea><br>
<?php echo LANG_DOCUMENT_DATE; ?>: <input type="date" name="date" value="<?php if (isset($date)) { echo $date; } else { echo date("Y-m-d"); }?>"><br>
<?php echo LANG_DOCUMENT; ?>: <input type="file" name="file"><br>
<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" />
<?php echo LANG_SUPPORTED_FILES; ?>: PDF, DOC, DOCX, XLS, XLSX, ODT, ODS, JPEG, PNG, TIFF.<br>
<input type="submit" name="submit" value="<?php echo LANG_SUBMIT; ?>">
</form>
