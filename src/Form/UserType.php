<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){
        $builder
            ->add('username', null, ['label' => 'Identifiant'])
            ->add('firstname', null, ['label' => 'Prénom'])
            ->add('lastname', null, ['label' => 'Nom de famille'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('condominium',EntityType::class, [
                'class' => 'App\Entity\Condominium',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('co')
                        ->orderBy('co.title', 'ASC');
                },
                'label' => 'Copropriété'
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'ROLE_USER' => 'ROLE_USER',
                    'ROLE_MANAGER' => 'ROLE_MANAGER'
                ],
                'multiple' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => [
                'class' => 'btn-success'
            ]]);
    }
}