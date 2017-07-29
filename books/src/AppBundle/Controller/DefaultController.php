<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Invitation;
use AppBundle\Entity\Book;
use AppBundle\Entity\BookRead;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $query = $qb->select('i')
            ->from('AppBundle:BookRead', 'i')
            ->where('i.user = :user AND i.status = :stat')
            ->setParameter('user', $user )
            ->setParameter('stat', BookRead::READING)
        ;
        $readingBooks = $query->getQuery()->getResult();
        $query2 = $qb->select('k')
            ->from('AppBundle:BookRead', 'k')
            ->where('k.user = :user AND k.status = :stat')
            ->setParameter('user', $user )
            ->setParameter('stat', BookRead::FINISHED)
        ;
        $finishedBooks = $query2->getQuery()->getResult();
        $readBookAverage = 'na';
        $readDayPages = 'na';
        $pages = 0;
        foreach ($finishedBooks as $fb){
            $pages += $fb->getBook()->getPages();
        }
        $days_diff = intval(($user->getCreatedAt()->diff(new \DateTime))->format('%a'));
        if ($days_diff != 0) {
            $readDayPages = round($pages / $days_diff,1);
        }
        if (count($finishedBooks) > 0 ) {
            $readBookAverage = round($days_diff / count($finishedBooks),1);
        }

        return array(
            'readingBooks' => $readingBooks,
            'finishedBooks' => $finishedBooks,
            'readingCount' => count($readingBooks),
            'finishedCount' => count($finishedBooks),
            'readPages' => $pages,
            'readDayPages' => $readDayPages,
            'readBookAverage' => $readBookAverage,
            'username' => $user->getUsername(),
            'commentsNum' => count($user->getComments()),
            'borrowedNumber' =>count($user->getBorrowedBooks()),
            'lentNumber' => count($user->getLentBooks())
        );
    }

    /**
     * @Route("/searchOpenLibrary", options={"expose"=true}, name="search_open_library")
     */
    public function searchOpenLibrary(){
        $href = "http://openlibrary.org/search.json?q=".urlencode($_GET['q']);
        $json = file_get_contents($href);
        $obj = json_decode($json);
        $results = $obj->docs;
        $results = array_slice($results, 0, 20);

        $books = array();
        $x=0;
        foreach ($results as $result){
            $book = array(
                'id' => $x++,
                'text' => $result->title,
                'title' => $result->title,
                'subtitle' => isset($result->subtitle)?$result->subtitle:"",
                'author' => isset($result->author_name)?$result->author_name:"",
                'person' => isset($result->person)?$result->person:"",
                'time' => isset($result->time)?$result->time:"",
                'place' => isset($result->place)?$result->place:""
            );
            $books[] = $book;
        }

        return new JsonResponse(array(
                'items' => $books,
                'total_count' => count($books),
                'page' => 1
            )
        );
    }

    /**
     * @Route("/bookReadAction/{bookId}", name="book_read_action")
     */
    public function bookReadAction($bookId, Request $request){

        $em = $this->getDoctrine()->getManager();
        $re = $em->getRepository(Book::class);
        $book = $re->findOneById($bookId);
        if ($book) {
            $user = $this->get('security.token_storage')->getToken()->getUser();

            $qb = $em->createQueryBuilder();
            $query = $qb->select('i')
                ->from('AppBundle:BookRead', 'i')
                ->where('i.book = :book AND i.user = :user AND i.status = :stat')
                ->setParameter('book', $book)
                ->setParameter('user', $user )
                ->setParameter('stat', BookRead::READING)
            ;
            $result = $query->getQuery()->getResult();
            $action = $request->request->get('action');
            if (count($result) == 0 && $action == 'start') {
                $bookR = new BookRead();
                $bookR->setStartDate(new \DateTime);
                $bookR->setBook($book);
                $bookR->setUser($user);
                $bookR->setStatus(BookRead::READING);
                $em->persist($bookR);
                $em->flush();
            }
            if (count($result) > 0 && $action == 'stop') {
                $result[0]->setEndDate(new \DateTime);
                $result[0]->setStatus(BookRead::FINISHED);
                $em->persist($result[0]);
                $em->flush();
            }

        }

        return $this->redirectToRoute('book_show', array('id' => $bookId));
    }

}
