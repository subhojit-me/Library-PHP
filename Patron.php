<?php
class Patron
{
  private $patron_name;
  private $contact_info;
  private $id;

  function __construct($patron_name, $contact_info, $id = null)
  {
    $this->setPatronName($patron_name);
    $this->setContactInfo($contact_info);
    $this->setId($id);
  }

  function setPatronName($new_author_name)
  {
    $this->author_name = (string) $new_author_name;
  }

  function getPatronName()
  {
    return $this->author_name;
  }

  function setContactInfo($contact_info)
  {
    $this->contact_info = (string) $contact_info;
  }

  function getContactInfo(){
    return $this->contact_info;
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
      "INSERT INTO patrons (patron_name, contact_info) VALUES
      (:patron_name, :contact_info);"
    );
    $statement_handle->bindValue(':patron_name', $this->getPatronName(), PDO::PARAM_STR);
    $statement_handle->bindValue(':contact_info', $this->getContactInfo(), PDO::PARAM_STR);
    $statement_handle->execute();
    $this->setId($GLOBALS['DB']->lastInsertId());
  }

  static function getSome($search_selector, $search_argument = '')
  {
    $output = array();
    $statement_handle = null;
    if ($search_selector == 'id') {
        $statement_handle = $GLOBALS['DB']->prepare(
            "SELECT * FROM patrons WHERE id = :search_argument ORDER BY patron_name, id;"
        );
        $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
    }
    if ($search_selector == 'patron_name') {
        $statement_handle = $GLOBALS['DB']->prepare(
            "SELECT * FROM patrons WHERE patron_name = :search_argument ORDER BY patron_name, id;"
        );
        $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
    }
    if ($search_selector == 'contact_info') {
        $statement_handle = $GLOBALS['DB']->prepare(
            "SELECT * FROM patrons WHERE contact_info = :search_argument ORDER BY patron_name, id;"
        );
        $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
    }
    if ($search_selector == 'all') {
        $statement_handle = $GLOBALS['DB']->prepare("SELECT * FROM patrons ORDER BY id;");
    }
    if ($statement_handle) {
      $statement_handle->execute();
      $results = $statement_handle->fetchAll();
      foreach ($results as $result) {

              $new_patron = new Patron(
              $result['patron_name'],
              $result['contact_info'],
              $result['id']
          );
          array_push($output, $new_patron);
      }
    }
    return $output;
  }

  static function deleteSome($search_selector, $search_argument = 0)
  {
    $statement_handle = null;
    if ($search_selector == 'id') {
        $statement_handle = $GLOBALS['DB']->prepare(
            "DELETE FROM patrons WHERE id = :search_argument;"
        );
        $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
    }
    if ($search_selector == 'patron_name') {
        $statement_handle = $GLOBALS['DB']->prepare(
            "DELETE FROM patrons WHERE patron_name = :search_argument;"
        );
        $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
    }
    if ($search_selector == 'contact_info') {
        $statement_handle = $GLOBALS['DB']->prepare(
            "DELETE FROM patrons WHERE contact_info = :search_argument;"
        );
        $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_STR);
    }
    if ($search_selector == 'all') {
        $statement_handle = $GLOBALS['DB']->prepare("DELETE FROM patrons;");
    }
    if ($statement_handle) {
        $statement_handle->execute();
    }
  }

    function updatePatronName($new_patron_name)
    {
        $this->setPatronName($new_patron_name);
        $statement_handle = $GLOBALS['DB']->prepare("UPDATE patrons SET patron_name = :new_patron_name WHERE id = {$this->getId()};");
        $statement_handle->bindValue(':new_patron_name', $this->getPatronName(), PDO::PARAM_STR);
        $statement_handle->execute();
    }

    function updateContactInfo($new_contact_info)
    {
        $this->setContactInfo($new_contact_info);
        $statement_handle = $GLOBALS['DB']->prepare("UPDATE patrons SET contact_info = :new_contact_info WHERE id = {$this->getId()};");
        $statement_handle->bindValue(':new_contact_info', $this->getContactInfo(), PDO::PARAM_STR);
        $statement_handle->execute();
    }
}
 ?>
