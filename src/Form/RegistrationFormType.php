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
                'label' => 'form.member.firstName',
                'attr' => [
                    'placeholder' => 'form.member.firstName',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'form.member.lastName',
                'attr' => [
                    'placeholder' => 'form.member.lastName',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('birthDate', BirthdayType::class, [
                'label' => 'form.member.birthDate',
                'help' => 'form.member.birthDate.help',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'p-3 js-datepicker text-white'],
            ])
            ->add('address', AddressType::class, [
                'label' => 'form.member.address',
                'help' => 'form.member.address.help',
                'data_class' => User::class,
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.member.email',
                'attr' => [
                    'placeholder' => 'form.member.email',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'form.member.agreeTerms',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
