<?php

namespace HouseOfAgile\NakaCMSBundle\Form\Backend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackendAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('street', TextareaType::class, [
                'label' => 'form.addressForm.street',
                'attr' => [
                    'placeholder' => 'form.addressForm.street',
                ],
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'form.addressForm.zipcode',
                'attr' => [
                    'placeholder' => 'form.addressForm.zipcode',
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'form.addressForm.city',
                'attr' => [
                    'placeholder' => 'form.addressForm.city',
                ],
            ])
            ->add('country', CountryType::class, [
                'label' => 'form.addressForm.country',
                'required' => false,
                'attr' => [
                    'placeholder' => 'form.addressForm.country',
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