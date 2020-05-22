<?php
// Stop direct access
if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header("Location: ../../");
}

?>
<section id="directoryStructure">
  <a href = "?page=newCategory"><div><?php echo LANG_NEW_CATEGORY; ?> +</div></a>
  <a href = "?page=newFolder"><div><?php echo LANG_NEW_FOLDER; ?> +</div></a>
  <hr />
  <?php
  // Get all categories
  foreach(getCategories() as $eachCategory) {
    $theCategory = new Category($eachCategory);
    // Output the category title
    echo '<div><strong>' . $theCategory->getTitle() . '</strong>';

    if (count($theCategory->getFolders()) == 0 && $theCategory->getID() != -1) {
      // If the category is empty, show a delete button
      echo ' <a href=?page=deleteCategory&id=' . $theCategory->getID() . '">Delete</a></div>';
    } else {
      // Close the category div
      echo '</div>';
      // Show the folders in the category
      foreach($theCategory->getFolders() as $eachFolder) {
        // Create a new folder object
        $theFolder = new Folder($eachFolder);
        // Check if it is the current folder, set the appropriate class
        if (isset($currentFolder) && $theFolder->getID() == $currentFolder->getID()) { $class = 'folder selected'; } else { $class = 'folder'; }
        echo '<a href="?page=' . $page . '&folder=' . $theFolder->getID() . '" class="' . $class . '">' . $theFolder->getTitle() . ' (' . $theFolder->getFileCount() . ')</a>';
      }
    }
  }
  ?>
