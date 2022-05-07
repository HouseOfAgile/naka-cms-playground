<?php

namespace HouseOfAgile\NakaCMSBundle\Form;

use App\NakaData\GlobalDataParameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusinessRoleType extends AbstractType
{
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('choices', GlobalDataParameter::getBusinessRoles());
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function getName()
    {
        return 'business_role';
    }
}
