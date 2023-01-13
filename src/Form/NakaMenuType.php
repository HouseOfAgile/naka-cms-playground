<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\Menu;
use App\Entity\MenuItem;
use HouseOfAgile\NakaCMSBundle\Form\NakaMenuItemType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NakaMenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('menuItems', CollectionType::class, [
            'entry_type' => NakaMenuItemType::class,
            'entry_options' => ['label' => false],
        ]);
        $builder->add('newOrder', HiddenType::class, [
            'mapped' => false,
            'data' => implode(',',$options['orderedMenuItemsArray']),
            'attr' => ['class' => 'new-order']
        ]);
        $builder
            // ->add('menuItems')
            ->add('submit', SubmitType::class, ['label' => 'backend.form.configureMenu.submit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
            'orderedMenuItemsArray' => [],
        ]);
    }
}
