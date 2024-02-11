<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,

                'choice_label' => 'label',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
            ])
            ->add('label', null, [
                'label' => 'Nom',
            ])
            ->add('couleur', null, [
                'label' => 'Couleur',
            ])
            ->add('taille', null, [
                'label' => 'Taille',
            ])
            ->add('pointure', NumberType::class, [
                'label' => 'Pointure',
            ])
            ->add('minCmd', NumberType::class, [
                'label' => 'Quantité minimum de commande',
            ])
            ->add('minCmdUnit', ChoiceType::class, [
                'label' => 'Unité de commande',
                'choices' => [
                    'pièce' => 'pièce',
                    'kg' => 'kg',
                    'mètre' => 'mètre',
                    'litre' => 'litre',
                    'carton' => 'carton',
                ],
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Poids',
            ])
            ->add('longueur', NumberType::class, [
                'label' => 'Longueur',
            ])
            ->add('largeur', NumberType::class, [
                'label' => 'Largeur',
            ])
            ->add('hauteur', NumberType::class, [
                'label' => 'Hauteur',
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
