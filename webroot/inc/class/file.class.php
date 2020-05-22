<?php
/**
Create a new file, set filename, create date, upload date, title
getFilename
getCreateDate
getUploadDate
getTitle




**/

class File
{
  private $id;
  private $title;
  private $notes;
  private $folder;
  private $documentDate;
  private $file;

  function __construct($anID)
  {
    global $dbh;
    // Do file SQL query
    $statement = $dbh->prepare('SELECT ID,title,notes,folder,upload_date,document_date,file FROM documents WHERE ID = ?');
    $statement->execute(array($anID));
    $result = $statement->fetch();
    $this->file = $result['file'];
    $this->documentDate = $result['document_date'];
    $this->notes = $result['notes'];
    $this->title = $result['title'];
    $this->id = $result['ID'];
  }

  function getID()
  {
    return $this->id;
  }

  function getTitle()
  {
    return $this->title;
  }

  function setTitle($aTitle)
  {
    $this->title = $aTitle;
  }

  function getNotes()
  {
    return $this->notes;
  }

  function setNotes($aNote)
  {
    $his->notes = $aNote;
  }

  function getFolder()
  {
    return $this->folder;
  }

  function setFolder($aFolder)
  {
    $this->folder = $aFolder;
  }

  function getUploadDate()
  {
    $file = $this->getFile();
    $dateString = explode('.', $this->getFile());
    return strtotime($dateString[0]);
  }

  function getDocumentDate()
  {
    return $this->documentDate;
  }

  function setDocumentDate($aDate)
  {
    $this->documentDate = $aDate;
  }

  function getFile()
  {
    return $this->file;
  }

  function getExtension()
  {
    $file = $this->getFile();
    $filename = explode('.', $this->file);
    return end($filename);
  }

  function getPDF()
  {
    $name = explode('.', $this->getFile());
    return $name[0] . '.pdf';
  }
  function getPDFContents() {
    $filePath = '/var/www/html/webDMSTesting/documents/' . $this->getFile();
    echo $filePath;
    $contents = file_get_contents('/var/www/html/webDMSTesting/documents/' . $this->getFile());
    return $contents;
  }
}
