<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="books_library")
 */
class Library
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="library")
     */
    protected $users;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $owner;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Book", mappedBy="library")
     */
    protected $books;











    public function __construct(){
        $this->users = new ArrayCollection();
        $this->books = new ArrayCollection();
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
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @param mixed $user
     */
    public function addUsers($user){
        $this->users[] = $user;
    }

    /**
     * @param mixed
     */
    public function removeUsers($user){
        $this->users->removeElement($user);
    }

    /**
     * @return mixed
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * @param mixed $books
     */
    public function setBooks($books)
    {
        $this->books = $books;
    }

    /**
     * @param mixed $user
     */
    public function addBooks($book){
        $this->books[] = $book;
    }

    /**
     * @param mixed
     */
    public function removeBooks($book){
        $this->books->removeElement($book);
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

}

?>