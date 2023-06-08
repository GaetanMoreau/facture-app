<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

class ClientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Lastname', null, [
                'constraints' => new NotBlank(),
            ])
            ->add('Firstname', null, [
                'constraints' => new NotBlank(),
            ])
            ->add('Society')
            ->add('email', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Email([
                        'mode' => 'strict',
                    ]),
                ],
            ])
            ->add('Phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'form.validation.phone',
                    ])
                ]
            ])

            ->add('Job')
            ->add('Address')
            ->add('Additional_address')
            ->add('Postal_code')
            ->add('City')
            ->add('Country')
            ->add('Website')
            ->add('Comments', TextareaType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
