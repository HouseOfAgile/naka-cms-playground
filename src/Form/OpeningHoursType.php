<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpeningHoursType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add('openingHours', TextareaType::class, [
            'data' => $options['openinHoursData'],
            'attr' => ['class' => 'openinghours-container'],
            'help' => 'form.editOpeningHours.help'
        ]);
        $builder->add('submit', SubmitType::class, [
            'label' => 'form.openingHours.updateOpeningHours',
            'attr' =>['class'=>'btn btn-success d-flex justify-content-center p-4']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'openinHoursData' => '',
            // Configure your form options here
        ]);
    }
}
