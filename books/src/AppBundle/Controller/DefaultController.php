<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Invitation;
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
        return array();
    }

    /**
     * @Route("/searchOpenLibrary", name="search_open_library")
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

}
