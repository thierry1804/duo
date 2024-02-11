<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
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
                'class' => Category::class,
                'choice_label' => 'label',
                'label' => 'Catégorie',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.label', 'ASC');
                },
                'attr' => [
                    'class' => 'form-select mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control mt-2',
                ],
                'label_attr' => [
                    'class' => 'd-none'
                ],
            ])
            ->add('label', null, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('couleur', null, [
                'label' => 'Couleur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('taille', null, [
                'label' => 'Taille',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('pointure', NumberType::class, [
                'label' => 'Pointure',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('minCmd', NumberType::class, [
                'label' => 'Quantité minimum de commande',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('minCmdUnit', ChoiceType::class, [
                'label' => 'Unité de commande',
                'required' => false,
                'choices' => [
                    'pièce' => 'pièce',
                    'kg' => 'kg',
                    'mètre' => 'mètre',
                    'litre' => 'litre',
                    'carton' => 'carton',
                ],
                'attr' => [
                    'class' => 'form-select mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Poids',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('longueur', NumberType::class, [
                'label' => 'Longueur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('largeur', NumberType::class, [
                'label' => 'Largeur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('hauteur', NumberType::class, [
                'label' => 'Hauteur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-2',
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input mb-2 ms-2',
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
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
