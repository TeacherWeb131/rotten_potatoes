<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom",
                'attr' => [
                    'placeholder' => "Votre nom !"
                ]
            ])
            ->add('avatar', TextType::class, [
                'label' => "Avatar",
                'attr' => [
                    'placeholder' => "Votre avatar !"
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Adresse Email",
                'attr' => [
                    'placeholder' => "Votre adresse email !"
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => "Mot de passe",
                'attr' => [
                    'placeholder' => "Votre mot de passe !"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
