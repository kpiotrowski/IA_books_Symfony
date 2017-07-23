<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Invitation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * invitation controller.
 *
 * @Route("invitation")
 */
class InvitationController extends Controller
{
    /**
     * Lists all invitation entities.
     *
     * @Route("/", name="invitation_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $errors = array();
        $results = array();
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->getId() == $user->getLibrary()->getOwner()->getId()) {
            $qb = $em->createQueryBuilder();
            $results = $qb->select('i')
                ->from('AppBundle:Invitation', 'i')
                ->where('i.user = :user')
                ->setParameter('user', $user)
                ->getQuery()
                ->getResult();
        }
        else $errors[] = "Only library owner is able to see and add invitations!";

        return $this->render('AppBundle::invitation/index.html.twig', array(
            'invitations' => $results,
            'errors' => $errors
        ));
    }

    /**
     * Creates a new invitation entity.
     *
     * @Route("/new", name="invitation_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $errors = array();
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->getId() == $user->getLibrary()->getOwner()->getId()) {
            $invitation = new Invitation();
            $form = $this->createForm('AppBundle\Form\InvitationType', $invitation);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->addInvitations($invitation);
                $invitation->setUser($user);
                $invitation->setLibrary($user->getLibrary());
                $em->persist($invitation);
                $em->flush();
                $this->sendInvitation($invitation);

                return $this->redirectToRoute('invitation_index');
            }

            return $this->render('AppBundle::invitation/new.html.twig', array(
                'invitation' => $invitation,
                'form' => $form->createView(),
            ));
        } else {
            $url = $this->generateUrl('invitation_index');
            return new RedirectResponse($url);
        }


    }


    /**
     * @param Invitation $invitation
     */
    public function sendInvitation($invitation){
        $message = \Swift_Message::newInstance()
            ->setSubject('New invitation!')
            ->setFrom($this->getParameter('mailer_user'))
            ->setTo($invitation->getUserEmail())
            ->setBody(
                $this->renderView(
                    'AppBundle::Emails/invitation.html.twig',
                    array('user' => $invitation->getUser(), 'token' => $invitation->getToken())
                ),
                'text/html', 'utf-8'
            )
        ;
        $this->get('mailer')->send($message);
    }

}
