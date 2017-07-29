<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * invitation controller.
 *
 * @Route("comment")
 */
class CommentController extends Controller
{

    /**
     * Creates a new book entity.
     *
     * @Route("/new/{book_id}", name="comment_new")
     * @Method({"POST"})
     * @Template()
     */
    public function newAction($book_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $re = $em->getRepository(Book::class);
        $book = $re->findOneById($book_id);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if ($book->getLibrary() != $user->getLibrary() && $book->getBorrowLibrary() != $user->getLibrary()) {
            return $this->redirectToRoute('book_show', array('id' => $book_id));
        }

        $comm = new Comment();
        $form = $this->createForm('AppBundle\Form\CommentType', $comm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->addComments($comm);
            $user->addComments($comm);
            $comm->setAuthor($user);
            $comm->setBook($book);
            $comm->setComDate(new \DateTime);

            $em->persist($comm);
            $em->flush();
            return $this->redirectToRoute('book_show', array('id' => $book_id));
        }

        return $this->redirectToRoute('book_show', array('id' => $book_id));
    }


}