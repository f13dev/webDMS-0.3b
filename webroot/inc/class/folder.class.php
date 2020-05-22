<?php
/**
Create new folder, set title, description, files
getTitle
getDescription
getFiles
getFileCount



**/

class folder
{
  private $id;
  private $title;
  private $description;
  private $category;
  private $files;
  private $fileCount;

  function __construct($anID)
  {
    global $dbh;
    // Do folders SQL query
    $statement = $dbh->prepare('SELECT ID,title,description,category FROM folders WHERE ID = ?');
    $statement->execute(array($anID));
    $result = $statement->fetch();
    // Set variable
    $this->id = $result['ID'];
    $this->title = $result['title'];
    $this->description = $result['description'];
    $this->category = $result['category'];
    // Do files query
    $statement = $dbh->prepare('SELECT ID FROM documents WHERE folder = ?');
    $statement->bindParam(1, $anID, PDO::PARAM_INT);
    $statement->execute();
    $count = $statement->rowCount();
    // Set variable
    $this->fileCount = $count;
  }

  function getID()
  {
    return $this->id;
  }

  function setFiles($order_by = '', $order = '')
  {
    global $dbh;
    // Set order variables
    if ($order_by == 'file')
    {
      $orderBy = 'file';
    } else if ($order_by == 'title')
    {
      $orderBy = 'title';
    } else
    {
      $orderBy = 'document_date';
    }
    if ($order == 'ASC')
    {
      $order = 'ASC';
    } else
    {
      $order = 'DESC';
    }
    // Do files query
    $statement = $dbh->prepare("SELECT * FROM documents WHERE folder=? ORDER BY $orderBy $order");
    $statement->bindParam(1, $this->id, PDO::PARAM_INT);
    $statement->execute();
    $result=$statement->fetchAll();
    $values = array();
    foreach($result as $file)
    {
      // Add each file to array
      array_push($values, $file);
    }
    // Set variable
    $this->files = $values;
  }

  function getFiles()
  {
    return $this->files;
  }

  function getTitle()
  {
    return $this->title;
  }

  function setTitle($aTitle)
  {
    $this->title = $aTitle;
  }

  function getDescription()
  {
    return $this->description;
  }

  function setDescription($aDescription)
  {
    $this->description = $aDescription;
  }

  function getFileCount()
  {
    return $this->fileCount;
  }

  function getFilesCount()
  {
    return count($this->files);
  }

  function getCategory()
  {
    return $this->category;
  }

  function setCategory($aCategory)
  {
    $this->category = $aCategory;
  }
}
