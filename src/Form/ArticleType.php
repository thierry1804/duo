<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image')
            ->add('label')
            ->add('couleur')
            ->add('taille')
            ->add('pointure')
            ->add('minCmd')
            ->add('minCmdUnit')
            ->add('poids')
            ->add('longueur')
            ->add('largeur')
            ->add('hauteur')
            ->add('disponible')
            ->add('createdAt')
            ->add('editedAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
