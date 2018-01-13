<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $user = $options['user'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('sondage', EntityType::class, [
                'class' => 'App\Entity\Sondage',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('s')
                        ->where("s.user = :id")
                        ->setParameter('id', $user->getId())
                        ->orderBy('s.question', 'ASC');
                },
                'label' => 'Sondage pour le choix de date',
                'placeholder' => 'Choix de la date'
            ])
            ->add('date', DateType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd h:m',
                'attr' => [
                    'class' => 'flatpickr'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => [
                'class' => 'btn-success'
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'user' => null
        ]);
    }
}