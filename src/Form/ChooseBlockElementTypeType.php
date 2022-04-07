<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\BlockElementType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChooseBlockElementTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('name', TextType::class);
        $builder->add('blockElementType', EntityType::class, [
            'class' => BlockElementType::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->orderBy('u.id', 'ASC');
            },
            'choice_label' => function ($type) {
                return $type->getName();
            },
            'label' => 'backend.form.chooseBlockElementType.blockElementType',
            'help' => 'backend.form.chooseBlockElementType.blockElementType.help',
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
