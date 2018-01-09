<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConversationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $user = $options['user'];

        $builder
            ->add('title', null, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('content', TextareaType::class, ['label' => 'Votre post', 'attr' => [
                'class' => 'editor-html'
            ]]);

        /**
         * si on est pas admin on affiche seulement la liste des membres de sa résidence
         */
        if ($user->getCondominium()) {
            $builder->add('authorizedUser', EntityType::class, [
                'class' => 'App\Entity\User',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('u')
                        ->where("u.username != 'admin' AND u.condominium = :id")
                        ->setParameter('id', $user->getCondominium()->getId())
                        ->orderBy('u.username', 'ASC');
                },
                'label' => 'Utilisateur autorisé à voir',
                'multiple' => true
            ])
                ->add('project', EntityType::class, [
                    'class' => 'App\Entity\Project',
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        return $er->createQueryBuilder('p')
                            ->where("p.condominium = :id")
                            ->setParameter('id', $user->getCondominium()->getId())
                            ->orderBy('p.title', 'ASC');
                    },
                    'label' => 'Projet lié'
                ]);
        } else if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $builder->add('authorizedUser', EntityType::class, [
                'class' => 'App\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where("u.username != 'admin'")
                        ->orderBy('u.username', 'ASC');
                },
                'label' => 'Utilisateur autorisé à voir',
                'multiple' => true
            ]);
        }
        $builder->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => [
            'class' => 'btn-success'
        ]]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(['user' => null]);
    }
}