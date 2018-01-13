<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CondominiumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
            ->add('title', null, ['label' => 'Nom de la copropriété'])
            ->add('save', SubmitType::class, ['label' => 'Valider', 'attr' => [
                'class' => 'btn-success'
            ]]);
    }
}