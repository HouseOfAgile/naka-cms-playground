<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends UserInfoType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'form.member.agreeTerms',
                'label_attr' => ['class' => 'py-2'],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'form.member.agreeTerms.constraint.isTrue',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'label' => 'form.member.password',
                'help' => 'form.member.password.help',
                'type' => PasswordType::class,
                'invalid_message' => 'form.member.password.invalidMessage',
                'attr' => ['autocomplete' => 'new-password'],
                'mapped' => false,

                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'form.member.password',
                    'attr' => [
                        'placeholder' => 'form.member.password',
                    ],
                    'row_attr' => [
                        'class' => 'form-floating m-4',
                    ]
                ],
                'second_options' => [
                    'label' => 'form.member.repeatPassword',
                    'attr' => [
                        'placeholder' => 'form.member.repeatPassword',
                    ],
                    'row_attr' => [
                        'class' => 'form-floating m-4',
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.member.password.constraint.pleaseEnterAPassword',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('register', SubmitType::class, [
                'label' => 'form.member.register',
            ]);
    }
}
