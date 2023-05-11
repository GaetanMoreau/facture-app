<?php

namespace App\Form;

use App\Entity\Estimate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EstimateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount')
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'Brouillon' => 'Brouillon',
                    'Signé' => 'Signé',
                    'Finalisé' => 'Finalisé',
                    'Refusé' => 'Refusé'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Estimate::class,
        ]);
    }
}
