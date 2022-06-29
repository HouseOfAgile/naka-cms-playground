<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'form.contact.name'])
            ->add('email', EmailType::class, ['label' => 'form.contact.email'])
            ->add('subject', TextType::class, ['label' => 'form.contact.subject'])
            ->add('message', TextareaType::class, [
                'label' => 'form.contact.message',
                'attr' => array('rows' => '6')
            ])
            ->add('submit', SubmitType::class, ['label' => 'form.contact.submit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
