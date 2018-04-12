<?php
  class Genre{
    private $genre_name;
    private $id;

    function __construct($genre_name = '', $id = null)
    {
      $this->setGenreName($genre_name);
      $this->setId($id);
    }

    function setGenreName($new_genre_name)
    {
      $this->genre_name = (string) $new_genre_name;
    }

    function getGenreName()
    {
      return $this->genre_name;
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
      $prepare_statement = $GLOBALS['DB']->prepare("INSERT INTO genres (genre_name) VALUES (:genre_name);");
      $prepare_statement->bindValue(":genre_name", $this->getGenreName(), PDO::PARAM_STR);
      $prepare_statement->execute();
      $this->setId($GLOBALS['DB']->lastInsertId());
    }

    static function getSome($search_selector, $search_argument = '')
    {
      $output = array();
      $statement_handle = null;
      if ($search_selector == 'id') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "SELECT * FROM genres WHERE id = :search_argument ORDER BY genre_name, id;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
      }
      if ($search_selector == 'genre_name') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "SELECT * FROM genres WHERE genre_name = :search_argument ORDER BY genre_name, id;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
      }
      if ($search_selector == 'all') {
          $statement_handle = $GLOBALS['DB']->prepare("SELECT * FROM genres ORDER BY id;");
      }
      if ($statement_handle) {
        // var_dump($statement_handle);
          $statement_handle->execute();
          $results = $statement_handle->fetchAll();
          // $results = $GLOBALS['DB']->query($query);
          foreach ($results as $result) {
                  $new_genre = new Genre(
                  $result['genre_name'],
                  $result['id']
              );
              array_push($output, $new_genre);
          }
      }
      return $output;
    }

    static function deleteSome($search_selector, $search_argument = 0)
    {
      $statement_handle = null;
      if ($search_selector == 'id') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "DELETE FROM genres WHERE id = :search_argument;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
      }
      if ($search_selector == 'genre_name') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "DELETE FROM genres WHERE genre_name = :search_argument;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
      }
      if ($search_selector == 'all') {
          $statement_handle = $GLOBALS['DB']->prepare("DELETE FROM genres;");
      }
      if ($statement_handle) {
          $statement_handle->execute();
      }
    }

    function updateGenreName($new_genre_name)
    {
        $this->setGenreName($new_genre_name);
        $prepare_statement = $GLOBALS['DB']->prepare("UPDATE genres SET genre_name = :new_genre_name WHERE id = {$this->getId()};");
        $prepare_statement->bindValue(':new_genre_name', $this->getGenreName(), PDO::PARAM_STR);
        $prepare_statement->execute();
    }
  }

 ?>
