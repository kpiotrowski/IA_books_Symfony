<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * http://openlibrary.org/search.json?q=name - Tego API używam do wyszukiwania
 * Class BookController
 * @package AppBundle\Controller
 * @Route("book")
 */
class BookController extends Controller
{
    /**
     * @Route("/", name="books_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $errors = array();
        $results = array();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $qb = $em->createQueryBuilder();
        $query = $qb->select('i')
            ->from('AppBundle:Book', 'i')
            ->where('i.library = :lib')
            ->setParameter('lib', $user->getLibrary() );
        if ( isset($_GET['titleOrAuthor'])) {
            $search = $_GET['titleOrAuthor'];
            $search = '%'.$search.'%';
            $query->andWhere('i.title LIKE :titleOrAuthor OR i.author LIKE :titleOrAuthor');
            $query->setParameter('titleOrAuthor', $search );
        }
        $pageNum = 1;
        if ( isset($_GET['pageNum'])) {
            $pageNum = $_GET['pageNum'];
            if ($pageNum < 1) $pageNum = 1;
        }


        $results = $query->getQuery()->getResult();
        $pages = ceil(count($results)/10);
        if ($pageNum > $pages) $pageNum = $pages;
        $results = array_slice($results, 10*($pageNum-1), 10);

        return $this->render('AppBundle::Book/index.html.twig', array(
            'books' => $results,
            'errors' => $errors,
            'pages' => $pages,
            'currPage' =>$pageNum,
        ));
    }

    /**
     * @Route("/show/{id}", name="book_show")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function showAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $re = $em->getRepository(Book::class);
        $book = $re->findOneById($id);
        $title = $book->getTitle();
        if ($book) {
            $user = $this->get('security.token_storage')->getToken()->getUser();
            if ($book->getLibrary() != $user->getLibrary()) {
                return array(
                    'errors' => array('This book is not in your library! You have no permission to see it.')
                );
            }
            $form = $this->createForm('AppBundle\Form\BookType', $book);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $book2 = $form->getData();
                if ( $title != $book2->getTitle()) {
                    $book->setTitle($title);
                    $form = $this->createForm('AppBundle\Form\BookType', $book);
                    return array(
                        'errors' => array('You can\'t modify book title'),
                        'form' => $form->createView(),
                        'book' => $book,
                    );
                }
                $em->persist($book2);
                $em->flush();
                return $this->redirectToRoute('book_show', array('id' => $id));
            }

            return $this->render('AppBundle::Book/show.html.twig', [
                'form' => $form->createView(),
                'book' => $book,
            ]);
        } else {
            return array(
                'errors' => array('This book doesn\'t exist')
            );
        }


    }

    /**
     * Creates a new book entity.
     *
     * @Route("/new", name="book_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $errors = array();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->getLibrary()) {
            $library = $user->getLibrary();
            $book = new Book();
            $form = $this->createForm('AppBundle\Form\BookType', $book);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $book->setLibrary($library);
                $book->setCreatedBy($user);
                $library->addBooks($book);


                $em->persist($book);
                $em->flush();
                return $this->redirectToRoute('books_index');
            }

            return $this->render('AppBundle::Book/new.html.twig', array(
                'invitation' => $book,
                'form' => $form->createView(),
            ));
        } else {
            $url = $this->generateUrl('books_index');
            return new RedirectResponse($url);
        }
    }

}