</section>
<section id="fileStructure">
  <section id="fileNavigator">
    <?php
    // Check to see if a folder is set
    if (isset($currentFolder)) {
      // Set orders
      if (isset($_GET['order_by'])) $order_by = $_GET['order_by']; else $order_by = 'document_date';
      if (isset($_GET['order'])) $order = $_GET['order']; else $order='DESC';
      // Set files
      $currentFolder->setFiles($order_by, $order);
      // Show folder title
      echo '<h2>' . $currentFolder->getTitle() . '</h2>';
      // Header table
      echo '<table>';
      if ($currentFolder->getDescription() != '') {
        echo '<tr><td colspan="3"><strong>' . LANG_DESCRIPTION . ': </strong>' . nl2br($currentFolder->getDescription()) . '</td></tr>';
      }
      echo '<tr>
        <td><a href="?page=newFile&folder=' . $currentFolder->getID() . '">' . LANG_UPLOAD_DOCUMENT . '</td>
        <td><a href="?page=editFolder&folder=' . $currentFolder->getID() . '">' . LANG_EDIT_FOLDER . '</a></td>
        <td><a href="?page=deleteFolder&folder=' . $currentFolder->getID() . '">' . LANG_DELETE_FOLDER . '</a></td>
      </tr>
      </table>';
      // Files table
      echo '<h2>' . LANG_FILES . '</h2>';
      echo '<table id="fileList">';
        echo '<tr class=tableHeader>';
          // Define URLS
          if (isset($currentFile)) {
            $base_URL = '?page=' . $page . '&folder=' . $currentFolder->getID() . '&file=' . $currentFile->getID();
          } else {
            $base_URL = '?page=' . $page . '&folder=' . $currentFolder->getID();
          }
          $asc_URL = $base_URL . '&order_by=title&order=ASC';
          $desc_URL = $base_URL . '&order_by=title';
          echo '<td>' . LANG_TITLE . ' <a href="' . $asc_URL . '">&#x25B2;</a><a href="' . $desc_URL . '">&#x25BC;<a/></td>';
          $desc_URL = $base_URL . '&order_by=document_date&order=ASC';
          $asc_URL = $base_URL . '&order_by=document_date';
          echo '<td>' . LANG_DATE . '  <a href="' . $asc_URL . '">&#x25B2;</a><a href="' . $desc_URL . '">&#x25BC;<a/></td>';
          echo '<td>' . LANG_FILE . '</td>';
          $desc_URL = $base_URL . '&order_by=file&order=ASC';
          $asc_URL = $base_URL . '&order_by=file';
          echo '<td>' . LANG_UPLOADED . '  <a href="' . $asc_URL . '">&#x25B2;</a><a href="' . $desc_URL . '">&#x25BC;<a/></td>';
          echo '<td>' . LANG_EDIT . '</td>';
          echo '<td>' . LANG_DELETE . '</td>';
          echo '<td>' . LANG_DOWNLOAD . '</td>';
        echo '</tr>';
        foreach($currentFolder->getFiles() as $eachFile) {
          $ext = explode('.',$eachFile['file']);
          $ext = end($ext);
          // Get the time from the filename
          $uploaded = explode('.', $eachFile['file']);
          $uploaded = $uploaded[0];
          // Check if the current file is sellected
          if (isset($currentFile) && $eachFile['ID'] == $currentFile->getID()) {
            $class = 'selectedFile';
            $id = 'focused';
            $currentFile = new File($eachFile['ID']);
          } else {
            $class = '';
            $id = '';
          }
          // Check order to append to URLs
          $order = '';
          if (isset($_GET['order_by'])) $order .= '&order_by=' . $_GET['order_by'];
          if (isset($_GET['order'])) $order .= '&order=' . $_GET['order'];
          echo '<tr class="' . $class . '" id="' . $id . '">
            <td>
              <a href="?page=webdms&folder=' . $currentFolder->getID() . '&file=' . $eachFile['ID'] . $order . '">' . $eachFile['title'] . '</a>
            </td>
            <td>' . date("dS F Y", strtotime($eachFile['document_date'])) . '</td>
            <td>' . $eachFile['file'] . '</td>
            <td>' . gmdate("dS F Y H:i", $uploaded) . '</td>
            <td><a href="?page=editFile&folder=' . $currentFolder->getID() . '&file=' . $eachFile['ID'] . '">' . LANG_EDIT . '</a></td>
            <td><a href="?page=deleteFile&folder=' . $currentFolder->getID() . '&file=' . $eachFile['ID'] . '">' . LANG_DELETE . '</a></td>
            <td><a href="inc/dms/downloadFile.php?file=' . $eachFile['file'] . '" download="' . $eachFile['title'] . '.' . $ext . '">' . LANG_DOWNLOAD . '</a></td>
          </tr>';
        }
        echo '</table>';
    } else {
      echo '<div class="pleaseSelect"><div>' . LANG_SELECT_FOLDER . '.</div></div>';
    }
    ?>
  </section>
  <section id="filePreview">
    <?php
    if (isset($currentFile)) {
      // Create supported file arrays
      $supportedImages = array('jpg','jpeg','tif','tiff','png');
      $supportedDocuments = array('odt','ods','doc','docx','xls','xlsx');
      if (strtolower($currentFile->getExtension()) == 'pdf') {
        // Embed as a PDF
        //echo '<iframe src="' . SITE_DOCUMENTS . $currentFile->getFile() . '" width="100%" height="100%"><p>iFrames are not supported</p></iframe>';
        echo '<iframe src="inc/dms/getFile.php?file=' . $currentFile->getFile() . '" width="100%" height="100%"><p>iFrames are not supported</p></iframe>';
      } elseif (in_array($currentFile->getExtension(), $supportedImages)) {
        echo '<div style="height:100%; overflow-y:auto">
          <img src="inc/dms/getFile.php?file=' . $currentFile->getFile() . '" width="100%" />
        </div>';
      } elseif (in_array($currentFile->getExtension(), $supportedDocuments)) {
        // If the PDF doesn't exist, try to create it
        if (!file_exists(SITE_WEBROOT . SITE_DOCUMENTS . $currentFile->getPDF()) && OFFICE_APPLICATION) {
          $cmd = 'export HOME=/tmp && soffice --headless --convert-to pdf --outdir ' .  SITE_WEBROOT . SITE_DOCUMENTS . ' ' .  SITE_WEBROOT . SITE_DOCUMENTS . $currentFile->getFile();
	  exec($cmd);
        }
        // If the file exists, display it
        if (file_exists(SITE_WEBROOT . SITE_DOCUMENTS . $currentFile->getPDF())) {
          echo '<iframe src="inc/dms/getFile.php?file=' . $currentFile->getPDF() . '#toolbar=1" width="100%" height="100%"><p>iFrames are not supported</p></iframe>';
        } else {
          $error = true;
        }
      } else {
        $error = true;
      }
    } else {
      echo '<div class="pleaseSelect"><div>' . LANG_SELECT_FILE . '.</div></div>';
    }
    if (isset($error) && $error) {
      echo '<div class="pleaseSelect"><div>' . LANG_NO_PREVIEW . '.</div></div>';
    }
    ?>

  </section>
</section>
