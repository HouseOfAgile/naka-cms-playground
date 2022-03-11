<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\BlockElement;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CodeEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlockElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('htmlCode', TextareaType::class, [
                'required' => false,
            ])
            ->add('jsCode', TextareaType::class, [
                'required' => false,
            ])
            ->add('cssCode', TextareaType::class, [
                'required' => false,
            ])
            ->add('name', TextType::class)
            // ->add('type')
            // ->add('pictures')
            ;
        $builder->add('submit', SubmitType::class);
        if ($options['add_refresh']) {
            $builder->add('refresh', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlockElement::class, 'add_refresh' => false,
        ]);
    }
}
