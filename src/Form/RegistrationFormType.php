<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\User;
use HouseOfAgile\NakaCMSBundle\Form\Type\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'form.registerForm.firstName',
                'attr' => [
                    'placeholder' => 'form.registerForm.firstName',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.registerForm.lastName',
                'attr' => [
                    'placeholder' => 'form.registerForm.lastName',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('birthDate', BirthdayType::class, [
                'label' => 'form.registerForm.birthDate',
                'help' => 'form.registerForm.birthDate.help',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'p-3 js-datepicker text-white'],
            ])
            ->add('address', AddressType::class, [
                'label' => 'form.registerForm.address',
                'help' => 'form.registerForm.address.help',
                'data_class' => User::class,
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.registerForm.email',
                'attr' => [
                    'placeholder' => 'form.registerForm.email',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'form.registerForm.agreeTerms',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'form.registerForm.agreeTerms.constraint.isTrue',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'label' => 'form.registerForm.password',
                'help' => 'form.registerForm.password.help',
                'type' => PasswordType::class,
                'invalid_message' => 'form.registerForm.password.invalidMessage',
                'attr' => ['autocomplete' => 'new-password'],
                'mapped' => false,

                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'form.registerForm.password',
                    'attr' => [
                        'placeholder' => 'form.registerForm.password',
                    ],
                    'row_attr' => [
                        'class' => 'form-floating m-4',
                    ]
                ],
                'second_options' => [
                    'label' => 'form.registerForm.repeatPassword',
                    'attr' => [
                        'placeholder' => 'form.registerForm.repeatPassword',
                    ],
                    'row_attr' => [
                        'class' => 'form-floating m-4',
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'form.registerForm.password.constraint.pleaseEnterAPassword',
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
                'label' => 'form.registerForm.register',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
