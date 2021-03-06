<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\PrePersist()
     *
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
    }


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Library", inversedBy="users")
     */
    protected $library;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $invitationToken;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Invitation", mappedBy="user")
     */
    protected $invitations;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="author")
     */
    protected $comments;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BookRead", mappedBy="user")
     * @ORM\OrderBy({"endDate" = "DESC"})
     */
    protected $readBooks;

    /**
     * Od kogoś
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BookBorrow", mappedBy="bookUser")
     */
    protected $borrowedBooks;

    /**
     * Komus
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\BookBorrow", mappedBy="bookOwner")
     */
    protected $lentBooks;


    public function __construct()
    {
        parent::__construct();
        $this->createdAt = new \DateTime;
        $this->invitations = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->readBooks = new ArrayCollection();
        $this->borrowedBooks = new ArrayCollection();
        $this->lentBooks = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
    public function getLibrary()
    {
        return $this->library;
    }

    /**
     * @param mixed $library
     */
    public function setLibrary($library)
    {
        $this->library = $library;
    }

    /**
     * @return mixed
     */
    public function getInvitations()
    {
        return $this->invitations;
    }

    /**
     * @param mixed $invitations
     */
    public function setInvitations($invitations)
    {
        $this->invitations = $invitations;
    }

    /**
     * @param mixed $invitation
     */
    public function addInvitations($invitation){
        $this->invitations[] = $invitation;
    }

    /**
     * @param mixed
     */
    public function removeInvitations($invitation){
        $this->invitations->removeElement($invitation);
    }

    /**
     * @return mixed
     */
    public function getInvitationToken()
    {
        return $this->invitationToken;
    }

    /**
     * @param mixed $invitationToken
     */
    public function setInvitationToken($invitationToken)
    {
        $this->invitationToken = $invitationToken;
    }


    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    /**
     * @param mixed $$comment
     */
    public function addComments($comment){
        $this->comments[] = $comment;
    }

    /**
     * @param mixed
     */
    public function removeComments($comment){
        $this->comments->removeElement($comment);
    }

    /**
     * @param mixed $readBooks
     */
    public function setReadBooks($readBooks)
    {
        $this->readBooks = $readBooks;
    }

    /**
     * @param mixed $$comment
     */
    public function addReadBooks($readBook){
        $this->readBooks[] = $readBook;
    }

    /**
     * @param mixed
     */
    public function removeReadBooks($readBook){
        $this->readBooks->removeElement($readBook);
    }

    /**
     * @param mixed $borrowedBooks
     */
    public function setBorrowedBooks($borrowedBooks)
    {
        $this->borrowedBooks = $borrowedBooks;
    }

    /**
     * @param mixed $$comment
     */
    public function addBorrowedBooks($borrowedBook){
        $this->borrowedBooks[] = $borrowedBook;
    }

    /**
     * @param mixed
     */
    public function removeBorrowedBooks($borrowedBook){
        $this->borrowedBooks->removeElement($borrowedBook);
    }

    /**
     * @param mixed $borrowedBooks
     */
    public function setLentBooks($lentBooks)
    {
        $this->lentBooks = $lentBooks;
    }

    /**
     * @param mixed $$comment
     */
    public function addLentBooks($lentBook){
        $this->lentBooks[] = $lentBook;
    }

    /**
     * @param mixed
     */
    public function removeLentBooks($lentBook){
        $this->lentBooks->removeElement($lentBook);
    }

    /**
     * @return mixed
     */
    public function getBorrowedBooks()
    {
        return $this->borrowedBooks;
    }

    /**
     * @return mixed
     */
    public function getLentBooks()
    {
        return $this->lentBooks;
    }

    /**
     * @return mixed
     */
    public function getReadBooks()
    {
        return $this->readBooks;
    }
}