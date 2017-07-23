<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="book_borrow")
 */
class BookBorrow
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    protected $book;

    protected $owner;

    protected $current_user;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $time;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $finish_time;













    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getFinishTime()
    {
        return $this->finish_time;
    }

    /**
     * @param mixed $finish_time
     */
    public function setFinishTime($finish_time)
    {
        $this->finish_time = $finish_time;
    }


}

?>