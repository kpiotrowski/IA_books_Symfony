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


    public function __construct()
    {
        parent::__construct();
        $this->createdAt = new \DateTime;
        $this->invitations = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
}