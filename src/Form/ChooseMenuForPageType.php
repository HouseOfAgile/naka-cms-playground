<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\Menu;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseMenuForPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        // $builder->add('name', TextType::class);
        $builder->add('menu', EntityType::class, [
            'class' => Menu::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.id', 'ASC');
            },
            'choice_label' => function ($type) {
                return $type->getName();
            },
            'label' => 'backend.form.chooseMenuForPage.menu',
        ]);
        if ($options['add_submit']) {
            $builder->add('submit', SubmitType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'add_submit' => false,
        ]);
    }
}
