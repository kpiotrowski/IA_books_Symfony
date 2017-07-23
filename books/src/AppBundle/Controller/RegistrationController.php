<?php
// src/AppBundle/Controller/RegistrationController.php

namespace AppBundle\Controller;

use AppBundle\Entity\Library;
use AppBundle\Entity\User;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Skorzystałem z FOS User Bundle do rejestracji, niestety musiałem nadpisać niektóre f-cje aby zaimplementować rejestracje
 * przez zaproszenie (wygenerowany specjalny token)
 * Class RegistrationController
 * @package AppBundle\Controller
 */
class RegistrationController extends BaseController
{
    public function confirmAction(Request $request, $token)
    {
        $result =  parent::confirmAction($request, $token);
        $user = $this->getUser();
        if($user && !$user->getLibrary()){
            $em = $this->getDoctrine()->getManager();
            if($user->getInvitationToken())
                $this->assignUserToBookLibrary($em, $user, $user->getInvitationToken());
            else
                $this->createLibraryForUser($em, $user);
            $em->flush();
        }
        return $result;
    }

    public function registerAction(Request $request)
    {
        $formFactory = $this->get('fos_user.registration.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        $form = $formFactory->createForm();
        $form->setData($user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                /****************************TO DODAŁEM **************************/
                if($request->request->get('book_library_invitation_token')){
                    $user->setInvitationToken($request->request->get('book_library_invitation_token'));
                }
                /*****************************************************************/


                $userManager->updateUser($user);
                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                return $response;
            }
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);
            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }
        return $this->render('@FOSUser/Registration/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    /**
     * @param $em
     * @param User $user
     * @param $lib_token
     */
    public function assignUserToBookLibrary($em, $user, $lib_token)
    {
        $qb = $em->createQueryBuilder();
        $results = $qb->select('i')
            ->from('AppBundle:Invitation', 'i')
            ->where('i.enabled = 1')
            ->andWhere('i.token = :token')
            ->setParameter('token', $lib_token)
            ->getQuery()
            ->getResult();
        if($results && count($results)==1 ) {
            $inv = $results[0];
            $inv->setEnabled(0);
            $inv->getLibrary()->addUsers($user);
            $user->setLibrary($inv->getLibrary());
        }
        else {
            $this->createLibraryForUser($em, $user);
        }
    }

    public function createLibraryForUser($em, $user)
    {
        $library = new Library();
        $library->addUsers($user);
        $library->setOwner($user);
        $em->persist($library);
        $user->setLibrary($library);
    }
}