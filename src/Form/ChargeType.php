<?php

namespace App\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChargeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $user = $options['user'];

        $builder
            ->add('title', null, ['label' => 'Nom'])
            ->add('amount', NumberType::class, [
                'label' => 'Montant',
                'scale' => 2
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd h:m',
                'attr' => [
                    'class' => 'flatpickr'
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => 'App\Entity\User',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('u')
                        ->where("u.username != 'admin' AND u.condominium = :id")
                        ->setParameter('id', $user->getCondominium()->getId())
                        ->orderBy('u.username', 'ASC');
                },
                'label' => 'Personnes concernées',
                'multiple' => true
            ])
            ->add('contract', EntityType::class, [
                'class' => 'App\Entity\Contract',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('contract')
                        ->where("contract.condominium = :id")
                        ->setParameter('id', $user->getCondominium()->getId())
                        ->orderBy('contract.title', 'ASC');
                },
                'placeholder' => 'Choisissez un contrat',
                'label' => 'Contrat lié'
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