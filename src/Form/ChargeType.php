<?php

namespace App\Form;

use App\Entity\Charge;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
        $charge = $options['charge'];

        $condominiumId = null;

        if ($charge->getCondominium()) {
            $condominiumId = $charge->getCondominium()->getId();
        } else if ($user->getCondominium()) {
            $condominiumId = $user->getCondominium()->getId();
        }

        $builder
            ->add('title', null, ['label' => 'Nom'])
            ->add('amount', NumberType::class, [
                'label' => 'Montant',
                'scale' => 2
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
            ->add('user', EntityType::class, [
                'class' => 'App\Entity\User',
                'query_builder' => function (EntityRepository $er) use ($user, $condominiumId) {
                    return $er->createQueryBuilder('u')
                        ->where("u.username != 'admin' AND u.condominium = :id")
                        ->setParameter('id', $condominiumId)
                        ->orderBy('u.username', 'ASC');
                },
                'label' => 'Personnes concernées',
                'multiple' => true
            ])
            ->add('contract', EntityType::class, [
                'class' => 'App\Entity\Contract',
                'query_builder' => function (EntityRepository $er) use ($user, $condominiumId) {
                    return $er->createQueryBuilder('contract')
                        ->where("contract.condominium = :id")
                        ->setParameter('id', $condominiumId)
                        ->orderBy('contract.title', 'ASC');
                },
                'placeholder' => 'Choisissez un contrat',
                'label' => 'Contrat lié',
                'required' => false
            ])
            ->add('attachment', FileType::class, [
                'label' => 'pièce jointe',
                'required' => false,
                'data_class' => null
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => [
                'class' => 'btn-success'
            ]]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'user' => null,
            'charge' => null
        ]);
    }
}