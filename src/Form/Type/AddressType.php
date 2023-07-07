<?php

namespace HouseOfAgile\NakaCMSBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', TextareaType::class, [
                'label' => 'form.addressForm.street',
                'attr' => [
                    'placeholder' => 'form.addressForm.street',
                ],
                'row_attr' => [
                    'class' => 'form-floating m-4',
                ],
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'form.addressForm.zipcode',
                'attr' => [
                    'placeholder' => 'form.addressForm.zipcode',
                ],
                'row_attr' => [
                    'class' => 'form-floating m-4',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'form.addressForm.city',
                'attr' => [
                    'placeholder' => 'form.addressForm.emacityl',
                ],
                'row_attr' => [
                    'class' => 'form-floating m-4',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => 'form.addressForm.country',
                'attr' => [
                    'placeholder' => 'form.addressForm.country',
                ],
                'row_attr' => [
                    'class' => 'form-floating m-4',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'inherit_data' => true,
        ]);
    }
}