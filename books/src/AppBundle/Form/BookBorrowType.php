<?php

namespace AppBundle\Form;

use AppBundle\AppBundle;
use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookBorrowType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $books = $entity->getBookOwner()->getLibrary()->getBooks();
        foreach ($books as $key => $item) {
            if ($item -> getBorrowLibrary()) unset($books[$key]);
        };


        $builder->
        add('book', EntityType::class, array(
            'class' => 'AppBundle:Book',
            'choice_label' => 'title',
            'choices' => $books
        ))->
        add('userEmail', EmailType::class)->
        add('save', SubmitType::class)

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\BookBorrow'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_book_borrow';
    }


}
