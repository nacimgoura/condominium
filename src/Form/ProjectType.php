<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $user = $options['user'];

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du projet'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices'  => [
                    'En discussion' => 'En discussion',
                    'En attente d\'exécution' => 'En attente d\'exécution',
                    'Exécuté' => 'Exécuté',
                ],
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd h:m',
                'attr' => [
                    'class' => 'flatpickr'
                ]
            ])
            ->add('authorizedUser', EntityType::class, [
                'class' => 'App\Entity\User',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('u')
                        ->where("u.username != 'admin' AND u.condominium = :id")
                        ->setParameter('id', $user->getCondominium()->getId())
                        ->orderBy('u.username', 'ASC');
                },
                'label' => 'Utilisateur lié',
                'multiple' => true
            ])
            ->add('sondage', EntityType::class, [
                'class' => 'App\Entity\Sondage',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('s')
                        ->where("s.user = :id")
                        ->setParameter('id', $user->getId())
                        ->orderBy('s.question', 'ASC');
                },
                'label' => 'Vos sondages',
                'multiple' => true
            ])
            ->add('attachment', FileType::class, [
                'label' => 'pièce jointe',
                'required' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => [
                'class' => 'btn-success'
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(['user' => null]);
    }
}
