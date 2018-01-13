<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('title', null, ['label' => 'Nom'])
            ->add('deadline', DateType::class, [
                'label' => 'Date d\'échéance',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'yyyy-MM-dd h:m',
                'attr' => [
                    'class' => 'flatpickr'
                ]
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
}