<?php
  class AuthorBook
  {
    private $id;
    private $author_id;
    private $book_id;

    function __construct($author_id = null, $book_id = null, $id = null)
    {
      $this->setAuthorId($author_id);
      $this->setBookId($book_id);
      $this->setId($id);
    }

    public function getId(){
      return $this->id;
    }

    public function setId($id){
      $this->id = (int)$id;
    }

    public function getAuthorId(){
      return $this->author_id;
    }

    public function setAuthorId($author_id){
      $this->author_id = (int)$author_id;
    }

    public function getBookId(){
      return $this->book_id;
    }

    public function setBookId($book_id){
      $this->book_id = (int)$book_id;
    }

    function save()
    {
      $GLOBALS['DB']->exec(
      "INSERT INTO authors_books
          (author_id, book_id) VALUES
          ({$this->getAuthorId()}, {$this->getBookId()});"
      );
      $this->id = $GLOBALS['DB']->lastInsertId();
    }

    function update($author_id, $book_id)
    {
      $this->setAuthorId($author_id);
      $this->setBookId($book_id);
      $GLOBALS['DB']->exec(
          "UPDATE authors_books SET
              author_id = {$this->getAuthorId()},
              book_id = {$this->getBookId()}
          WHERE id = {$this->getId()};"
      );
    }

    static function getSome($search_selector, $search_argument = '')
    {
      $output = array();
      $query = "";
      if ($search_selector == 'all') {
          $query = "SELECT * FROM authors_books;";
      }
      if ($search_selector == 'author_id') {
          $query = "SELECT * FROM authors_books WHERE author_id = $search_argument;";
      }
      if ($search_selector == 'book_id') {
          $query = "SELECT * FROM authors_books WHERE book_id = $search_argument;";
      }
      if ($query) {
          $results = $GLOBALS['DB']->query($query);
          foreach ($results as $result) {
                  $author_book = new AuthorBook(
                  $result['author_id'],
                  $result['book_id'],
                  $result['id']
              );
              array_push($output, $author_book);
          }
      }
      return $output;
    }

    static function deleteSome($search_selector, $search_argument = 0)
    {
      $delete_command = '';
      if ($search_selector == 'id') {
          $delete_command = "DELETE FROM authors_books WHERE id = $search_argument;";
      }
      if ($search_selector == 'all') {
          $delete_command = "DELETE FROM authors_books;";
      }
      if ($search_selector == 'author_id') {
          $delete_command = "DELETE FROM authors_books WHERE author_id = $search_argument;";
      }
      if ($search_selector == 'book_id') {
          $delete_command = "DELETE FROM authors_books WHERE book_id = $search_argument;";
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
