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

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $userEmail;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="borrowedBooks")
     */
    protected $bookUser;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="lentBooks")
     */
    protected $bookOwner;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Book")
     */
    protected $book;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $time;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $finish_time;


    public function __construct()
    {
        $this->time = new \DateTime();
    }






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

    /**
     * @return mixed
     */
    public function getBookUser()
    {
        return $this->bookUser;
    }

    /**
     * @param mixed $bookUser
     */
    public function setBookUser($bookUser)
    {
        $this->bookUser = $bookUser;
    }

    /**
     * @return mixed
     */
    public function getBookOwner()
    {
        return $this->bookOwner;
    }

    /**
     * @param mixed $bookOwner
     */
    public function setBookOwner($bookOwner)
    {
        $this->bookOwner = $bookOwner;
    }

    /**
     * @return mixed
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * @param mixed $book
     */
    public function setBook($book)
    {
        $this->book = $book;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * @param mixed $userEmail
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;
    }


}

?>