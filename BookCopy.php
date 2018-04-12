<?php
    class BookCopy
    {
        private $id;
        private $book_id;
        private $book_condition;
        private $comment;
        //book_condition is a scale from 0-5 where 5 is brand new and 0 is missing/not useable

        function __construct($book_id = null, $book_condition = 0, $comment = '', $id = null)
        {
            $this->setBookId($book_id);
            $this->setBookCondition($book_condition);
            $this->setComment($comment);
            $this->setId($id);
        }

        function setBookId($book_id)
        {
            $this->book_id = (int) $book_id;
        }

        function setBookCondition($book_condition)
        {
            $this->book_condition = (int) $book_condition;
        }

        function setComment($comment)
        {
            $this->comment = (string) $comment;
        }

        function setId($id)
        {
            $this->id = (int) $id;
        }

        function getBookId()
        {
            return $this->book_id;
        }

        function getBookCondition()
        {
            return $this->book_condition;
        }

        function getComment()
        {
            return $this->comment;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $statement_handle = $GLOBALS['DB']->prepare(
                "INSERT INTO book_copies (book_id, comment, book_condition) VALUES
                (:book_id, :comment, :book_condition);"
            );
            $statement_handle->bindValue(':book_id', $this->getBookId(), PDO::PARAM_INT);
            $statement_handle->bindValue(':comment', $this->getComment(), PDO::PARAM_STR);
            $statement_handle->bindValue(':book_condition', $this->getBookCondition(), PDO::PARAM_INT);
            $statement_handle->execute();
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function update($book_id, $book_condition, $comment)
        {
            $this->setBookId($book_id);
            $this->setBookCondition($book_condition);
            $this->setComment($comment);

            $statement_handle = $GLOBALS['DB']->prepare(
                "UPDATE book_copies SET
                    book_id = :book_id,
                    comment = :comment,
                    book_condition = :book_condition
                WHERE id = :id ;"
            );
            $statement_handle->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $statement_handle->bindValue(':book_id', $this->getBookId(), PDO::PARAM_INT);
            $statement_handle->bindValue(':comment', $this->getComment(), PDO::PARAM_STR);
            $statement_handle->bindValue(':book_condition', $this->getBookCondition(), PDO::PARAM_INT);
            $statement_handle->execute();
        }

        static function getSome($search_selector, $search_argument = '')
        {
            $statement_handle = null;

            if ($search_selector == 'id') {
                $statement_handle = $GLOBALS['DB']->prepare(
                    "SELECT * FROM book_copies WHERE id = :search_argument;"
                );
                $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
            }
            if ($search_selector == 'book_id') {
                $statement_handle = $GLOBALS['DB']->prepare(
                    "SELECT * FROM book_copies WHERE book_id = :search_argument;"
                );
                $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
            }

            if ($search_selector == 'all') {
                var_dump('test');
                $statement_handle = $GLOBALS['DB']->prepare(
                    "SELECT * FROM book_copies ORDER BY book_id, id;"
                );
            }

            $output = array();
            if ($statement_handle) {
                $statement_handle->execute();
                $results = $statement_handle->fetchAll();
                foreach ($results as $result) {
                    $book_copy = new BookCopy(
                        $result['book_id'],
                        $result['book_condition'],
                        $result['comment'],
                        $result['id']
                    );
                    array_push($output, $book_copy);
                }
            }
            return $output;
        }

        static function deleteSome($search_selector, $search_argument = 0)
        {
            $statement_handle = null;

            if ($search_selector == 'id') {
                $statement_handle = $GLOBALS['DB']->prepare(
                    "DELETE FROM book_copies WHERE id = :search_argument;"
                );
                $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
            }

            if ($search_selector == 'all') {
                $statement_handle = $GLOBALS['DB']->prepare("DELETE FROM book_copies;");
            }

            if ($statement_handle) {
                $statement_handle->execute();
            }
        }

    }
?>
