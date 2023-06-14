<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\PageBlockElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageBlockElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder->add('name', null, ['empty_data'=>'']);
        $builder->add('position');

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageBlockElement::class,
        ]);
        $resolver->setDefault('block_prefix', 'sortable_block');
        $resolver->setDefault('attr',  ['class' => 'bg-info text-light rounded']);
    }
}
