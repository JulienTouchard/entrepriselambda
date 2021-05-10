<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',HiddenType::class,['mapped'=>false])
            ->add('roles',HiddenType::class,['mapped'=>false])
            ->add('password')
            ->add('firstName')
            ->add('lastName')
            ->add('company',EntityType::class,[
                'class'=> Company::class,
                'choice_label'=>'name',
                'choice_name'=>ChoiceList::fieldName($this,'id'),
                'choice_value'=>ChoiceList::value($this,'id'),
                'multiple'=> false,
                'expanded'=>false
            ])
            ->add('createdAt',HiddenType::class,['mapped'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
