<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\MenuItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NakaMenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', null, [
            'empty_data' => '',
            'disabled' => true]);
        $builder->add('position');

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuItem::class,
        ]);
        $resolver->setDefault('block_prefix', 'sortable_block');
        $resolver->setDefault('attr', ['class' => 'bg-info text-light rounded']);
    }
}
