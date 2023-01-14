<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\Page;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlocksInPagePositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('pageBlockElements', CollectionType::class, [
            'entry_type' => PageBlockElementType::class,
            'entry_options' => ['label' => false],
        ]);
        $builder->add('newOrder', HiddenType::class, [
            'mapped' => false,
            'data' => implode(',',$options['orderedPageBlockElementsArray']),
            'attr' => ['class' => 'new-order']
        ]);
        $builder
            // ->add('menuItems')
            ->add('submit', SubmitType::class, ['label' => 'backend.form.reorganizeBlocksInPage.submit']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'orderedPageBlockElementsArray' => [],
        ]);
    }
}
