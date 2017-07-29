<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BookBorrow;
use AppBundle\Entity\BookRead;
use AppBundle\Entity\Invitation;
use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use AppBundle\Form\BookBorrowType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * invitation controller.
 *
 * @Route("borrow")
 */
class BookBorrowController extends Controller
{
    /**
     * Lists all invitation entities.
     *
     * @Route("/", name="book_borrow_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return array(
            'borrowedBooks' => $user->getBorrowedBooks(),
            'lentBooks' => $user->getLentBooks()
        );

    }

    /**
     * @Route("/new_book", name="new_book_borrow")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newBookAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->getLibrary()) {
            $book = new Book();
            $form = $this->createForm('AppBundle\Form\BookType', $book);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {


                $book->setBorrowLibrary($user->getLibrary());
                $book->setCreatedBy($user);

                $user->getLibrary()->addBorrowedBooks($book);

                $borrow = new BookBorrow();
                $borrow->setBook($book);
                $borrow->setBookUser($user);
                $borrow->setTime(new \DateTime());
                $book->setCurrentBorrow($borrow);

                $em->persist($book);
                $em->persist($borrow);
                $em->flush();
                return $this->redirectToRoute('book_borrow_index');
            }

            return $this->render('AppBundle::BookBorrow/newBook.html.twig', array(
                'form' => $form->createView(),
            ));
        } else {
            return $this->redirectToRoute('book_borrow_index');
        }
    }

    /**
     * @Route("/borrow_book/", name="borrow_book")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function borrowAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $borrow = new BookBorrow();
        $borrow->setBookOwner($user);
        $borrow->setTime(new \DateTime());

        $form = $this->createForm(BookBorrowType::class, $borrow);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $borrow = $form->getData();
            $re = $em->getRepository(User::class);

            $bookUser = $re->findOneByEmail($borrow->getUserEmail());
            if ($bookUser == null) {
                return $this->render('AppBundle::BookBorrow/borrow.html.twig', [
                    'form' => $form->createView(),
                    'errors' => array('Ten uzytkownik nie istnieje')
                ]);
            }

            $book = $borrow->getBook();
            $borrow->setBookUser($bookUser);
            $borrow->setBookOwner($user);
            $borrow->setTime(new \DateTime());


            if ($book->getLibrary() == $user->getLibrary() && $borrow->getBookUser()->getLibrary() != $user->getLibrary()){
                $user->addLentBooks($borrow);
                $borrow->getBookUser()->addBorrowedBooks($borrow);

                $borrow->getBook()->setBorrowLibrary($borrow->getBookUser()->getLibrary());
                $borrow->getBookUser()->getLibrary()->addBorrowedBooks($book);
                $borrow->getBook()->setCurrentBorrow($borrow);

                $em->persist($borrow);

                $em->flush();
                return $this->redirectToRoute('book_borrow_index');
            } else {

                return $this->render('AppBundle::BookBorrow/borrow.html.twig', [
                    'form' => $form->createView(),
                    'errors' => array('Książka nie jest w Twojej bibliotece lub próbujesz wupozyczyć ją komuś kto jest w Twojej biblitece')
                ]);

            }
        }

        return $this->render('AppBundle::BookBorrow/borrow.html.twig', [
            'form' => $form->createView()

        ]);
    }

    /**
     * @Route("/return_book/{bookId}", name="return_book")
     * @Method("POST")
     * @Template()
     */
    public function returnBook($bookId){
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $re = $em->getRepository(Book::class);
        $book = $re->findOneById($bookId);
        if ($book) {
            $borrow = null;
            $reading = null;
            foreach ($user->getBorrowedBooks() as $bb) {
                if ($bb->getBook() == $book && $bb->getFinishTime() == null){
                    $borrow = $bb;
                    break;
                }
            }
            foreach ($user->getReadBooks() as $rb){
                if ($rb->getBook() == $book && $rb->getStatus() == BookRead::READING){
                    $reading = $rb;
                    break;
                }
            }
            if ($borrow){
                $user->getLibrary()->removeBorrowedBooks($borrow);
                $book->setBorrowLibrary(null);
                $book->setCurrentBorrow(null);

                $borrow->setFinishTime(new \DateTime());
                if ($reading){
                    $reading->setEndDate(new \DateTime);
                    $reading->setStatus(BookRead::FINISHED);
                }
                $em->flush();
            }
        }
        return $this->redirectToRoute('book_borrow_index');

    }



}
