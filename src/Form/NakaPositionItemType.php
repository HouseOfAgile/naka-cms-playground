<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NakaPositionItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['items'] as $item) {
            # code...
            $builder->add('item_' . $item->getId(), TextType::class, [
                'attr' => ['class' => 'bg-info p-3 text-light rounded'],
                'block_prefix' => 'sortable_block',
                'label' => $item->getName(),
                'data' => $item->getPosition(),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'items' => [],
        ]);
    }
}
