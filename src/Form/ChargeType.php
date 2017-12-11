<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ChargeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('title', null, ['label' => 'Nom'])
            ->add('amount', NumberType::class, ['label' => 'Montant'])
            ->add('deadline', DateType::class, ['label' => 'Date de fin'])
            ->add('authorized_user', EntityType::class, [
                'class' => 'App\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.username != :admin')
                        ->setParameter('admin', 'admin')
                        ->orderBy('u.username', 'ASC');
                },
                'label' => 'Utilisateur autorisé à voir',
                'multiple' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => [
                'class' => 'btn-success'
            ]]);
    }
}