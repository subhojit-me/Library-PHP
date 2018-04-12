<?php
  class Author{
    private $author_name;
    private $id;

    function __construct($author_name = '', $id = null)
    {
      $this->setAuthorName($author_name);
      $this->setId($id);
    }

    function setAuthorName($new_author_name)
    {
      $this->author_name = (string) $new_author_name;
    }

    function getAuthorName()
    {
      return $this->author_name;
    }

    function setId($new_id)
    {
      $this->id = (int) $new_id;
    }

    function getId()
    {
      return $this->id;
    }

    function save() {
        $statement_handle = $GLOBALS['DB']->prepare(
            "INSERT INTO authors (author_name) VALUES (:author_name);"
        );
        $this->setId($GLOBALS['DB']->lastInsertId());
        $statement_handle->bindValue(':author_name', $this->getAuthorName(), PDO::PARAM_STR);
        $statement_handle->execute();
        $this->id = $GLOBALS['DB']->lastInsertId();
    }

    static function getSome($search_selector, $search_argument = '')
    {
      $output = array();
      $statement_handle = null;
      if ($search_selector == 'id') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "SELECT * FROM authors WHERE id = :search_argument ORDER BY author_name, id;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
      }
      if ($search_selector == 'author_name') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "SELECT * FROM authors WHERE author_name = :search_argument ORDER BY author_name, id;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
      }
      if ($search_selector == 'author_search') {
          $search_argument = "%$search_argument%";
        $statement_handle = $GLOBALS['DB']->prepare(
            "SELECT * FROM authors WHERE author_name LIKE :search_argument ORDER BY author_name, id;"
        );
        $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
      }
      if ($search_selector == 'all') {
          $statement_handle = $GLOBALS['DB']->prepare("SELECT * FROM authors ORDER BY id;");
      }
      if ($statement_handle) {
        // var_dump($statement_handle);
          $statement_handle->execute();
          $results = $statement_handle->fetchAll();
          // $results = $GLOBALS['DB']->query($query);
          foreach ($results as $result) {
                  $new_author = new Author(
                  $result['author_name'],
                  $result['id']
              );
              array_push($output, $new_author);
          }
      }
      return $output;
    }

    static function deleteSome($search_selector, $search_argument = 0)
    {
      $statement_handle = null;
      if ($search_selector == 'id') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "DELETE FROM authors WHERE id = :search_argument;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
      }
      if ($search_selector == 'author_name') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "DELETE FROM authors WHERE author_name = :search_argument;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
      }
      if ($search_selector == 'all') {
          $statement_handle = $GLOBALS['DB']->prepare("DELETE FROM authors;");
      }
      if ($statement_handle) {
          $statement_handle->execute();
      }
    }

    function updateAuthorName($new_author_name)
    {
        $this->setAuthorName($new_author_name);
        $statement_handle = $GLOBALS['DB']->prepare("UPDATE authors SET author_name = :new_author_name WHERE id = :id ;");
        $statement_handle->bindValue(':new_author_name', $this->getAuthorName(), PDO::PARAM_STR);
        $statement_handle->bindValue(':id', $this->getId(), PDO::PARAM_STR);
        $statement_handle->execute();
    }
  }

 ?>
