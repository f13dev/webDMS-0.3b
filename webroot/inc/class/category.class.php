<?php
/**
Global Get all categories
Create a new category, set title, folders[]

getTitle
getFolders []


**/
// Create a connection to the database

class Category
{
  private $id;
  private $title;
  private $folders;

  function __construct($anID)
  {
    global $dbh;
    // Do category SQL query
    $statement = $dbh->prepare('SELECT ID,title FROM categories WHERE ID = ?');
    $statement->execute(array($anID));
    $result = $statement->fetch();
    // Set variable
    $this->id = $result['ID'];
    $this->title = $result['title'];
    // Do folders query
    $statement = $dbh->prepare('SELECT ID FROM folders WHERE category = ? ORDER BY title');
    $statement->bindParam(1, $anID, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll();
    $values = array();
    foreach ($result as $folder)
    {
      array_push($values, $folder['ID']);
    }
    $this->folders = $values;
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

  function getFolders() {
    return $this->folders;
  }
}

function getCategories()
{
  global $dbh;
  $statement = $dbh->prepare('SELECT ID FROM categories ORDER BY title');
  $statement->execute();
  // Create an array variable
  $result = $statement->fetchAll();
  $values = array();
  foreach($result as $category)
  {
    array_push($values, $category['ID']);
  }
  return $values;
  // Return an array of category IDs
}
