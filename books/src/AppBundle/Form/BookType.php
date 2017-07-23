<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->
            add('title')->
            add('subtitle', null, array(
                'required' => false,
            ))->
            add('description', TextareaType::class, array(
                'required' => false,
            ))->
            add('author')->
            add('pages', null, array(
                'required' => false,
            ))->
            add('publicationDate', DateTimeType::class, array(
                'date_format'=> 'dd-MM-yyyy',
                'format'=> 'dd-MM-yyyy',
                'years' => range(1700, 2100),
                'time_widget' => null,
                'required' => false,
        ))->
            add('save', SubmitType::class)

        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Book'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_book';
    }


}
