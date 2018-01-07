<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SondageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options){

        $builder
            ->add('question')
            ->add('choice', CollectionType::class, [
                'label' => 'Réponses possibles',
                'entry_type' => TextType::class,
                'allow_add' => true,
                'prototype' => true
            ])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => [
                'class' => 'btn-success'
            ]]);
    }
}