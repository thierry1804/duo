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
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Article::class,

                'choice_label' => 'label',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
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
