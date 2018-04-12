<?php
  class GenreBook
  {
    private $id;
    private $genre_id;
    private $book_id;

    function __construct($genre_id = null, $book_id = null, $id = null)
    {
      $this->setGenreId($genre_id);
      $this->setBookId($book_id);
      $this->setId($id);
    }

    public function getId(){
      return $this->id;
    }

    public function setId($id){
      $this->id = (int) $id;
    }

    public function getGenreId(){
      return $this->genre_id;
    }

    public function setGenreId($genre_id){
      $this->genre_id = (int)$genre_id;
    }

    public function getBookId(){
      return $this->book_id;
    }

    public function setBookId($book_id){
      $this->book_id = (int) $book_id;
    }

    function save()
    {
      $GLOBALS['DB']->exec(
      "INSERT INTO genres_books
          (genre_id, book_id) VALUES
          ({$this->getGenreId()}, {$this->getBookId()});"
      );
      $this->id = $GLOBALS['DB']->lastInsertId();
    }

    function update($genre_id, $book_id)
    {
      $this->setGenreId($genre_id);
      $this->setBookId($book_id);
      $GLOBALS['DB']->exec(
          "UPDATE genres_books SET
              genre_id = {$this->getGenreId()},
              book_id = {$this->getBookId()}
          WHERE id = {$this->getId()};"
      );
    }

    static function getSome($search_selector, $search_argument = '')
    {
      $output = array();
      $query = "";
      if ($search_selector == 'all') {
          $query = "SELECT * FROM genres_books;";
      }
      if ($search_selector == 'genre_id') {
          $query = "SELECT * FROM genres_books WHERE genre_id = $search_argument;";
      }
      if ($search_selector == 'book_id') {
          $query = "SELECT * FROM genres_books WHERE book_id = $search_argument;";
      }
      if ($query) {
          $results = $GLOBALS['DB']->query($query);
          foreach ($results as $result) {
                  $genre_book = new GenreBook(
                  $result['genre_id'],
                  $result['book_id'],
                  $result['id']
              );
              array_push($output, $genre_book);
          }
      }
      return $output;
    }

    static function deleteSome($search_selector, $search_argument = 0)
    {
      $delete_command = '';
      if ($search_selector == 'id') {
          $delete_command = "DELETE FROM genres_books WHERE id = $search_argument;";
      }
      if ($search_selector == 'all') {
          $delete_command = "DELETE FROM genres_books;";
      }
      if ($search_selector == 'genre_id') {
          $delete_command = "DELETE FROM genres_books WHERE genre_id = $search_argument;";
      }
      if ($search_selector == 'book_id') {
          $delete_command = "DELETE FROM genres_books WHERE book_id = $search_argument;";
      }
      if ($delete_command) {
          $GLOBALS['DB']->exec($delete_command);
      }
    }

    static function getAll()
    {
      return self::getSome('all');
    }

    static function deleteAll()
    {
      self::deleteSome('all');
    }
  }


?>
