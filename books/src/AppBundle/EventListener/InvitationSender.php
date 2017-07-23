<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Invitation;

class InvitationSender
{
    protected $twig;
    protected $mailer;
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Invitation)
            return;

        $message = \Swift_Message::newInstance()
            ->setSubject('New invitation!')
            ->setFrom('kpiotrowski@lociechocinek.pl')
            ->setTo($entity->getUserEmail())
            ->setBody(
                $this->twig->render(
                // app/Resources/views/Emails/registration.html.twig
                    'AppBundle::Emails/invitation.html.twig',
                    array('user' => $entity->getUser(), 'token' => $entity->getToken())
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);

    $entityManager = $args->getEntityManager();
    // ... do something with the Product
    }
}