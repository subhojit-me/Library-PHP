<?php
require_once __DIR__."/../src/AuthorBook.php";

  class Book
  {
    private $title;
    private $publish_date;
    private $synopsis;
    private $id;

    function __construct($title = '', $publish_date = '', $synopsis = '', $id = null)
    {
      $this->setTitle($title);
      $this->setPublishDate($publish_date);
      $this->setSynopsis($synopsis);
      $this->setId($id);
    }

    public function getTitle(){
  		return $this->title;
  	}

  	public function setTitle($title){
  		$this->title = (string) $title;
  	}

  	public function getPublishDate(){
  		return $this->publish_date;
  	}

  	public function setPublishDate($publish_date){
  		$this->publish_date = $publish_date;
  	}

  	public function getSynopsis(){
  		return $this->synopsis;
  	}

  	public function setSynopsis($synopsis){
  		$this->synopsis = (string) $synopsis;
  	}

  	public function getId(){
  		return $this->id;
  	}

  	public function setId($id){
  		$this->id = (int) $id;
  	}

    function save() {
        $statement_handle = $GLOBALS['DB']->prepare(
            "INSERT INTO books (title, publish_date, synopsis) VALUES (:title, :publish_date, :synopsis);"
        );
        $this->setId($GLOBALS['DB']->lastInsertId());
        $statement_handle->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $statement_handle->bindValue(':publish_date', $this->getPublishDate(), PDO::PARAM_STR);
        $statement_handle->bindValue(':synopsis', $this->getSynopsis(), PDO::PARAM_STR);
        $statement_handle->execute();
        $this->setId($GLOBALS['DB']->lastInsertId());
    }

  static function getSome($search_selector, $search_argument = '')
  {
    $output = array();
    $statement_handle = null;
    if ($search_selector == 'id') {
      $statement_handle = $GLOBALS['DB']->prepare(
          "SELECT * FROM books WHERE id = :search_argument ORDER BY title, id;"
      );
      $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
    }
    if ($search_selector == 'title') {
      $statement_handle = $GLOBALS['DB']->prepare(
          "SELECT * FROM books WHERE title = :search_argument ORDER BY title, id;"
      );
      $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
    }
    if ($search_selector == 'title_search') {
        $search_argument = "%$search_argument%";
      $statement_handle = $GLOBALS['DB']->prepare(
          "SELECT * FROM books WHERE title LIKE :search_argument ORDER BY title, id;"
      );
      $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
    }
    if ($search_selector == 'publish_date') {
      $statement_handle = $GLOBALS['DB']->prepare(
          "SELECT * FROM books WHERE publish_date = :search_argument ORDER BY name, id;"
      );
      $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
    }
    if ($search_selector == 'all') {
      $statement_handle = $GLOBALS['DB']->prepare("SELECT * FROM books ORDER BY id;");
    }
    if ($statement_handle) {
    // var_dump($statement_handle);
      $statement_handle->execute();
      $results = $statement_handle->fetchAll();
      // $results = $GLOBALS['DB']->query($query);
      foreach ($results as $result) {
              $new_book = new Book(
              $result['title'],
              $result['publish_date'],
              $result['synopsis'],
              $result['id']
          );
          array_push($output, $new_book);
        }
      }
      return $output;
    }

    static function deleteSome($search_selector, $search_argument = 0)
    {
      $statement_handle = null;
      if ($search_selector == 'id') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "DELETE FROM books WHERE id = :search_argument;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
      }
      if ($search_selector == 'title') {
          $statement_handle = $GLOBALS['DB']->prepare(
              "DELETE FROM books WHERE title = :search_argument;"
          );
          $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
      }
      if ($search_selector == 'all') {
          $statement_handle = $GLOBALS['DB']->prepare("DELETE FROM books;");
      }
      if ($statement_handle) {
          $statement_handle->execute();
      }
    }

    function updateTitle($new_title)
    {
        $this->setTitle($new_title);
        $statement_handle = $GLOBALS['DB']->prepare(
            "UPDATE books SET title = :title WHERE id = :id;"
        );
        $statement_handle->bindValue(':title', $this->getTitle(), PDO::PARAM_STR);
        $statement_handle->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $statement_handle->execute();
    }
    function updateSynopsis($new_synopsis)
    {
        $this->setSynopsis($new_synopsis);
        $statement_handle = $GLOBALS['DB']->prepare(
            "UPDATE books SET synopsis = :synopsis WHERE id = :id;"
        );
        $statement_handle->bindValue(':synopsis', $this->getSynopsis(), PDO::PARAM_STR);
        $statement_handle->bindValue(':id', $this->getId(), PDO::PARAM_INT);
        $statement_handle->execute();
    }
  }
?>
