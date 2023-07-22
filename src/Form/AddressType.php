<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AddressType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('province', TextType::class, [
                'label' => 'Province',
            ])
            ->add('district', TextType::class, [
                'label' => 'District',
            ])
            ->add('ward', TextType::class, [
                'label' => 'Ward',
            ])
            ->add('street', TextType::class, [
                'label' => 'Street',
            ]);

        $currentUser = $this->security->getUser();
        $builder->add('user', EntityType::class, [
            'label' => 'User',
            'class' => 'App\Entity\User',
            'choice_label' => 'username',
            'data' => $currentUser,
            'disabled' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}

