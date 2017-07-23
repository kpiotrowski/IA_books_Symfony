<?php


namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteNotConfirmedCommand extends ContainerAwareCommand
    {

    protected function configure()
    {
        $this
        ->setName('app:delete-not-confirmed')
        ->setDescription('Delete not confirmed users')
        ->setHelp('Komenda usuwa wszystkich uzytkowników którzy nie potwierdzili swojej rejestracji w ciągu 24h')
    ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Started deleting users");

        $container = $this->getContainer();

        $em = $container->get('doctrine')->getManager();
        $qb = $em->createQueryBuilder();
        $results = $qb->delete()
            ->from('AppBundle:User', 'u')
            ->where('u.enabled = 0')
            ->andWhere('u.createdAt <= :date')
            ->setParameter('date', new \DateTime('-24 hours'))
            ->getQuery()
            ->getResult();

        $output->writeln($results);
        $output->writeln("Finished deleting user");
    }
}


?>