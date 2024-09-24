<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\User;
use HouseOfAgile\NakaCMSBundle\Form\Type\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoType extends AbstractType
{
    public function __construct(
        protected RequestStack $requestStack,
    ) {
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale() ?: 'de';

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
                'label_attr' => ['class' => 'pt-3 px-3'],
                'help' => 'form.member.birthDate.help',
                'required' => false,
                'format' => $locale == 'de' ? 'd.M.y' : 'd/M/y',
                'widget' => 'single_text',
                'html5' => false,
                'error_bubbling' => false,
                'attr' => ['class' => 'p-3 js-datepicker text-center'],
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
            ->add('address', AddressType::class, [
                'label' => 'form.member.address',
                'help' => 'form.member.address.help',
                'data_class' => User::class,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
