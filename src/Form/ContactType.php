<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    const FORM_CONTROL = 'form-control form-control-lg';
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'form.contact.name',
                'attr' => [
					'placeholder' => 'form.contact.name',
                    'class' => $this::FORM_CONTROL,
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.contact.email',
                'attr' => [
					'placeholder' => 'form.contact.email',
                    'class' => $this::FORM_CONTROL,
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'form.contact.subject',
                'attr' => [
					'placeholder' => 'form.contact.subject',
                    'class' => $this::FORM_CONTROL,
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'form.contact.message',
                'attr' => [
					'placeholder' => 'form.contact.message',
                    'rows' => '6',
                    'style' => 'height: 140px;',
                    'class' => $this::FORM_CONTROL,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
