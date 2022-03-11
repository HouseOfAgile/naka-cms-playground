<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                // 'delete_label' => '...',
                // 'download_label' => '...',
                // 'download_uri' => true,
                // 'image_uri' => true,
                // 'imagine_pattern' => '...',
                // 'asset_helper' => true,
            ])
            // ->add('pageGallery')
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
