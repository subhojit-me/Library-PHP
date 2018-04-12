<?php
    class Checkout
    {
        private $id;
        private $book_copy_id;
        private $patron_id;
        private $checkout_date;
        private $due_date;
        private $returned_date;
        private $comment;
        private $still_out;


        function __construct($book_copy_id = null, $patron_id = null, $checkout_date = '', $due_date = '', $returned_date = '', $comment = '', $still_out = 0, $id = null)
        {
            $this->setBookCopyId($book_copy_id);
            $this->setPatronId($patron_id);
            $this->setCheckoutDate($checkout_date);
            $this->setDueDate($due_date);
            $this->setReturnedDate($returned_date);
            $this->setComment($comment);
            $this->setStillOut($still_out);
            $this->setId($id);
        }

        function setBookCopyId($book_copy_id)
        {
            $this->book_copy_id = (int) $book_copy_id;
        }

        function setPatronId($patron_id)
        {
            $this->patron_id = (int) $patron_id;
        }

        function setCheckoutDate($checkout_date)
        {
            $this->checkout_date = $checkout_date;
        }

        function setDueDate($due_date)
        {
            $this->due_date = $due_date;
        }

        function setReturnedDate($returned_date)
        {
            $this->returned_date = $returned_date;
        }

        function setComment($comment)
        {
            $this->comment = (string) $comment;
        }

        function setStillOut($still_out)
        {
            $this->still_out = (int) $still_out;
        }

        function setId($id)
        {
            $this->id = (int) $id;
        }

        function getBookCopyId()
        {
            return $this->book_copy_id;
        }

        function getPatronId()
        {
            return $this->patron_id;
        }

        function getCheckoutDate()
        {
            return $this->checkout_date;
        }

        function getDueDate()
        {
            return $this->due_date;
        }

        function getReturnedDate()
        {
            return $this->returned_date;
        }

        function getComment()
        {
            return $this->comment;
        }

        function getStillOut()
        {
            return $this->still_out;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $statement_handle = $GLOBALS['DB']->prepare(
                "INSERT INTO checkouts (book_copy_id, patron_id, checkout_date, due_date, returned_date, comment, still_out) VALUES
                (:book_copy_id, :patron_id, :checkout_date, :due_date, :returned_date, :comment, :still_out);"
            );
            $statement_handle->bindValue(':book_copy_id', $this->getBookCopyId(), PDO::PARAM_INT);
            $statement_handle->bindValue(':patron_id', $this->getPatronId(), PDO::PARAM_INT);
            $statement_handle->bindValue(':checkout_date', $this->getCheckoutDate());
            $statement_handle->bindValue(':due_date', $this->getDueDate());
            $statement_handle->bindValue(':returned_date', $this->getReturnedDate());
            $statement_handle->bindValue(':comment', $this->getComment(), PDO::PARAM_STR);
            $statement_handle->bindValue(':still_out', $this->getStillOut(), PDO::PARAM_INT);
            $statement_handle->execute();
            $this->setId($GLOBALS['DB']->lastInsertId());
        }

        function update($book_copy_id, $patron_id, $checkout_date, $due_date, $returned_date, $comment, $still_out)
        {
            $this->setBookCopyId($book_copy_id);
            $this->setPatronId($patron_id);
            $this->setCheckoutDate($checkout_date);
            $this->setDueDate($due_date);
            $this->setReturnedDate($returned_date);
            $this->setComment($comment);
            $this->setStillOut($still_out);

            $statement_handle = $GLOBALS['DB']->prepare(
                "UPDATE checkouts SET
                    book_copy_id = :book_copy_id,
                    patron_id = :patron_id,
                    checkout_date = :checkout_date,
                    due_date = :due_date,
                    returned_date = :returned_date,
                    comment = :comment,
                    still_out = :still_out
                WHERE id = :id ;"
            );
            $statement_handle->bindValue(':book_copy_id', $this->getBookCopyId(), PDO::PARAM_INT);
            $statement_handle->bindValue(':patron_id', $this->getPatronId(), PDO::PARAM_INT);
            $statement_handle->bindValue(':checkout_date', $this->getCheckoutDate());
            $statement_handle->bindValue(':due_date', $this->getDueDate());
            $statement_handle->bindValue(':returned_date', $this->getReturnedDate());
            $statement_handle->bindValue(':comment', $this->getComment(), PDO::PARAM_STR);
            $statement_handle->bindValue(':still_out', $this->getStillOut(), PDO::PARAM_INT);
            $statement_handle->bindValue(':id', $this->getId(), PDO::PARAM_INT);
            $statement_handle->execute();
        }

        static function getSome($search_selector, $search_argument = '')
        {
            $statement_handle = null;

            if ($search_selector == 'id') {
                $statement_handle = $GLOBALS['DB']->prepare(
                    "SELECT * FROM checkouts WHERE id = :search_argument;"
                );
                $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
            }

            if ($search_selector == 'patron_id') {
                $statement_handle = $GLOBALS['DB']->prepare(
                    "SELECT * FROM checkouts WHERE patron_id = :search_argument;"
                );
                $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
            }

            if ($search_selector == 'all') {
                $statement_handle = $GLOBALS['DB']->prepare(
                    "SELECT * FROM checkouts ORDER BY book_copy_id, id;"
                );
            }

            $output = array();
            if ($statement_handle) {
                $statement_handle->execute();
                $results = $statement_handle->fetchAll();
                foreach ($results as $result) {
                    $checkout = new Checkout(
                        $result['book_copy_id'],
                        $result['patron_id'],
                        $result['checkout_date'],
                        $result['due_date'],
                        $result['returned_date'],
                        $result['comment'],
                        $result['still_out'],
                        $result['id']
                    );
                    array_push($output, $checkout);
                }
            }
            return $output;
        }

        static function deleteSome($search_selector, $search_argument = 0)
        {
            $statement_handle = null;

            if ($search_selector == 'id') {
                $statement_handle = $GLOBALS['DB']->prepare(
                    "DELETE FROM checkouts WHERE id = :search_argument;"
                );
                $statement_handle->bindValue(':search_argument', $search_argument, PDO::PARAM_INT);
            }

            if ($search_selector == 'all') {
                $statement_handle = $GLOBALS['DB']->prepare("DELETE FROM checkouts;");
            }

            if ($statement_handle) {
                $statement_handle->execute();
            }
        }
    }
?>
