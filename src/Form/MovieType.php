<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use App\Entity\People;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('slug')
            ->add('title', TextType::class, [
                'label' => 'Titre'])
            ->add('poster', TextType::class,[
                'label' => 'Poster'])
            ->add('synopsis', TextType::class,[
                'label' => 'Synopsis'])
            ->add('releasedAt', DateType::class,[
                'label' => 'Réalisé le',
                'format' => 'dd-MM-yyyy'])
            ->add('categories', EntityType::class, [
                'label' => 'Catégories',
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => 'true',
                'expanded' => 'true'
            ])
            ->add('actors', EntityType::class, [
                'label' => 'Acteurs',
                'class' => People::class,
                'choice_label' => function(People $people){
                    return $people->getFirstName(). " ". $people->getLastName();
                },
                'multiple' => 'false'
            ])
            ->add('director', EntityType::class, [
                'label' => 'Réalisateurs',
                'class' => People::class,
                'choice_label' => 'fullName'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
